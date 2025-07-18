<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Ambulance;
use App\Models\VehicleMovement;
use Auth;
use DB;
use Session;
use Redirect;
use PDF;
use Mail;
use Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomExport;
use App\Models\AmbulanceAllocation;
use App\Models\AmbulanceRequest;
use App\Models\HomeHealthRequest;
use App\Models\TransportRequest;
use App\Models\Vehicle;
use App\Models\TransportAllocation;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application .
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        ini_set('memory_limit', '512M');
        $title = 'Dashboard';

        if (Auth::check()) {

            $user = Auth::user();
            $auth = Auth()->user();
            
            $drivers = Employee::where('type', 'DRIVER')->orderBy('id')->get();
            $vehicles = Vehicle::get();
            $employeeArray = Employee::pluck('name', 'id');

            $vehicleMovements = VehicleMovement::query();
            $cancelledMovements = VehicleMovement::withTrashed()->where('status', 'cancel');
            $manualMovements = VehicleMovement::where('status', 'end')
                ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
                ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
                ->select(
                    'vehicle_movements.*',
                    'vehicles.reg_no as vehicle_name',
                    'employees.name as driver_name'
                )
                ->whereNull('vehicle_movements.allocation_id');

            $vehicleFuels = null;
            $vehicleJobs = null;

            if( $request->vehicle_id) {
                $vehicleMovements->where('vehicle_movements.vehicle_id', $request->vehicle_id);
                $cancelledMovements->where('vehicle_movements.vehicle_id', $request->vehicle_id);
                $manualMovements->where('vehicle_movements.vehicle_id', $request->vehicle_id);
            }

            if( $request->driver_id) {
                $vehicleMovements->where('vehicle_movements.driver_id', $request->driver_id);
                $cancelledMovements->where('vehicle_movements.driver_id', $request->driver_id);
                $manualMovements->where('vehicle_movements.driver_id', $request->driver_id);
            }

            $vehicleFuels = DB::table('vehicle_fuels')
                ->leftJoin('vehicles', 'vehicle_fuels.vehicle_id', '=', 'vehicles.id')
                ->where('vehicle_fuels.vehicle_id', $request->vehicle_id);

            $vehicleJobs = DB::table('vehicle_jobs')
                ->leftJoin('vehicles', 'vehicle_jobs.vehicle_id', '=', 'vehicles.id')
                ->where('vehicle_jobs.vehicle_id', $request->vehicle_id);

            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            if ($fromDate && $toDate) {
                $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
                $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

                $vehicleMovements->whereBetween('date', [$fromDate, $toDate]);
                $cancelledMovements->whereBetween('date', [$fromDate, $toDate]);
                $manualMovements->whereBetween('date', [$fromDate, $toDate]);
                
                if ($vehicleFuels) $vehicleFuels->whereBetween('date', [$fromDate, $toDate]);
                if ($vehicleJobs) $vehicleJobs->whereBetween('date', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();

                $vehicleMovements->whereDate('date', "=", $fromDate);
                $cancelledMovements->whereDate('date', "=", $fromDate);
                $manualMovements->whereDate('date', "=", $fromDate);
                if ($vehicleFuels) $vehicleFuels->whereDate('date', "=", $fromDate);
                if ($vehicleJobs) $vehicleJobs->whereDate('date', "=", $fromDate);
            } elseif ($toDate) {
                $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

                $vehicleMovements->whereDate('date', '<=', $toDate);
                $cancelledMovements->whereDate('date', '<=', $toDate);
                $manualMovements->whereDate('date', '<=', $toDate);
                if ($vehicleFuels) $vehicleFuels->whereDate('date', '<=', $toDate);
                if ($vehicleJobs) $vehicleJobs->whereDate('date', '<=', $toDate);
            } else {
                $vehicleMovements->whereDate('date', '>=', Carbon::today());
                $cancelledMovements->whereDate('date', '>=', Carbon::today());
                $manualMovements->whereDate('date', '>=', Carbon::today());
                if ($vehicleFuels) $vehicleFuels->whereDate('date', '>=', Carbon::today());
                if ($vehicleJobs) $vehicleJobs->whereDate('date', '>=', Carbon::today());
            }

            $ambulanceRequests = AmbulanceRequest::whereNull('allot_type')->get()->each(function ($item) {
                $item->request_type = 'Ambulance';
                $item->request_date = $item->booking_date ?? $item->created_at;
            });

            $transportRequests = TransportRequest::whereNull('allot_type')->get()->each(function ($item) {
                $item->request_type = 'Transport';
                $item->request_date = $item->booking_date ?? $item->created_at;
            });

            $homehealthRequests = HomeHealthRequest::where('status', 'Pending')->get()->each(function ($item) {
                $item->request_type = 'HomeHealth';
                $item->request_date = $item->booking_date ?? $item->created_at;
            });

            // Step 3: Merge All Requests
            $mergedRequests = $ambulanceRequests
                ->merge($transportRequests)
                ->merge($homehealthRequests);

            // Step 4: Filter by Date
            $filteredRequests = $mergedRequests->filter(function ($item) use ($fromDate, $toDate) {
                $date = Carbon::parse($item->request_date);

                if ($fromDate && $toDate) {
                    return $date->between($fromDate, $toDate);
                } elseif ($fromDate) {
                    return $date->isSameDay($fromDate);
                } elseif ($toDate) {
                    return $date->lte($toDate);
                } else {
                    return $date->gte(Carbon::today());
                }
            });           

            $requestMovements = (clone $vehicleMovements)->where('status', 'allot');
            $runningMovements = (clone $vehicleMovements)->where('status', 'start');
            $completedMovements = (clone $vehicleMovements)->where('status', 'end');

            $totalHours = 0;
            $duration = '00:00';

            if ($completedMovements->count() > 0) {
                $completedMovementData = (clone $completedMovements)->get();
                foreach ($completedMovementData as $vehicleMovement) {
                    if ($vehicleMovement->time_in && $vehicleMovement->time_out) {
                        $start = \Carbon\Carbon::createFromFormat('H:i', substr($vehicleMovement->time_out, 0, 5));
                        $end = \Carbon\Carbon::createFromFormat('H:i', substr($vehicleMovement->time_in, 0, 5));
                        
                        $diffInMinutes = $end->diffInMinutes($start);
                        $totalHours += $diffInMinutes;
                    }
                }
                $duration = \Carbon\CarbonInterval::minutes($totalHours)->cascade()->format('%H:%I');
            } else {
                $duration = '00:00';
            }

            $vehicleSummary = [
                'totalTrips' => (clone $completedMovements)->count(),
                'totalKms' => (clone $completedMovements)->sum('km_covered'),
                'totalHours' => $duration,
                'totalFuel' => $vehicleFuels ? (clone $vehicleFuels)->sum('fuel_qty') : 0,
                'totalCancel' => $cancelledMovements->get()->count(),
                'totalService' => $vehicleJobs ? (clone $vehicleJobs)->sum('bill_amount') : 0,
            ];

            $pendingMovements = VehicleMovement::query()
                ->where('status', 'allot')
                ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
                ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
                ->select(
                    'vehicle_movements.*',
                    'vehicles.reg_no as vehicle_name',
                    'employees.name as driver_name',
                    // Calculate delay in days using DATEDIFF
                    DB::raw("DATEDIFF(CURRENT_DATE(), vehicle_movements.date) as delay_days")
                )
                ->having('delay_days', '>=', 1)
                ->latest()
                ->paginate(10);
                
            $todaysMovements = (clone $vehicleMovements)
                ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
                ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
                ->select(
                    'vehicle_movements.*',
                    'vehicles.reg_no as vehicle_name',
                    'employees.name as driver_name'
                )
                ->latest()
                ->paginate(25);        
        
            $requestMovements = $requestMovements
                ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
                ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
                ->select(
                    'vehicle_movements.*',
                    'vehicles.reg_no as vehicle_name',
                    'employees.name as driver_name'
                )
                ->latest()
                ->paginate(10);

            $runningMovements = $runningMovements->latest()->paginate(10);
            $completedMovements = $completedMovements->latest()->paginate(10);
            $cancelledMovements = $cancelledMovements->latest()->paginate(10);
            $manualMovements = $manualMovements->latest()->paginate(10);
        
            // dd($mergedRequests);
            return view('back.dashboard', compact('title', 'request', 'user', 'drivers', 'vehicles', 'vehicleSummary', 'requestMovements', 'runningMovements', 'completedMovements', 'pendingMovements', 'cancelledMovements', 'manualMovements', 'todaysMovements', 'filteredRequests'));
        }
    }

    public function profile(Request $request, $id = null)
    {
        if (Auth::check()) {

            $title = 'Profile';

            $auth = Auth::user();

            return view('back.profile', compact('title', 'auth'));

            //    return redirect()->route(@$request->route,['id'=>@$data->CampusID]);
        }

        return redirect("login")->withSuccess('Oops! You do not have access');
    }

    public function settings()
    {
        $title = 'Settings';

        return view('back.settings', compact('title'));
    }

    public function support()
    {
        $title = 'Support';

        return view('back.support', compact('title'));
    }

    // Module Management
    public function modules()
    {
        $this->checkpermission('settings');

        $title = 'Modules';

        $modules = DB::table('modules')->orderBy('hierarchy')->Paginate(25);
        $roles = DB::table('roles')->orderBy('created_at')->get();
        $role_modules = DB::table('role_modules')->get();

        return view('back.modules', compact('title', 'modules', 'roles', 'role_modules'));
    }

    public function storeModule(Request $request)
    {
        // dd($request->all());
        $view = $request->view ?? 0;

        if($request->id !== null){
            DB::table('modules')->where('id', $request->id)->update(['name' => $request->name, 'slug' => Str::slug($request->name), 'url' => $request->url, 'icon' => $request->icon, 'hierarchy' => $request->hierarchy, 'view' => $view, 'updated_at' => Carbon::now()]);
            $moduleId = $request->id;
        } else {
            $moduleId = DB::table('modules')->insertGetId(['name' => $request->name, 'slug' => Str::slug($request->name), 'url' => $request->url, 'icon' => $request->icon, 'hierarchy' => $request->hierarchy, 'view' => $view, 'created_at' => Carbon::now()]);
        }

        if($moduleId)
        {
            DB::table('role_modules')->where('module_id', $moduleId)->delete();

            if (!empty($request->role)) {
                $roleData = [];
                foreach ($request->role as $roleId) {
                    $roleData[] = [
                        'role_id'    => $roleId,
                        'module_id'  => $moduleId,
                        'created_at' => Carbon::now(),
                    ];
                }
                DB::table('role_modules')->insert($roleData);
            }
            Session::flash("success", "Module updated successfully");
            return Redirect::back();
        }
    }

    // Role Management

    public function roles()
    {
        $this->checkpermission('settings');

        $title = 'Roles';

        $roles = DB::table('roles')->orderBy('created_at')->Paginate(10);

        return view('back.roles', compact('title', 'roles'));
    }

    public function storeRole(Request $request)
    {
        // dd($request->all());
        if ($request->view) {
            $view = $request->view;
        } else {
            $view = 0;
        }

        if ($request->id !== null) {
            $roleData = DB::table('roles')->where('id', $request->id)->update(['name' => $request->name, 'slug' => Str::slug($request->name), 'updated_at' => Carbon::now()]);
        } else {
            $roleData = DB::table('roles')->insert(['name' => $request->name, 'slug' => Str::slug($request->name), 'created_at' => Carbon::now()]);
        }

        if ($roleData) {
            Session::flash("success", "Role updated successfully");
            return Redirect::back();
        }
    }

    public function employees()
    {
        $this->checkpermission('employees');

        $title = 'Employees';

        $employees = User::where('register_by', '!=', 'ADMIN')->orderBy('id')->Paginate(25);
        $roles = DB::table('roles')->orderBy('created_at')->get();
        $user_roles = DB::table('user_roles')->get();
        // $entities = DB::table('Entities')->get();

        return view('back.employees', compact('title', 'employees', 'roles', 'user_roles'));
    }

    public function filteremployees(Request $request)
    {
        $title = 'Employees';

        if (@$request->register_by && @$request->verified !== null) {
            $employees = User::where('register_by', '!=', 'ADMIN')->where('register_by', @$request->register_by)->where('verified', @$request->verified)->where('username', 'like', "%" . @$request->username . "%")->orderBy('id')->Paginate(50);
        } else if (@$request->register_by) {
            $employees = User::where('register_by', '!=', 'ADMIN')->where('register_by', @$request->register_by)->where('username', 'like', "%" . @$request->username . "%")->orderBy('id')->Paginate(50);
        } else if (@$request->verified !== null) {
            $employees = User::where('register_by', '!=', 'ADMIN')->where('verified', @$request->verified)->where('username', 'like', "%" . @$request->username . "%")->orderBy('id')->Paginate(50);
        } else {
            $employees = User::where('register_by', '!=', 'ADMIN')->where('username', 'like', "%" . @$request->username . "%")->orderBy('id')->Paginate(50);
        }
        $roles = DB::table('roles')->orderBy('created_at')->get();
        $user_roles = DB::table('user_roles')->get();
        // $entities = DB::table('Entities')->get();

        return view('back.employees', compact('title', 'employees', 'request', 'roles', 'user_roles'));
    }

    public function userVerify($id = null)
    {
        $data = User::where('id', $id)->update(['verified' => '1']);

        if ($data) {
            $user = User::where('id', $id)->first();

            // Mail::send('email.emailVerified', ['user' => $user], function($message) use($user){
            //     $message->to($user->email);
            //     $message->subject('Account Verified');
            // });

            Session::flash("success", "User Verified successfully");
            // return redirect()->route('employees');
            return Redirect::back();
        }
    }

    public function postResetPassword(Request $request)
    {
        // dd($request->all());

        if ($request->username && $request->user_type) {

            $user = User::where('username', $request->username)->where('register_by', $request->user_type)->where('verified', true)->first();
            if (!$user) {
                return redirect()->back()->withError('Invalid Password Reset Link');
            }
            if ($request->password === $request->password_confirm) {
                $user->password = Hash::make($request->password);
                $user->confirmation_code = null;
                $user->save();

                Session::flash('success', 'Password Reset!');
                return redirect()->back();
            } else {
                return redirect()->back()->withError('New Password and Confirm Password don\'t match');
            }
        } else {
            return redirect()->back()->withError('Invalid Password Reset Link');
        }
    }

    public function updateUser(Request $request)
    {
        // dd($request->all());

        $user_roles = DB::table('user_roles')->where('user_id', '=', $request->id)->get();
        foreach ($user_roles as $rm) {
            DB::table('user_roles')->where('role_id', $rm->role_id)->where('user_id', $rm->user_id)->delete();
        }
        foreach ($request->role as $key => $id) {
            $role = DB::table('roles')->find($id);
            $userRoles = DB::table('user_roles')->insert(['role_id' => $role->id, 'user_id' => $request->id, 'created_at' => Carbon::now()]);
        }

        return redirect()->back()->withSuccess('User Role Assigned Successfully.');
    }

    public function adminRunQuery()
    {
        $auth = Auth()->user();
        $title = 'Run Query';
        if ($auth->register_by == 'ADMIN') {
            return view('back.query', compact('title'));
        }
    }

    public function submitAdminRunQuery(Request $request)
    {
        $auth = Auth()->user();
        if ($auth->register_by == 'ADMIN') {
            $form_query = $request->form_query;
            $form_query = str_ireplace("delete", "", $form_query);
            $form_query = str_ireplace("update", "", $form_query);

            // $query_result = DB::statement($form_query);
            $query_result = DB::select(DB::raw($form_query));

            dd($query_result);
        }
    }

    public function reports(Request $request)
    {
        ini_set('memory_limit', '512M');
        $title = "Reports";

        $user = Auth::user();

        $drivers = Employee::where('type', 'DRIVER')->orderBy('id')->get();
        $vehicles = Vehicle::get();
        $employeeArray = Employee::pluck('name', 'id');

        $vehicleMovements = VehicleMovement::query();
        $cancelledMovements = VehicleMovement::withTrashed()->where('status', 'cancel');
        $manualMovements = VehicleMovement::where('status', 'end')
            ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_movements.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name'
            )
            ->whereNull('vehicle_movements.allocation_id');

        $vehicleFuels = null;
        $vehicleJobs = null;

        if( $request->vehicle_id) {
            $vehicleMovements->where('vehicle_movements.vehicle_id', $request->vehicle_id);
            $cancelledMovements->where('vehicle_movements.vehicle_id', $request->vehicle_id);
            $manualMovements->where('vehicle_movements.vehicle_id', $request->vehicle_id);
        }

        if( $request->driver_id) {
            $vehicleMovements->where('vehicle_movements.driver_id', $request->driver_id);
            $cancelledMovements->where('vehicle_movements.driver_id', $request->driver_id);
            $manualMovements->where('vehicle_movements.driver_id', $request->driver_id);
        }

        $vehicleFuels = DB::table('vehicle_fuels')
            ->leftJoin('vehicles', 'vehicle_fuels.vehicle_id', '=', 'vehicles.id')
            ->where('vehicle_fuels.vehicle_id', $request->vehicle_id);

        $vehicleJobs = DB::table('vehicle_jobs')
            ->leftJoin('vehicles', 'vehicle_jobs.vehicle_id', '=', 'vehicles.id')
            ->where('vehicle_jobs.vehicle_id', $request->vehicle_id);

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleMovements->whereBetween('date', [$fromDate, $toDate]);
            $cancelledMovements->whereBetween('date', [$fromDate, $toDate]);
            $manualMovements->whereBetween('date', [$fromDate, $toDate]);
            
            if ($vehicleFuels) $vehicleFuels->whereBetween('date', [$fromDate, $toDate]);
            if ($vehicleJobs) $vehicleJobs->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();

            $vehicleMovements->whereDate('date', "=", $fromDate);
            $cancelledMovements->whereDate('date', "=", $fromDate);
            $manualMovements->whereDate('date', "=", $fromDate);
            if ($vehicleFuels) $vehicleFuels->whereDate('date', "=", $fromDate);
            if ($vehicleJobs) $vehicleJobs->whereDate('date', "=", $fromDate);
        } elseif ($toDate) {
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleMovements->whereDate('date', '<=', $toDate);
            $cancelledMovements->whereDate('date', '<=', $toDate);
            $manualMovements->whereDate('date', '<=', $toDate);
            if ($vehicleFuels) $vehicleFuels->whereDate('date', '<=', $toDate);
            if ($vehicleJobs) $vehicleJobs->whereDate('date', '<=', $toDate);
        }

        $requestMovements = (clone $vehicleMovements)->where('status', 'allot');
        $runningMovements = (clone $vehicleMovements)->where('status', 'start');
        $completedMovements = (clone $vehicleMovements)->where('status', 'end');

        $totalHours = 0;
        $duration = '00:00';

        if ($completedMovements->count() > 0) {
            $completedMovementData = (clone $completedMovements)->get();
            foreach ($completedMovementData as $vehicleMovement) {
                if ($vehicleMovement->time_in && $vehicleMovement->time_out) {
                    $start = \Carbon\Carbon::createFromFormat('H:i', substr($vehicleMovement->time_out, 0, 5));
                    $end = \Carbon\Carbon::createFromFormat('H:i', substr($vehicleMovement->time_in, 0, 5));
                    
                    $diffInMinutes = $end->diffInMinutes($start);
                    $totalHours += $diffInMinutes;
                }
            }
            $duration = \Carbon\CarbonInterval::minutes($totalHours)->cascade()->format('%H:%I');
        } else {
            $duration = '00:00';
        }

        $vehicleSummary = [
            'totalTrips' => (clone $completedMovements)->count(),
            'totalKms' => (clone $completedMovements)->sum('km_covered'),
            'totalHours' => $duration,
            'totalFuel' => $vehicleFuels ? (clone $vehicleFuels)->sum('fuel_qty') : 0,
            'totalCancel' => $cancelledMovements->get()->count(),
            'totalService' => $vehicleJobs ? (clone $vehicleJobs)->sum('bill_amount') : 0,
        ];

        return view('back.reports', compact('title', 'vehicles', 'drivers', 'vehicleSummary'));
    }

    public function reportAmbulance(Request $request)
    {
        $title = "Ambulance Report";

        $ambulanceData = AmbulanceAllocation::query()
        ->join('ambulance_requests', 'ambulance_requests.id', '=', 'ambulance_allocations.ambulance_request_id');

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();
            $ambulanceData = $ambulanceData->whereBetween('booking_date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $ambulanceData = $ambulanceData->whereDate('booking_date', '>=', $fromDate);
        } elseif ($toDate) {
            $ambulanceData = $ambulanceData->whereDate('booking_date', '<=', $toDate);
        }

        //dd($ambulanceData->get());

        $ambulanceDetails = (clone $ambulanceData)
        ->leftJoin('vehicle_movements', 'vehicle_movements.allocation_id', '=', 'ambulance_allocations.id')
        ->whereIn('vehicle_movements.type', ['ward', 'help-desk'])
        ->where('vehicle_movements.status', 'end');
        // ->whereNotNull('ambulance_allocations.ambulance_arranged');

        $ambulanceHelpDeskDetails =(clone $ambulanceDetails)
        ->where('ambulance_requests.type', 'help-desk')
        ->select(
            'ambulance_allocations.id',
            'ambulance_allocations.ambulance_arranged as ambulance_type',
            'ambulance_requests.booking_date',
            'ambulance_requests.destination',
        )
        ->orderBy('ambulance_allocations.ambulance_arranged')
        ->orderByDesc('ambulance_requests.created_at')
        ->get();

        $ambulanceHelpDeskGrouped = $ambulanceHelpDeskDetails->groupBy('ambulance_type');

        $ambulanceHelpDeskChartData = (clone $ambulanceDetails)
            ->where('ambulance_requests.type', 'help-desk')
            ->select('ambulance_allocations.ambulance_arranged as ambulance_type', DB::raw('count(*) as data_count'))
            ->groupBy('ambulance_allocations.ambulance_arranged')
            ->orderByDesc('data_count')
            ->limit(10)
            ->pluck('data_count', 'ambulance_type')
            ->toArray();       

        $ambulanceWardDetails = (clone $ambulanceDetails)
        ->where('ambulance_requests.ward', 'Ward')
        ->select(
            'ambulance_allocations.id',
            'ambulance_allocations.ambulance_arranged as ambulance_type',
            'ambulance_requests.booking_date',
            'ambulance_requests.destination',
        )
        ->orderBy('ambulance_allocations.ambulance_arranged')
        ->orderByDesc('ambulance_requests.created_at')
        ->get();

        $ambulanceWardGrouped = $ambulanceWardDetails->groupBy('ambulance_type');

        $ambulanceWardChartData = (clone $ambulanceDetails)
            ->where('ambulance_requests.ward', 'Ward')
            ->select('ambulance_allocations.ambulance_arranged as ambulance_type', DB::raw('count(*) as data_count'))
            ->groupBy('ambulance_allocations.ambulance_arranged')
            ->orderByDesc('data_count')
            ->limit(10)
            ->pluck('data_count', 'ambulance_type')
            ->toArray();

        $ambulanceICUDetails = (clone $ambulanceDetails)
        ->where('ambulance_requests.ward', 'ICU')
        ->select(
            'ambulance_allocations.id',
            'ambulance_allocations.ambulance_arranged as ambulance_type',
            'ambulance_requests.booking_date',
            'ambulance_requests.destination',
        )
        ->orderBy('ambulance_allocations.ambulance_arranged')
        ->orderByDesc('ambulance_requests.created_at')
        ->get();

        $ambulanceICUGrouped = $ambulanceICUDetails->groupBy('ambulance_type');

        $ambulanceICUChartData = (clone $ambulanceDetails)
            ->where('ambulance_requests.ward', 'ICU')
            ->select('ambulance_allocations.ambulance_arranged as ambulance_type', DB::raw('count(*) as data_count'))
            ->groupBy('ambulance_allocations.ambulance_arranged')
            ->orderByDesc('data_count')
            ->limit(10)
            ->pluck('data_count', 'ambulance_type')
            ->toArray();
        
         $ambulanceIntChartData = (clone $ambulanceDetails)
            ->select('ambulance_allocations.ambulance_arranged as ambulance_type', DB::raw('count(*) as data_count'))
            ->groupBy('ambulance_allocations.ambulance_arranged')
            ->orderByDesc('data_count')
            ->limit(10)
            ->pluck('data_count', 'ambulance_type')
            ->toArray();

        $ambulanceTypeDetails = (clone $ambulanceData)
        ->select(
            'ambulance_allocations.id',
            'ambulance_requests.ward as ambulance_type',
            'ambulance_requests.booking_date',
            'ambulance_requests.destination',
        )
        ->orderBy('ambulance_requests.ward')
        ->orderByDesc('ambulance_requests.created_at')
        ->get();

        $ambulanceTypeGrouped = $ambulanceTypeDetails->groupBy('ambulance_type');

        $ambulanceTypeChartData = (clone $ambulanceData)
        ->select(DB::raw("
                CASE 
                    WHEN ambulance_requests.ward IS NULL OR ambulance_requests.ward = '' THEN 'Helpdesk'
                    ELSE ambulance_requests.ward
                END as ambulance_type
            "), DB::raw('count(*) as data_count'))
            ->groupBy('ambulance_requests.ward')
            ->orderByDesc('data_count')
            ->limit(10)
            ->pluck('data_count', 'ambulance_type')
            ->toArray();  
            
            // dd($ambulanceTypeChartData);
        
        $ambulanceExtDetails =(clone $ambulanceData)
        ->whereNotNull('ambulance_allocations.tied_up_ambulance')
        ->select(
            'ambulance_allocations.id',
            'ambulance_allocations.tied_up_ambulance as ambulance_type',
            'ambulance_requests.booking_date',
            'ambulance_requests.destination',
        )
        ->orderBy('ambulance_allocations.tied_up_ambulance')
        ->orderByDesc('ambulance_requests.created_at')
        ->get();

        $ambulanceExtGrouped = $ambulanceExtDetails->groupBy('ambulance_type');

        $ambulanceExtChartData = (clone $ambulanceData)
            ->whereNotNull('ambulance_allocations.tied_up_ambulance')
            ->select('ambulance_allocations.tied_up_ambulance as ambulance_type', DB::raw('count(*) as data_count'))
            ->groupBy('ambulance_allocations.tied_up_ambulance')
            ->orderByDesc('data_count')
            ->limit(10)
            ->pluck('data_count', 'ambulance_type')
            ->toArray();
       
            // dd($ambulanceExtChartData);
        return view('back.report-ambulance', compact('title', 'ambulanceHelpDeskChartData', 'ambulanceExtChartData', 'ambulanceTypeChartData', 'ambulanceWardChartData', 'ambulanceICUChartData', 'ambulanceIntChartData'));
    }

    public function reportVehicle(Request $request)
    {
        ini_set('memory_limit', '512M');
        $title = 'Vehicle Report';

        $user = Auth::user();
        $auth = Auth()->user();
        
        $drivers = Employee::where('type', 'DRIVER')->orderBy('id')->get();
        $vehicles = Vehicle::get();
        $employeeArray = Employee::pluck('name', 'id');

        $vehicleMovements = VehicleMovement::where('status', 'end');
        $vehicleFuels = DB::table('vehicle_fuels');
        $vehicleJobs = DB::table('vehicle_jobs');

        $vehicleMovements = $vehicleMovements
            ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_movements.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name'
            );

        $vehicleFuels = $vehicleFuels
            ->leftJoin('vehicles', 'vehicle_fuels.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_fuels.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_fuels.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name'
            );

        $vehicleJobs = $vehicleJobs
            ->leftJoin('vehicles', 'vehicle_jobs.vehicle_id', '=', 'vehicles.id')
            ->select(
                'vehicle_jobs.*',
                'vehicles.reg_no as vehicle_name'
            );

        $cancelledMovements = VehicleMovement::withTrashed()
        ->where('status', 'cancel')
        ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
        ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
        ->select(
            'vehicle_movements.*',
            'vehicles.reg_no as vehicle_name',
            'employees.name as driver_name',
        );

        $vehicleManualMovements = VehicleMovement::where('vehicle_movements.status', 'end')
        ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
        ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
        ->select(
            'vehicle_movements.*',
            'vehicles.reg_no as vehicle_name',
            'employees.name as driver_name'
        )
        ->whereNull('vehicle_movements.allocation_id');

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleMovements->whereBetween('date', [$fromDate, $toDate]);
            $cancelledMovements->whereBetween('date', [$fromDate, $toDate]);
            $vehicleManualMovements->whereBetween('date', [$fromDate, $toDate]);
            
            if ($vehicleFuels) $vehicleFuels->whereBetween('date', [$fromDate, $toDate]);
            if ($vehicleJobs) $vehicleJobs->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();

            $vehicleMovements->whereDate('date', "=", $fromDate);
            $cancelledMovements->whereDate('date', "=", $fromDate);
            $vehicleManualMovements->whereDate('date', "=", $fromDate);
            if ($vehicleFuels) $vehicleFuels->whereDate('date', "=", $fromDate);
            if ($vehicleJobs) $vehicleJobs->whereDate('date', "=", $fromDate);
        } elseif ($toDate) {
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleMovements->whereDate('date', '<=', $toDate);
            $cancelledMovements->whereDate('date', '<=', $toDate);
            $vehicleManualMovements->whereDate('date', '<=', $toDate);
            if ($vehicleFuels) $vehicleFuels->whereDate('date', '<=', $toDate);
            if ($vehicleJobs) $vehicleJobs->whereDate('date', '<=', $toDate);
        }

        $tripWiseReport = (clone $vehicleMovements)
            ->select('vehicles.reg_no as vehicle_name', DB::raw('count(*) as trip_count'))
            ->whereNotNull('vehicles.reg_no')
            ->groupBy('vehicles.reg_no')
            ->orderByDesc('trip_count')
            ->limit(10)
            ->pluck('trip_count', 'vehicle_name')
            ->toArray();

        $kmCoveredReport = (clone $vehicleMovements)
            ->select('vehicles.reg_no as vehicle_name', DB::raw('SUM(vehicle_movements.km_covered) as total_km'))
            ->whereNotNull('vehicles.reg_no')
            ->groupBy('vehicles.reg_no')
            ->havingRaw('SUM(vehicle_movements.km_covered) > 0')
            ->orderByDesc('total_km')
            ->limit(10)
            ->pluck('total_km', 'vehicle_name')
            ->toArray();

        $travelTimeReport = [];

        if ($vehicleMovements->count() > 0) {
            $vehicleMove = (clone $vehicleMovements)
                // ->join('vehicles', 'vehicles.id', '=', 'vehicle_movements.vehicle_id') // ensure vehicles table is joined
                ->select('vehicles.reg_no as vehicle_name', 'vehicle_movements.time_in', 'vehicle_movements.time_out')
                ->whereNotNull('vehicles.reg_no')
                ->get();

            $grouped = $vehicleMove->groupBy('vehicle_name');

            foreach ($grouped as $vehicleName => $movements) {
                $totalMinutes = 0;

                foreach ($movements as $movement) {
                    if ($movement->time_in && $movement->time_out) {
                        $start = \Carbon\Carbon::createFromFormat('H:i', substr($movement->time_out, 0, 5));
                        $end = \Carbon\Carbon::createFromFormat('H:i', substr($movement->time_in, 0, 5));
                        $diff = $end->diffInMinutes($start);
                        $totalMinutes += $diff;
                    }
                }

                $totalHoursFormatted = round($totalMinutes / 60, 2); // to match previous format
                $travelTimeReport[$vehicleName] = $totalHoursFormatted;
            }

            // Optional: Sort descending and take top 10
            arsort($travelTimeReport);
            $travelTimeReport = array_slice($travelTimeReport, 0, 10, true);
        }

        $fuelWiseReport = (clone $vehicleFuels)
            ->select('vehicles.reg_no as vehicle_name', DB::raw('SUM(vehicle_fuels.fuel_qty) as total_fuel'))
            ->whereNotNull('vehicles.reg_no')
            ->groupBy('vehicles.reg_no')
            ->orderByDesc('total_fuel')
            ->limit(10)
            ->pluck('total_fuel', 'vehicle_name')
            ->toArray();

        $jobWiseReport = (clone $vehicleJobs)
            ->select('vehicles.reg_no as vehicle_name', DB::raw('SUM(vehicle_jobs.bill_amount) as total_bill'))
            ->where('vehicle_jobs.approve', '1')
            ->whereNotNull('vehicles.reg_no')
            ->groupBy('vehicles.reg_no')
            ->orderByDesc('total_bill')
            ->limit(10)
            ->pluck('total_bill', 'vehicle_name')
            ->toArray();

        $totalHours = 0;
        $duration = '00:00';

        if ($vehicleMovements->count() > 0) {
            $vehicleMovements = (clone $vehicleMovements)->get();
            foreach ($vehicleMovements as $vehicleMovement) {
                if ($vehicleMovement->time_in && $vehicleMovement->time_out) {
                    $start = \Carbon\Carbon::createFromFormat('H:i', substr($vehicleMovement->time_out, 0, 5));
                    $end = \Carbon\Carbon::createFromFormat('H:i', substr($vehicleMovement->time_in, 0, 5));
                    
                    $diffInMinutes = $end->diffInMinutes($start);
                    $totalHours += $diffInMinutes;
                }
            }
            $duration = \Carbon\CarbonInterval::minutes($totalHours)->cascade()->format('%H:%I');
        } else {
            $duration = '00:00';
        }

        $vehicleSummary = [
            'totalTrips' => (clone $vehicleMovements)->count(),
            'totalTransport' => $vehicleMovements ? (clone $vehicleMovements)->where('type', 'transport')->count() : 0,
            'totalHomeHealth' => $vehicleMovements ? (clone $vehicleMovements)->where('type', 'home-health')->count() : 0,
            'totalAmbulance' => $vehicleMovements ? (clone $vehicleMovements)->whereIn('type', ['ward', 'help-desk'])->count() : 0,
            'totalManual' => (clone $vehicleManualMovements)->count(),            
            'totalKms' => (clone $vehicleMovements)->sum('km_covered'),
            'totalHours' => $duration,
            'totalCancel' => (clone $cancelledMovements)->get()->count(),
            'totalFuel' => $vehicleFuels ? (clone $vehicleFuels)->sum('fuel_qty') : 0,
            'totalService' => $vehicleJobs ? (clone $vehicleJobs)->where('approve', '1')->sum('bill_amount') : 0,
        ];

        $pendingMovements = VehicleMovement::query()
        ->where('status', 'allot')
        ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
        ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
        ->select(
            'vehicle_movements.*',
            'vehicles.reg_no as vehicle_name',
            'employees.name as driver_name',
            DB::raw("DATEDIFF(CURRENT_DATE(), vehicle_movements.date) as delay_days")
        )
        ->having('delay_days', '>=', 1)
        ->paginate(10);

        $cancelledMovements = $cancelledMovements->paginate(10);

        $pendingJobs = DB::table('vehicle_jobs')
        ->where('accept', '1')
        ->where(function ($query) {
            $query->where('approve', '!=', '1')
                ->orWhereNull('approve');
        })
        ->leftJoin('vehicles', 'vehicle_jobs.vehicle_id', '=', 'vehicles.id')
        ->select(
            'vehicle_jobs.*',
            'vehicles.reg_no as vehicle_name'
        )
        ->paginate(10);

        $vehicleManualMovements = $vehicleManualMovements
        ->latest()->paginate(10);

        //dd($pendingJobs);
    
        return view('back.report-vehicle', compact('title', 'vehicleSummary', 'cancelledMovements', 'pendingJobs', 'kmCoveredReport', 'tripWiseReport', 'travelTimeReport', 'fuelWiseReport', 'jobWiseReport'));
    }

    public function reportDriver(Request $request)
    {
        ini_set('memory_limit', '512M');
        $title = 'Driver Report';

        $user = Auth::user();
        $auth = Auth()->user();
        
        $drivers = Employee::where('type', 'DRIVER')->orderBy('id')->get();
        $vehicles = Vehicle::get();
        $employeeArray = Employee::pluck('name', 'id');

        $vehicleMovements = VehicleMovement::where('status', 'end');
        $vehicleFuels = DB::table('vehicle_fuels');

        $vehicleMovements = $vehicleMovements
            ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_movements.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name'
            );

        $vehicleFuels = $vehicleFuels
            ->leftJoin('vehicles', 'vehicle_fuels.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_fuels.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_fuels.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name'
            );

        $cancelledMovements = VehicleMovement::withTrashed()
        ->where('status', 'cancel')
        ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
        ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
        ->select(
            'vehicle_movements.*',
            'vehicles.reg_no as vehicle_name',
            'employees.name as driver_name',
        );

        $vehicleManualMovements = VehicleMovement::where('status', 'end')
        ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
        ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
        ->select(
            'vehicle_movements.*',
            'vehicles.reg_no as vehicle_name',
            'employees.name as driver_name'
        )
        ->whereNull('vehicle_movements.allocation_id');

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleMovements->whereBetween('date', [$fromDate, $toDate]);
            $cancelledMovements->whereBetween('date', [$fromDate, $toDate]);
            $vehicleManualMovements->whereBetween('date', [$fromDate, $toDate]);
            
            if ($vehicleFuels) $vehicleFuels->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();

            $vehicleMovements->whereDate('date', "=", $fromDate);
            $cancelledMovements->whereDate('date', "=", $fromDate);
            $vehicleManualMovements->whereDate('date', "=", $fromDate);
            if ($vehicleFuels) $vehicleFuels->whereDate('date', "=", $fromDate);
        } elseif ($toDate) {
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleMovements->whereDate('date', '<=', $toDate);
            $cancelledMovements->whereDate('date', '<=', $toDate);
            $vehicleManualMovements->whereDate('date', '<=', $toDate);
            if ($vehicleFuels) $vehicleFuels->whereDate('date', '<=', $toDate);
        }

        $tripWiseReport = (clone $vehicleMovements)
            ->select('employees.name as driver_name', DB::raw('count(*) as trip_count'))
            ->whereNotNull('employees.name')
            ->groupBy('employees.name')
            ->orderByDesc('trip_count')
            ->limit(10)
            ->pluck('trip_count', 'driver_name')
            ->toArray();

        $kmCoveredReport = (clone $vehicleMovements)
            ->select('employees.name as driver_name', DB::raw('SUM(vehicle_movements.km_covered) as total_km'))
            ->whereNotNull('employees.name')
            ->groupBy('employees.name')
            ->havingRaw('SUM(vehicle_movements.km_covered) > 0')
            ->orderByDesc('total_km')
            ->limit(10)
            ->pluck('total_km', 'driver_name')
            ->toArray();

        $travelTimeReport = [];

        if ($vehicleMovements->count() > 0) {
            $vehicleMove = (clone $vehicleMovements)
                // ->join('vehicles', 'vehicles.id', '=', 'vehicle_movements.vehicle_id') // ensure vehicles table is joined
                ->select('employees.name as driver_name', 'vehicle_movements.time_in', 'vehicle_movements.time_out')
                ->whereNotNull('employees.name')
                ->get();

            $grouped = $vehicleMove->groupBy('driver_name');

            foreach ($grouped as $vehicleName => $movements) {
                $totalMinutes = 0;

                foreach ($movements as $movement) {
                    if ($movement->time_in && $movement->time_out) {
                        $start = \Carbon\Carbon::createFromFormat('H:i', substr($movement->time_out, 0, 5));
                        $end = \Carbon\Carbon::createFromFormat('H:i', substr($movement->time_in, 0, 5));
                        $diff = $end->diffInMinutes($start);
                        $totalMinutes += $diff;
                    }
                }

                $totalHoursFormatted = round($totalMinutes / 60, 2); // to match previous format
                $travelTimeReport[$vehicleName] = $totalHoursFormatted;
            }

            // Optional: Sort descending and take top 10
            arsort($travelTimeReport);
            $travelTimeReport = array_slice($travelTimeReport, 0, 10, true);
        }

        $fuelWiseReport = (clone $vehicleFuels)
            ->select('employees.name as driver_name', DB::raw('SUM(vehicle_fuels.fuel_qty) as total_fuel'))
            ->whereNotNull('employees.name')
            ->groupBy('employees.name')
            ->orderByDesc('total_fuel')
            ->limit(10)
            ->pluck('total_fuel', 'driver_name')
            ->toArray();

        $totalHours = 0;
        $duration = '00:00';

        if ($vehicleMovements->count() > 0) {
            $vehicleMovements = (clone $vehicleMovements)->get();
            foreach ($vehicleMovements as $vehicleMovement) {
                if ($vehicleMovement->time_in && $vehicleMovement->time_out) {
                    $start = \Carbon\Carbon::createFromFormat('H:i', substr($vehicleMovement->time_out, 0, 5));
                    $end = \Carbon\Carbon::createFromFormat('H:i', substr($vehicleMovement->time_in, 0, 5));
                    
                    $diffInMinutes = $end->diffInMinutes($start);
                    $totalHours += $diffInMinutes;
                }
            }
            $duration = \Carbon\CarbonInterval::minutes($totalHours)->cascade()->format('%H:%I');
        } else {
            $duration = '00:00';
        }

        $vehicleSummary = [
            'totalTrips' => (clone $vehicleMovements)->count(),
            'totalKms' => (clone $vehicleMovements)->sum('km_covered'),
            'totalHours' => $duration,
            'totalCancel' => (clone $cancelledMovements)->get()->count(),
            'totalFuel' => $vehicleFuels ? (clone $vehicleFuels)->sum('fuel_qty') : 0
        ];

        $pendingMovements = VehicleMovement::query()
        ->where('status', 'allot')
        ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
        ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
        ->select(
            'vehicle_movements.*',
            'vehicles.reg_no as vehicle_name',
            'employees.name as driver_name',
            DB::raw("DATEDIFF(CURRENT_DATE(), vehicle_movements.date) as delay_days")
        )
        ->having('delay_days', '>=', 1)
        ->paginate(10);

        $cancelledMovements = $cancelledMovements->paginate(10);

        $pendingJobs = DB::table('vehicle_jobs')
        ->where('accept', '1')
        ->where(function ($query) {
            $query->where('approve', '!=', '1')
                ->orWhereNull('approve');
        })
        ->leftJoin('vehicles', 'vehicle_jobs.vehicle_id', '=', 'vehicles.id')
        ->select(
            'vehicle_jobs.*',
            'vehicles.reg_no as vehicle_name'
        )
        ->paginate(10);

        $vehicleManualMovements = $vehicleManualMovements
        ->latest()->paginate(10);

        //dd($pendingJobs);
    
        return view('back.report-driver', compact('title', 'vehicleSummary', 'cancelledMovements', 'pendingJobs', 'kmCoveredReport', 'tripWiseReport', 'travelTimeReport', 'fuelWiseReport'));
    }

    public function reportDepartment(Request $request)
    {
        ini_set('memory_limit', '512M');
        $title = 'Department Report';

        $user = Auth::user();
        $auth = Auth()->user();
        
        $drivers = Employee::where('type', 'DRIVER')->orderBy('id')->get();
        $vehicles = Vehicle::get();
        $employeeArray = Employee::pluck('name', 'id');

        $vehicleMovements = VehicleMovement::where('type', 'transport')->where('status', 'end');

        // $vehicleMovements = $vehicleMovements
        //     ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
        //     ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
        //     ->select(
        //         'vehicle_movements.*',
        //         'vehicles.reg_no as vehicle_name',
        //         'employees.name as department'
        //     );

        $cancelledMovements = VehicleMovement::withTrashed()
        ->where('status', 'cancel')
        ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
        ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
        ->select(
            'vehicle_movements.*',
            'vehicles.reg_no as vehicle_name',
            'employees.name as department',
        );

        $vehicleManualMovements = VehicleMovement::where('status', 'end')
        ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
        ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
        ->select(
            'vehicle_movements.*',
            'vehicles.reg_no as vehicle_name',
            'employees.name as department'
        )
        ->whereNull('vehicle_movements.allocation_id');

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleMovements->whereBetween('date', [$fromDate, $toDate]);
            $cancelledMovements->whereBetween('date', [$fromDate, $toDate]);
            $vehicleManualMovements->whereBetween('date', [$fromDate, $toDate]);

        } elseif ($fromDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();

            $vehicleMovements->whereDate('date', "=", $fromDate);
            $cancelledMovements->whereDate('date', "=", $fromDate);
            $vehicleManualMovements->whereDate('date', "=", $fromDate);
        } elseif ($toDate) {
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleMovements->whereDate('date', '<=', $toDate);
            $cancelledMovements->whereDate('date', '<=', $toDate);
            $vehicleManualMovements->whereDate('date', '<=', $toDate);
        }

        $tripWiseReport = (clone $vehicleMovements)
            ->select('department', DB::raw('count(*) as trip_count'))
            ->whereNotNull('department')
            ->groupBy('department')
            ->orderByDesc('trip_count')
            ->limit(10)
            ->pluck('trip_count', 'department')
            ->toArray();

        $kmCoveredReport = (clone $vehicleMovements)
            ->select('department', DB::raw('SUM(vehicle_movements.km_covered) as total_km'))
            ->whereNotNull('department')
            ->groupBy('department')
            ->havingRaw('SUM(vehicle_movements.km_covered) > 0')
            ->orderByDesc('total_km')
            ->limit(10)
            ->pluck('total_km', 'department')
            ->toArray();

        $travelTimeReport = [];

        if ($vehicleMovements->count() > 0) {
            $vehicleMove = (clone $vehicleMovements)
                // ->join('vehicles', 'vehicles.id', '=', 'vehicle_movements.vehicle_id') // ensure vehicles table is joined
                ->select('department', 'vehicle_movements.time_in', 'vehicle_movements.time_out')
                ->whereNotNull('department')
                ->get();

            $grouped = $vehicleMove->groupBy('department');

            foreach ($grouped as $vehicleName => $movements) {
                $totalMinutes = 0;

                foreach ($movements as $movement) {
                    if ($movement->time_in && $movement->time_out) {
                        $start = \Carbon\Carbon::createFromFormat('H:i', substr($movement->time_out, 0, 5));
                        $end = \Carbon\Carbon::createFromFormat('H:i', substr($movement->time_in, 0, 5));
                        $diff = $end->diffInMinutes($start);
                        $totalMinutes += $diff;
                    }
                }

                $totalHoursFormatted = round($totalMinutes / 60, 2); // to match previous format
                $travelTimeReport[$vehicleName] = $totalHoursFormatted;
            }

            // Optional: Sort descending and take top 10
            arsort($travelTimeReport);
            $travelTimeReport = array_slice($travelTimeReport, 0, 10, true);
        }

        $totalHours = 0;
        $duration = '00:00';

        if ($vehicleMovements->count() > 0) {
            $vehicleMovements = (clone $vehicleMovements)->get();
            foreach ($vehicleMovements as $vehicleMovement) {
                if ($vehicleMovement->time_in && $vehicleMovement->time_out) {
                    $start = \Carbon\Carbon::createFromFormat('H:i', substr($vehicleMovement->time_out, 0, 5));
                    $end = \Carbon\Carbon::createFromFormat('H:i', substr($vehicleMovement->time_in, 0, 5));
                    
                    $diffInMinutes = $end->diffInMinutes($start);
                    $totalHours += $diffInMinutes;
                }
            }
            $duration = \Carbon\CarbonInterval::minutes($totalHours)->cascade()->format('%H:%I');
        } else {
            $duration = '00:00';
        }

        $vehicleSummary = [
            'totalTrips' => (clone $vehicleMovements)->count(),
            'totalKms' => (clone $vehicleMovements)->sum('km_covered'),
            'totalHours' => $duration,
            'totalCancel' => (clone $cancelledMovements)->get()->count(),
        ];

        $cancelledMovements = $cancelledMovements->paginate(10);

        $pendingJobs = DB::table('vehicle_jobs')
        ->where('accept', '1')
        ->where(function ($query) {
            $query->where('approve', '!=', '1')
                ->orWhereNull('approve');
        })
        ->leftJoin('vehicles', 'vehicle_jobs.vehicle_id', '=', 'vehicles.id')
        ->select(
            'vehicle_jobs.*',
            'vehicles.reg_no as vehicle_name'
        )
        ->paginate(10);

        $vehicleManualMovements = $vehicleManualMovements
        ->latest()->paginate(10);

        //dd($pendingJobs);
    
        return view('back.report-department', compact('title', 'vehicleSummary', 'cancelledMovements', 'pendingJobs', 'kmCoveredReport', 'tripWiseReport', 'travelTimeReport'));
    }

    public function accessReport(Request $request)
    {
        $title = "Access Report";
        $patients = Patient::query();
        if ($request->source)
            $patients->where('source', $request->source);
        $patients = $patients->get();

        $patientCounts = Patient::select('source', \DB::raw('count(*) as patient_count'))
            ->when($request->source, function ($query) use ($request) {
                return $query->where('source', $request->source);
            })
            ->groupBy('source')
            ->get();

        if ($request->submit == 'export')
            return Excel::download(new CustomExport($patients), 'access_report.xlsx');
        return view('back.access-report', compact('title', 'patients', 'patientCounts'));
    }

    public function hmisReport(Request $request)
    {
        $title = "HMIS Report";
        $patients = Patient::query();
        if ($request->source) $patients->where('source', $request->source);
        if ($request->district_city) $patients->where('district_city', $request->district_city);
        if ($request->doctor) $patients->where('doctor', $request->doctor);
        if ($request->ref_by_hospital) $patients->where('ref_by_hospital', $request->ref_by_hospital);

        $sourceWisePatients = (clone $patients)->select('source', \DB::raw('count(*) as patient_count'))
            ->groupBy('source')
            ->get();
        $areaWisePatients = (clone $patients)->select('district_city', \DB::raw('count(*) as patient_count'))
            ->groupBy('district_city')
            ->get();
        $doctorWisePatients = (clone $patients)->select('doctor', \DB::raw('count(*) as patient_count'))
            ->groupBy('doctor')
            ->get();
        $hospitalWisePatients = (clone $patients)->select('ref_by_hospital', \DB::raw('count(*) as patient_count'))
            ->groupBy('ref_by_hospital')
            ->get();
        $patients = $patients->get();

        if ($request->submit == 'export')
            return Excel::download(new CustomExport($patients), 'hmis_report.xlsx');

        return view('back.hmis-report', compact('title', 'patients', 'sourceWisePatients', 'areaWisePatients', 'doctorWisePatients', 'hospitalWisePatients'));
    }

    public function marketingReport(Request $request)
    {
        $title = "Marketing Report";
        $patients = Patient::where('patient_type', 'Marketing');
        if ($request->employee_id) $patients->where('employee_id', $request->employee_id);
        if ($request->source) $patients->where('source', $request->source);
        if ($request->district_city) $patients->where('district_city', $request->district_city);
        if ($request->doctor) $patients->where('doctor', $request->doctor);
        if ($request->ref_by_hospital) $patients->where('ref_by_hospital', $request->ref_by_hospital);

        $employeeWisePatients = (clone $patients)->select('employees.name as employee_name', \DB::raw('count(patients.id) as patient_count'))
            ->join('employees', 'patients.employee_id', '=', 'employees.id')
            ->groupBy('employees.name')
            ->get();
        $sourceWisePatients = (clone $patients)->select('source', \DB::raw('count(*) as patient_count'))
            ->groupBy('source')
            ->get();
        $areaWisePatients = (clone $patients)->select('district_city', \DB::raw('count(*) as patient_count'))
            ->groupBy('district_city')
            ->get();
        $doctorWisePatients = (clone $patients)->select('doctor', \DB::raw('count(*) as patient_count'))
            ->groupBy('doctor')
            ->get();
        $hospitalWisePatients = (clone $patients)->select('ref_by_hospital', \DB::raw('count(*) as patient_count'))
            ->groupBy('ref_by_hospital')
            ->get();
        $patients = $patients->get();
        if ($request->submit == 'export')
            return Excel::download(new CustomExport($patients), 'marketing_report.xlsx');
        $employees = Employee::get();
        return view('back.marketing-report', compact('title', 'patients', 'employeeWisePatients', 'sourceWisePatients', 'areaWisePatients', 'doctorWisePatients', 'hospitalWisePatients', 'employees'));
    }

    public function performanceReport(Request $request)
    {
        $title = "Performance Report";
        $patients = Patient::get();
        $doctors = Doctor::get();
        $ambulance = Ambulance::get();
        $employee = $request->employee_id;
        $area = $request->district_city;
        //$patients = collect();
        //$doctors = collect();
        //$ambulance = collect();
        $patientsArea = collect();
        $doctorsArea = collect();
        $ambulanceArea = collect();
        if ($request->employee_id) {
            $patients = Patient::where('employee_id', $request->employee_id)->get();
            $doctors = Doctor::where('employee_id', $request->employee_id)->get();
            $ambulance = Ambulance::where('employee_id', $request->employee_id)->get();
        }
        if ($request->district_city) {
            $patientsArea = Patient::where('district_city', $request->district_city)->get();
            $doctorsArea = Doctor::where('district', $request->district_city)->get();
            $ambulanceArea = Ambulance::where('district', $request->district_city)->get();
        }
        $employeeWisePatients = Patient::select('employees.name as employee_name', \DB::raw('count(patients.id) as patient_count'))
            ->join('employees', 'patients.employee_id', '=', 'employees.id')
            ->groupBy('employees.name')
            ->get();
        $areaWisePatients = Patient::select('district_city', \DB::raw('count(*) as patient_count'))
            ->groupBy('district_city')
            ->get();

        if ($request->submit == 'export')
            return Excel::download(new CustomExport($patientsArea->isNotEmpty() ? $patientsArea : $patients), 'performance_report.xlsx');

        $employees = Employee::get();
        return view('back.performance-report', compact('title', 'patients', 'employeeWisePatients', 'areaWisePatients', 'patients', 'doctors', 'ambulance', 'employees', 'employee', 'area', 'patientsArea', 'doctorsArea', 'ambulanceArea'));
    }

    // Format employee activity data
    public function formatEmployeeActivityData($activities)
    {
        $employeeActivityData = [];

        // Loop through each activity
        foreach ($activities as $activity) {
            $employeeIndex = $activity->employee_id - 1; // Zero-indexed
            $monthIndex = $activity->month - 1; // Zero-indexed month (1 -> 0, 2 -> 1, etc.)

            // Ensure the employeeActivityData array has enough space
            if (!isset($employeeActivityData[$employeeIndex])) {
                $employeeActivityData[$employeeIndex] = array_fill(0, 12, 0); // Ensure 12 months
            }

            // Assign the activity count for the respective employee and month
            $employeeActivityData[$employeeIndex][$monthIndex] = $activity->total_activity;
        }

        return $employeeActivityData;
    }

    public function prodReport(Request $request)
    {
        $title = 'Productivity Report';
        ini_set("memory_limit", -1);
        $employees = Employee::get();
        $doctors = Doctor::get();
        $ambulances = Ambulance::get();

        $patients = Patient::where('patient_type', '!=', 'Ambulance');

        if ($request->type) $patients->where('type', $request->type);
        if ($request->patient_type) $patients->where('patient_type', $request->patient_type);
        if ($request->year) $patients->whereYear('reg_date', $request->year);
        if ($request->employee_id) $patients->where('employee_id', $request->employee_id);

        $employeeWiseReport = (clone $patients)
            ->join('employees', 'patients.employee_id', 'employees.id')
            ->select('employees.name as employee_name', DB::raw('count(*) as patient_count'))
            ->where('patients.patient_type', 'Marketing')
            ->groupBy('employees.name')
            ->orderByDesc('patient_count')
            ->orderBy('employees.id')
            ->limit(10)
            ->pluck('patient_count', 'employee_name')
            ->toArray();

        $patients = (clone $patients)->get();

        $visitData = DB::table('employee_visits');
        ($request->year) ? $visitData->whereYear('date', $request->year) : $visitData->whereYear('date', Carbon::now()->year);
        if ($request->employee_id) $visitData->where('employee_id', $request->employee_id);

        $activityData = DB::table('employee_activities');
        ($request->year) ? $activityData->whereYear('date', $request->year) : $activityData->whereYear('date', Carbon::now()->year);
        if ($request->employee_id) $activityData->where('employee_id', $request->employee_id);

        $employeeVisitReport = (clone $visitData)
            ->join('employees', 'employee_visits.employee_id', '=', 'employees.id')
            ->select('employees.name as employee_name', DB::raw('count(*) as visit_count'))
            ->groupBy('employees.name')
            ->orderByDesc('visit_count')
            ->orderBy('employees.id')
            ->limit(10)
            ->pluck('visit_count', 'employee_name')
            ->toArray();

        $employeeActivityReport = (clone $activityData)
            ->join('employees', 'employee_activities.employee_id', '=', 'employees.id')
            ->select('employees.name as employee_name', DB::raw('count(*) as activity_count'))
            ->groupBy('employees.name')
            ->orderByDesc('activity_count')
            ->orderBy('employees.id')
            ->limit(10)
            ->pluck('activity_count', 'employee_name')
            ->toArray();

        $activities = (clone $activityData)
            ->select(
                'employee_id',
                DB::raw('MONTH(date) as month'),
                DB::raw('COUNT(*) as total_activity')
            )
            ->groupBy('employee_id', 'month')
            ->orderBy('employee_id')
            ->orderBy('month')
            ->get();

        $visits = (clone $visitData)
            ->select(
                'employee_id',
                DB::raw('MONTH(date) as month'),
                DB::raw('COUNT(*) as total_activity')
            )
            ->groupBy('employee_id', 'month')
            ->orderBy('employee_id')
            ->orderBy('month')
            ->get();

        $employeeActivityData = $this->formatEmployeeActivityData($activities);
        $employeeVisitData = $this->formatEmployeeActivityData($visits);

        $visitData = $visitData->get();
        //dd($visitData);
        return view('back.prod-report', compact('title', 'patients', 'employees', 'doctors', 'ambulances', 'employeeWiseReport', 'employeeActivityData', 'employeeVisitReport', 'employeeActivityReport', 'employeeVisitData', 'visitData'));
    }

    public function patientReport(Request $request)
    {
        //dd($request->type);
        ini_set("memory_limit", -1);
        $title = 'HMIS & Marketing Report';

        $employees = Employee::get();
        $patients = Patient::where('patient_type', '!=', 'Ambulance');
        $departments = $patients->whereNotNull('department')
            ->pluck('department')
            ->unique()
            ->map(function ($department) {
                return Str::title(strtolower($department));
            })
            ->toArray();

        if ($request->type) $patients->where('type', $request->type);
        if ($request->patient_type) $patients->where('patient_type', $request->patient_type);
        if ($request->year) $patients->whereYear('reg_date', $request->year);
        if ($request->employee_id) $patients->where('employee_id', $request->employee_id);
        if ($request->department) $patients->where('department', $request->department);

        $hmisPatientCounts = [(clone $patients)->where('patient_type', '!=', 'Marketing')->count()];

        $marketingPatientCounts = (clone $patients)
            ->join('employees', 'patients.employee_id', 'employees.id')
            ->select('employees.name as employee_name', DB::raw('count(*) as patient_count'))
            ->where('patients.patient_type', 'Marketing')
            ->groupBy('employees.name')
            ->orderByDesc('patient_count')
            ->limit(10)
            ->pluck('patient_count', 'employee_name')
            ->toArray();

        $sourceWiseReport = (clone $patients)
            ->select('patients.source', DB::raw('count(*) as patient_count'))
            ->groupBy('patients.source')
            ->orderByDesc('patient_count')
            ->limit(10)
            ->pluck('patient_count', 'source')
            ->toArray();

        $ageWiseReport = (clone $patients)
            ->select(
                DB::raw('CASE 
                        WHEN age BETWEEN 0 AND 18 THEN "0-18"
                        WHEN age BETWEEN 19 AND 30 THEN "19-30"
                        WHEN age BETWEEN 31 AND 40 THEN "31-40"
                        WHEN age BETWEEN 41 AND 50 THEN "41-50"
                        WHEN age BETWEEN 51 AND 60 THEN "51-60"
                        ELSE "61+" 
                    END AS age_range'),
                DB::raw('COUNT(*) as patient_count')
            )
            ->groupBy('age_range')
            ->orderBy('age_range')
            ->pluck('patient_count', 'age_range')
            ->toArray();

        $doctorWiseReport = (clone $patients)
            ->select('ref_doctor as doctor_name', DB::raw('count(*) as patient_count'))
            ->whereNull('employee_id')
            ->whereNotNull('ref_doctor')
            ->groupBy('ref_doctor')
            ->orderByDesc('patient_count')
            ->limit(10)
            ->pluck('patient_count', 'doctor_name')
            ->toArray();

        $hospitalWiseReport = (clone $patients)
            ->select(
                DB::raw('CONCAT(UPPER(SUBSTRING(ref_by_hospital, 1, 1)), LOWER(SUBSTRING(ref_by_hospital, 2))) AS hospital_name'),
                DB::raw('count(*) as patient_count')
            )
            ->whereNull('employee_id')
            ->whereNotNull('ref_by_hospital')
            ->groupBy('ref_by_hospital')
            ->orderByDesc('patient_count')
            ->limit(10)
            ->pluck('patient_count', 'hospital_name')
            ->toArray();

        $areaWiseReport = (clone $patients)
            ->select(
                DB::raw('CONCAT(UPPER(SUBSTRING(district_city, 1, 1)), LOWER(SUBSTRING(district_city, 2))) AS area_name'),
                DB::raw('count(*) as patient_count')
            )
            ->whereNotNull('district_city')
            ->groupBy('district_city')
            ->orderByDesc('patient_count')
            ->limit(10)
            ->pluck('patient_count', 'area_name')
            ->toArray();

        $patients = (clone $patients)->get();

        //dd($doctorWiseReport);
        return view('back.patient-report', compact('title', 'employees', 'departments', 'patients', 'hmisPatientCounts', 'marketingPatientCounts', 'sourceWiseReport', 'ageWiseReport', 'doctorWiseReport', 'hospitalWiseReport', 'areaWiseReport'));
    }

    public function ambulanceReport(Request $request)
    {
        $title = 'Ambulance Report';
        ini_set("memory_limit", -1);
        $employees = Employee::get();
        $patients = Patient::where('patient_type', 'Ambulance');
        $departments = $patients->whereNotNull('department')
            ->pluck('department')
            ->unique()
            ->map(function ($department) {
                return Str::title(strtolower($department));
            })
            ->toArray();

        if ($request->type) $patients->where('type', $request->type);
        if ($request->ambulance_type) $patients->where('ambulance_type', $request->ambulance_type);
        if ($request->year) $patients->whereYear('reg_date', $request->year);
        if ($request->employee_id) $patients->where('employee_id', $request->employee_id);
        if ($request->department) $patients->where('department', $request->department);

        $employeeWiseReport = (clone $patients)
            ->join('employees', 'patients.employee_id', 'employees.id')
            ->select('employees.name as employee_name', DB::raw('count(*) as patient_count'))
            ->groupBy('employees.name')
            ->orderByDesc('patient_count')
            ->limit(10)
            ->pluck('patient_count', 'employee_name')
            ->toArray();

        $typeWiseReport = (clone $patients)
            ->select('ambulance_type', DB::raw('count(*) as patient_count'))
            ->whereNotNull('ambulance_type')
            ->groupBy('ambulance_type')
            ->orderByDesc('patient_count')
            ->limit(10)
            ->pluck('patient_count', 'ambulance_type')
            ->toArray();

        $paymentWiseReport = (clone $patients)
            ->select('payment', DB::raw('count(*) as patient_count'))
            ->whereNotNull('payment')
            ->groupBy('payment')
            ->orderByDesc('patient_count')
            ->limit(10)
            ->pluck('patient_count', 'payment')
            ->toArray();

        $ambulanceWiseReport = (clone $patients)
            ->select('ambulance_name', DB::raw('count(*) as patient_count'))
            ->whereNotNull('ambulance_name')
            ->groupBy('ambulance_name')
            ->orderByDesc('patient_count')
            ->limit(10)
            ->pluck('patient_count', 'ambulance_name')
            ->toArray();

        $areaWiseReport = (clone $patients)
            ->select('area', DB::raw('count(*) as patient_count'))
            ->whereNotNull('area')
            ->groupBy('area')
            ->orderByDesc('patient_count')
            ->limit(10)
            ->pluck('patient_count', 'area')
            ->toArray();

        $patients = (clone $patients)->get();

        return view('back.ambulance_report', compact('title', 'employees', 'departments', 'patients', 'employeeWiseReport', 'typeWiseReport', 'paymentWiseReport', 'ambulanceWiseReport', 'areaWiseReport'));
    }

    public function getAmbulanceData(Request $request, $type = null)
    {
        //return $request->all();
        $ambulanceData = AmbulanceAllocation::query()
        ->join('ambulance_requests', 'ambulance_requests.id', '=', 'ambulance_allocations.ambulance_request_id');

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();
            $ambulanceData = $ambulanceData->whereBetween('booking_date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $ambulanceData = $ambulanceData->whereDate('booking_date', '>=', $fromDate);
        } elseif ($toDate) {
            $ambulanceData = $ambulanceData->whereDate('booking_date', '<=', $toDate);
        }

        if ($type == 'External') {
            $title = "External Ambulance Data  - $request->label";

            $ambulanceData = $ambulanceData
            ->whereNotNull('ambulance_allocations.tied_up_ambulance')
            ->where('ambulance_allocations.tied_up_ambulance', $request->label)
            ->orderByDesc('ambulance_requests.created_at');
            
        } else if($type == 'Internal') {
            $title = "Internal Ambulance Data - $request->label";

            $ambulanceData = $ambulanceData
            ->leftJoin('vehicle_movements', 'vehicle_movements.allocation_id', '=', 'ambulance_allocations.id')
            ->whereIn('vehicle_movements.type', ['ward', 'help-desk'])
            ->where('vehicle_movements.status', 'end')
            ->whereNull('ambulance_allocations.tied_up_ambulance')
            ->where('ambulance_allocations.ambulance_arranged', $request->label)
            ->orderByDesc('ambulance_requests.created_at');
            
        } else {
            $title = "Ambulance Request Data - $request->label";

            $label = $request->label == 'Helpdesk' ? Null : $request->label;
            $ambulanceData = AmbulanceAllocation::query()
            // ->join('ambulance_allocations', 'ambulance_allocations.id', '=', 'vehicle_movements.allocation_id')
            ->join('ambulance_requests', 'ambulance_requests.id', '=', 'ambulance_allocations.ambulance_request_id')
            ->where('ambulance_requests.ward', $label)
            ->orderByDesc('ambulance_requests.created_at');            
        }

        $ambulanceDetails = $ambulanceData->select('booking_date', 'patient_name', 'ambulance_requests.destination', 'reason')->get();
        
        return response()->json([
            'title' => $title,
            'tableData' => $ambulanceDetails
        ]);
    }

    public function getVehicleData(Request $request, $type = null)
    {
        //return $request->all();
        $vehicleData = VehicleMovement::where('status', 'end');
        $vehicleFuels = DB::table('vehicle_fuels');
        $vehicleJobs = DB::table('vehicle_jobs');

        $vehicleData = $vehicleData
            ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_movements.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name'
            );

        $vehicleFuels = $vehicleFuels
            ->leftJoin('vehicles', 'vehicle_fuels.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_fuels.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_fuels.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name'
            );

        $vehicleJobs = $vehicleJobs
            ->leftJoin('vehicles', 'vehicle_jobs.vehicle_id', '=', 'vehicles.id')
            ->select(
                'vehicle_jobs.*',
                'vehicles.reg_no as vehicle_name'
            );

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleData->whereBetween('date', [$fromDate, $toDate]);            
            if ($vehicleFuels) $vehicleFuels->whereBetween('date', [$fromDate, $toDate]);
            if ($vehicleJobs) $vehicleJobs->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();

            $vehicleData->whereDate('date', "=", $fromDate);
            if ($vehicleFuels) $vehicleFuels->whereDate('date', "=", $fromDate);
            if ($vehicleJobs) $vehicleJobs->whereDate('date', "=", $fromDate);
        } elseif ($toDate) {
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleData->whereDate('date', '<=', $toDate);
            if ($vehicleFuels) $vehicleFuels->whereDate('date', '<=', $toDate);
            if ($vehicleJobs) $vehicleJobs->whereDate('date', '<=', $toDate);
        }        

        if ($type == 'Litres') {

            $title = "Vehicle Fuel Data - $request->label";
            $vehicleDetails = $vehicleFuels->where('vehicles.reg_no', $request->label)
            ->select('date', 'reg_no', 'fuel_qty', 'mileage')
            ->orderByDesc('vehicle_fuels.created_at')->get();
            
        } else if($type == 'INR') {

            $title = "Job Card Data  - $request->label";
            $vehicleDetails = $vehicleJobs->where('vehicles.reg_no', $request->label)
            ->select('date', 'reg_no', 'service_type', 'service_desc', 'service_center', 'bill_desc', 'bill_amount')
            ->orderByDesc('vehicle_jobs.created_at')->get();
            
        } else {
            $title = "Vehicle Data - $request->label";

            $vehicleDetails = $vehicleData
            ->where('vehicles.reg_no', $request->label)
            ->select('date', 'place', 'purpose', 'km_covered', 'travel_time')
            ->orderByDesc('vehicle_movements.created_at')->get();
        }
        
        return response()->json([
            'title' => $title,
            'tableData' => $vehicleDetails
        ]);
    }

    public function getDriverData(Request $request, $type = null)
    {
        //return $request->all();
        $vehicleData = VehicleMovement::where('status', 'end');

        $vehicleData = $vehicleData
            ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_movements.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name'
            );

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleData->whereBetween('date', [$fromDate, $toDate]);            
        } elseif ($fromDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();

            $vehicleData->whereDate('date', "=", $fromDate);
        } elseif ($toDate) {
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleData->whereDate('date', '<=', $toDate);
        }        

        if ($type == 'Litres') {

        } else {
			$title = "Driver Data - $request->label";

            $vehicleDetails = $vehicleData
            ->where('employees.name', $request->label)
            ->select('date', 'place', 'purpose', 'km_covered', 'travel_time')
            ->orderByDesc('vehicle_movements.created_at')->get();
        }
        
        return response()->json([
            'title' => $title,
            'tableData' => $vehicleDetails
        ]);
    }

    public function getDeptData(Request $request, $type = null)
    {
        //return $request->all();
        $vehicleData = VehicleMovement::where('status', 'end');

        $vehicleData = $vehicleData
            ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_movements.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name'
            );

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleData->whereBetween('date', [$fromDate, $toDate]);            
        } elseif ($fromDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();

            $vehicleData->whereDate('date', "=", $fromDate);
        } elseif ($toDate) {
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();

            $vehicleData->whereDate('date', '<=', $toDate);
        }        

        if ($type == 'Litres') {
            
        } else {
            $title = "$request->label Department Data";

            $vehicleDetails = $vehicleData
            ->where('department', $request->label)
            ->select('date', 'place', 'purpose', 'km_covered', 'travel_time')
            ->orderByDesc('vehicle_movements.created_at')->get();
        }
        
        return response()->json([
            'title' => $title,
            'tableData' => $vehicleDetails
        ]);
    }
   

    public function users()
    {
        $this->checkpermission('users');

        $title = 'Users';

        $users = User::where('register_by', '!=', 'ADMIN')->orderBy('id')->Paginate(25);
        $roles = DB::table('roles')->orderBy('created_at')->get();
        $user_roles = DB::table('user_roles')->get();
        // $entities = DB::table('Entities')->get();

        return view('back.users', compact('title', 'users', 'roles', 'user_roles'));
    }

    public function filterUsers(Request $request)
    {
        $title = 'Users';

        if (@$request->register_by && @$request->verified !== null) {
            $users = User::where('register_by', '!=', 'ADMIN')->where('register_by', @$request->register_by)->where('verified', @$request->verified)->where('username', 'like', "%" . @$request->username . "%")->orderBy('id')->Paginate(50);
        } else if (@$request->register_by) {
            $users = User::where('register_by', '!=', 'ADMIN')->where('register_by', @$request->register_by)->where('username', 'like', "%" . @$request->username . "%")->orderBy('id')->Paginate(50);
        } else if (@$request->verified !== null) {
            $users = User::where('register_by', '!=', 'ADMIN')->where('verified', @$request->verified)->where('username', 'like', "%" . @$request->username . "%")->orderBy('id')->Paginate(50);
        } else {
            $users = User::where('register_by', '!=', 'ADMIN')->where('username', 'like', "%" . @$request->username . "%")->orderBy('id')->Paginate(50);
        }
        $roles = DB::table('roles')->orderBy('created_at')->get();
        $user_roles = DB::table('user_roles')->get();
        // $entities = DB::table('Entities')->get();

        return view('back.users', compact('title', 'users', 'request', 'roles', 'user_roles'));
    }
}
