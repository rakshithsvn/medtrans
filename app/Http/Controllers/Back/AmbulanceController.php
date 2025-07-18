<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\AmbulanceAllocation;
use App\Models\AmbulanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\User;
use App\Models\VehicleMovement;
use App\Notifications\GeneralNotification;
use Auth;

class AmbulanceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');        
        $externalAmbulance = ['Om Sai', 'Ganesh', 'Jeeva', 'Shree Sharavu', 'Shree Durga', 'Shree Ganesh', 'Kaveri', 'Mangala Hospital', 'Highland Hospital'];
        $ward_list = ['ED', '1D', '2C', '2D', '3B', '3C', '3D', '4B', '4C', '4D', '5B', '5C', '5D', '6B', '6C', '6D', '7B', '7C'];
        $icu_list = ['SICU', 'ITU', 'PICU', 'NICU', 'CCU', 'MICU', 'HDU', 'DIALYSIS'];
        view()->share('title', 'Ambulance Request');
        view()->share('externalAmbulance', $externalAmbulance);
        view()->share('ward_list', $ward_list);
        view()->share('icu_list', $icu_list);
    }

    public function index(Request $request, $type)
    {
        $title = "Ambulance Request - ".($type == 'ward' ? 'Ward' : 'Help Desk');

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $ambulanceRequestData = AmbulanceRequest::where('type', $type)->latest();
        $cancelledRequests = AmbulanceRequest::withTrashed()
            ->where('ambulance_requests.type', $type)
            ->whereNotNull('ambulance_requests.cancel_by')
            ->whereNotNull('ambulance_requests.cancel_reason');
        $cancelledMovements = VehicleMovement::withTrashed()
            ->where('vehicle_movements.type', $type)
            ->where('status', 'cancel');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();
            $ambulanceRequestData = $ambulanceRequestData->whereBetween('booking_date', [$fromDate, $toDate]);
            $cancelledRequests = $cancelledRequests->whereBetween('booking_date', [$fromDate, $toDate]);
            $cancelledMovements = $cancelledMovements->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $ambulanceRequestData = $ambulanceRequestData->whereDate('booking_date', '>=', $fromDate);
            $cancelledRequests = $cancelledRequests->whereDate('booking_date', '>=', $fromDate);
            $cancelledMovements = $cancelledMovements->whereDate('date', '>=', $fromDate);
        } elseif ($toDate) {
            $ambulanceRequestData = $ambulanceRequestData->whereDate('booking_date', '<=', $toDate);
            $cancelledRequests = $cancelledRequests->whereDate('booking_date', '<=', $toDate);
            $cancelledMovements = $cancelledMovements->whereDate('date', '<=', $toDate);
        }

        $ambulanceRequests = (clone $ambulanceRequestData)->whereNull('allot_type')->paginate(10);

        $requestIds = (clone $ambulanceRequestData)->pluck('id');
        $ambulanceAllocations = AmbulanceAllocation::
        whereIn('ambulance_request_id', $requestIds)
        ->whereHas('vehicleMovement', function($q) {
            $q->where('status', 'allot');
        })
        ->with([
            'ambulanceRequest',
            'driver',
            'vehicle',
            'supervisor',
            'vehicleMovement'
            ])      
            ->latest('created_at')->paginate(10);

        $ambulanceCompleted = AmbulanceAllocation::
        whereIn('ambulance_request_id', $requestIds)
        ->whereHas('vehicleMovement', function($q) {
            $q->where('status', 'end');
        })
        ->with([
            'ambulanceRequest',
            'driver',
            'vehicle',
            'supervisor',
            'vehicleMovement'
            ])      
            ->latest('created_at')->paginate(10);
        // dd($requestIds, $ambulanceCompleted);

        $user = Auth::user();
        $drivers = Employee::where('type', 'DRIVER')
        ->whereNotIn('id', function ($query) {
            $query->select('driver_id')
                ->from('vehicle_movements')
                ->where('status', 'start');
        })
        ->pluck('id', 'name');
        $vehicles = Vehicle::whereIn('type', ['Ambulance'])
        ->whereNotIn('id', function ($query) {
            $query->select('vehicle_id')
                ->from('vehicle_movements')
                ->where('status', 'start');
        })
        ->get();      
        $supervisors = Employee::where('type', 'SUPERVISOR')->get();
        $technicians = Employee::where('type', 'TECHNICIAN')->get();

        $cancelledMovements = $cancelledMovements
            ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_movements.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name',
            )
            ->get();
        $cancelledRequests = $cancelledRequests
            ->leftJoin('employees', 'ambulance_requests.cancel_by', '=', 'employees.id')
            ->select(
                'ambulance_requests.*',
                'employees.name as cancelled_by'
            )
            ->get();

        $combinedCancelled = collect($cancelledRequests)
            ->merge($cancelledMovements)
            ->sortByDesc('booking_date'); 
            
        // dd($ambulanceRequests, $type);

        return view('back.ambulance-request', compact('title', 'ambulanceRequests', 'ambulanceAllocations', 'ambulanceCompleted', 'user', 'type', 'drivers', 'vehicles', 'supervisors', 'technicians', 'cancelledMovements', 'cancelledRequests', 'combinedCancelled'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $requestData = $request->except('_token', '_method');
        $requestData['destination'] = $request->destination ?? $request->patient_location;
        $requestData['user_id'] = auth()->id();
        AmbulanceRequest::insert($requestData);

        $user = User::where('username', 'med-helpdesk')->first();
        if($user) $user->notify(new GeneralNotification(
            'Ambulance '.$request->type.' Request has been raised',
            url('/ambulance/request/'.$request->type)
        ));

        return redirect()->back()->with('success', 'Request created successfully!');
    }

    public function update(Request $request, $id)
    {
        $requestData = $request->except('_token', '_method');
        $requestData['user_id'] = auth()->id();
        $requestData['update_flag'] = 1;
        
//  dd($requestData, $id);
        AmbulanceRequest::where('id', $id)->update($requestData);
        return redirect()->back()->with('success', 'Request updated successfully!');
    }

    public function destroy($id)
    {
        AmbulanceRequest::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Request deleted successfully!');
    }

    public function import(Request $request)
    {
        // Implement your import logic here
    }

    public function search(Request $request)
    {
        $term = $request->input('search');

        $results = AmbulanceRequest::where(function ($query) use ($term) {
            $query->where('model', 'like', "%{$term}%")
                ->orWhere('reg_no', 'like', "%{$term}%")
                ->orWhere('type', 'like', "%{$term}%")
                ->orWhere('driver_id', 'like', "%{$term}%")
                ->orWhere('fuel_type', 'like', "%{$term}%");
        })->get();

        return response()->json($results);
    }

    public function allocate(Request $request)
    {
        //dd($request->all());
        $requestData = $request->except('_token','patient_name','mr_no','contact_no','ambulance_type', 'booking_date', 'booking_time', 'destination', 'reason', 'type');
        $requestData['user_id'] = auth()->id();

        $allocation_id = AmbulanceAllocation::insertGetId($requestData);

        $moveData['vehicle_id'] = $request['vehicle_id'];
        $moveData['driver_id'] = $request['driver_id'];
        $moveData['allocation_id'] = $allocation_id;
        $moveData['date'] = $request['booking_date'];
        $moveData['time_in'] = $request['booking_time'];
        $moveData['place'] = $request['destination'];
        $moveData['purpose'] = $request['reason'];
        $moveData['department'] = 'Ambulance';
        $moveData['contact_no'] = $request['contact_no'];
        $moveData['type'] = $request['type'];
        $moveData['status'] = 'allot';
        $moveData['user_id'] = auth()->id();
        $moveData['created_at'] = now();

        if(@$requestData['confirmed'] == 'yes') {
            VehicleMovement::insertGetId($moveData);

            $driver = Employee::find($request->driver_id);    
            $vehicle = Vehicle::find($request->vehicle_id);
            $user = User::where('username', $driver->employee_id)->first();

            $title = "New Trip Assigned";
            $message = "A new ambulance trip has been scheduled.";
            $fcmId = @$user->fcm_id;

            // dd($title, $message, $fcmId);
            if($fcmId) NotificationController::sendNotification($title, $message, $fcmId);

            $user = User::where('id', $request['user_id'])->first();
            if($user) $user->notify(new GeneralNotification(
                "Ambulance has been allotted.<br/><b>Vehicle:</b> {$vehicle->reg_no} <b>Driver:</b> {$driver->name}",
                url("/ambulance/request/{$request->type}")
            ));

            $tech = Employee::find($request['technician_id']);
            if($tech) {
                $user = User::where('username', $tech['employee_id'])->first();
                if($user) $user->notify(new GeneralNotification(
                "Ambulance has been allotted.<br/><b>Vehicle:</b> {$vehicle->reg_no} <b>Driver:</b> {$driver->name}",
                url("/ambulance/request/{$request->type}")
            ));
            }
        }
        AmbulanceRequest::where('id', $request->ambulance_request_id)->update(['allot_type' => 1]);

        return redirect()->back()->with('success', 'Allocation done successfully!');
    }

    public function report(Request $request)
    {
        $title = "Ambulance Report";
        $ambulanceDetails = AmbulanceAllocation::join('ambulance_requests', 'ambulance_requests.id', '=', 'ambulance_allocations.ambulance_request_id')
        ->leftJoin('vehicle_movements', 'vehicle_movements.allocation_id', '=', 'ambulance_allocations.id')
        ->where('vehicle_movements.status', 'end')
        ->whereNotNull('ambulance_allocations.ambulance_arranged');

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();
            $ambulanceDetails = $ambulanceDetails->whereBetween('booking_date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $ambulanceDetails = $ambulanceDetails->whereDate('booking_date', '>=', $fromDate);
        } elseif ($toDate) {
            $ambulanceDetails = $ambulanceDetails->whereDate('booking_date', '<=', $toDate);
        }

        $ambulanceHelpDeskDetails =(clone $ambulanceDetails)
        ->where('ambulance_requests.type', 'help-desk')
        ->whereNull('ambulance_allocations.tied_up_ambulance')
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
            ->whereNull('ambulance_allocations.tied_up_ambulance')
            ->select('ambulance_allocations.ambulance_arranged as ambulance_type', DB::raw('count(*) as data_count'))
            ->groupBy('ambulance_allocations.ambulance_arranged')
            ->orderByDesc('data_count')
            ->limit(10)
            ->pluck('data_count', 'ambulance_type')
            ->toArray();

        $ambulanceHelpDeskExtDetails =(clone $ambulanceDetails)
        ->where('ambulance_requests.type', 'help-desk')
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

        $ambulanceHelpDeskExtGrouped = $ambulanceHelpDeskExtDetails->groupBy('ambulance_type');

        $ambulanceHelpDeskExtChartData = (clone $ambulanceDetails)
            ->where('ambulance_requests.type', 'help-desk')
            ->whereNotNull('ambulance_allocations.tied_up_ambulance')
            ->select('ambulance_allocations.tied_up_ambulance as ambulance_type', DB::raw('count(*) as data_count'))
            ->groupBy('ambulance_allocations.tied_up_ambulance')
            ->orderByDesc('data_count')
            ->limit(10)
            ->pluck('data_count', 'ambulance_type')
            ->toArray();

        $ambulanceWardDetails = (clone $ambulanceDetails)
        ->where('ambulance_requests.type', 'ward')
        ->whereNull('ambulance_allocations.tied_up_ambulance')
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
            ->where('ambulance_requests.type', 'ward')
            ->whereNull('ambulance_allocations.tied_up_ambulance')
            ->select('ambulance_allocations.ambulance_arranged as ambulance_type', DB::raw('count(*) as data_count'))
            ->groupBy('ambulance_allocations.ambulance_arranged')
            ->orderByDesc('data_count')
            ->limit(10)
            ->pluck('data_count', 'ambulance_type')
            ->toArray();

         $ambulanceWardExtDetails = (clone $ambulanceDetails)
        ->where('ambulance_requests.type', 'ward')
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

        $ambulanceWardExtGrouped = $ambulanceWardExtDetails->groupBy('ambulance_type');

        $ambulanceWardExtChartData = (clone $ambulanceDetails)
            ->where('ambulance_requests.type', 'ward')
            ->whereNotNull('ambulance_allocations.tied_up_ambulance')
            ->select('ambulance_allocations.tied_up_ambulance as ambulance_type', DB::raw('count(*) as data_count'))
            ->groupBy('ambulance_allocations.tied_up_ambulance')
            ->orderByDesc('data_count')
            ->limit(10)
            ->pluck('data_count', 'ambulance_type')
            ->toArray();

        $user = Auth::user();
        $drivers = Employee::where('type', 'DRIVER')->pluck('id', 'name');
        $vehicles = Vehicle::whereIn('type', ['Ambulance'])->get();        
        $supervisors = Employee::where('type', 'SUPERVISOR')->get();

        return view('back.ambulance-report', compact('ambulanceHelpDeskDetails', 'ambulanceHelpDeskExtDetails', 'ambulanceHelpDeskGrouped', 'ambulanceHelpDeskChartData', 'ambulanceHelpDeskExtGrouped', 'ambulanceHelpDeskExtChartData', 'ambulanceWardGrouped', 'ambulanceWardChartData', 'ambulanceWardExtGrouped', 'ambulanceWardExtChartData', 'ambulanceWardDetails', 'ambulanceWardExtDetails',  'user', 'drivers', 'vehicles', 'title'));
    }

    // Technician Request
    public function techIndex(Request $request, $type)
    {
        $title = 'Technician Request';

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $techRequests = DB::table('technician_requests');
        $allocationRequests = AmbulanceAllocation::with([
            'ambulanceRequest',
            'driver',
            'vehicle',
            'supervisor'
        ])->whereNotNull('technician_id');
        $cancelledMovements = VehicleMovement::withTrashed()->where('vehicle_movements.type', $type)->where('status', 'cancel');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();
            $techRequests = $techRequests->whereBetween('booking_date', [$fromDate, $toDate]);
             $allocationRequests = $allocationRequests->whereHas('ambulanceRequest', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('booking_date', [$fromDate, $toDate]);
            });
            $cancelledMovements = $cancelledMovements->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $techRequests = $techRequests->whereDate('booking_date', '>=', $fromDate);
            $allocationRequests = $allocationRequests->whereHas('ambulanceRequest', function ($query) use ($fromDate) {
                $query->whereDate('booking_date', '>=', $fromDate);
            });
            $cancelledMovements = $cancelledMovements->whereDate('date', '>=', $fromDate);
        } elseif ($toDate) {
            $techRequests = $techRequests->whereDate('booking_date', '<=', $toDate);
           $allocationRequests = $allocationRequests->whereHas('ambulanceRequest', function ($query) use ($toDate) {
                $query->whereDate('booking_date', '>=', $toDate);
            });
            $cancelledMovements = $cancelledMovements->whereDate('date', '<=', $toDate);
        }

        $allocationRequests = $allocationRequests
         ->whereHas('ambulanceRequest', function ($query) use ($type) {
            $query->where('type', $type);
        });        

        if (in_array(Auth::user()->register_by, ['TECHNICIAN'])) {
            $employee = Employee::where('employee_id', Auth::user()->username)->first();
            $techRequests = $techRequests->where('technician_id', @$employee->id);
            $allocationRequests = $allocationRequests->where('technician_id', @$employee->id);
        }
       
        $techRequests = $techRequests->latest()->paginate(10);
        $excludedIds = $techRequests->pluck('allocation_id');

        $allocationRequests = $allocationRequests->whereNotIn('id', $excludedIds)->latest()->paginate(10);

        $cancelledMovements = $cancelledMovements
            ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_movements.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name',
            )
            ->get();

        $user = Auth::user();

        return view('back.technician-request', compact('allocationRequests', 'techRequests', 'user', 'title', 'type'));
    }

    public function techStore(Request $request)
    {
        //dd($request->all());
        $requestData = $request->except('_token','driver_name', 'vehicle_name');
        $requestData['user_id'] = auth()->id();
        
        $requestData['time_current']  = json_encode($request->time_current);
        $requestData['temp_current']  = json_encode($request->temp_current);
        $requestData['bp_current']    = json_encode($request->bp_current);
        $requestData['hr_current']    = json_encode($request->hr_current);
        $requestData['rep_current']   = json_encode($request->rep_current);
        $requestData['spo2_current']  = json_encode($request->spo2_current);

        DB::table('technician_requests')->insert($requestData);

        return redirect()->back()->with('success', 'Request created successfully!');
    }

    public function techUpdate(Request $request, $id)
    {
        $requestData = $request->except('_token', '_method');
        //dd($requestData, $id);
        DB::table('technician_requests')->where('id', $id)->update($requestData);

        return redirect()->back()->with('success', 'Request updated successfully!');
    }

}
