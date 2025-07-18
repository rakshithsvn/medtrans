<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\TransportAllocation;
use App\Models\HomeHealthRequest;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMovement;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class HomeHealthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $services = [
            'Basic Assessment',
            'Bed Sore Dressing',
            'Wound Dressing',
            'Injection',
            'IV Cannulization ',
            'IV Fluids/ IV Antibiotics',
            'Sample Collection',
            'Nebulization',
            'ECG',
            'Folys Catheter',
            'Ryles Tube Insertion/ Removal',
            'Enema',
            'Suture Removal',
            'Other'
        ];
        view()->share('services', $services);
        view()->share('title', 'Home Health Appointment');
    }

    public function index(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $homehealthRequestData = HomeHealthRequest::latest();
        $cancelledRequests = HomeHealthRequest::withTrashed()
            ->whereNotNull('home_health_requests.cancel_by')
            ->whereNotNull('home_health_requests.cancel_reason');
        $cancelledMovements = VehicleMovement::withTrashed()
            ->where('vehicle_movements.type', 'home-health')
            ->where('status', 'cancel');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();
            $homehealthRequestData = $homehealthRequestData->whereBetween('booking_date', [$fromDate, $toDate]);
            $cancelledRequests = $cancelledRequests->whereBetween('booking_date', [$fromDate, $toDate]);
            $cancelledMovements = $cancelledMovements->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $homehealthRequestData = $homehealthRequestData->whereDate('booking_date', '>=', $fromDate);
            $cancelledRequests = $cancelledRequests->whereDate('booking_date', '>=', $fromDate);
            $cancelledMovements = $cancelledMovements->whereDate('date', '>=', $fromDate);
        } elseif ($toDate) {
            $homehealthRequestData = $homehealthRequestData->whereDate('booking_date', '<=', $toDate);
            $cancelledRequests = $cancelledRequests->whereDate('booking_date', '<=', $toDate);
            $cancelledMovements = $cancelledMovements->whereDate('date', '<=', $toDate);
        }
        $homehealthRequests = (clone $homehealthRequestData)->where('status', 'Pending')->paginate(10);        

        $requestIds = (clone $homehealthRequestData)->pluck('id');
   
        $transportAllocations = TransportAllocation::where('type', 'home-health')
        ->whereIn('transport_request_id', $requestIds)
        ->whereHas('vehicleMovement', function($q) {
            $q->where('status', 'allot');
        })
        ->with([
            'homehealthRequest',
            'driver',
            'vehicle',
            'supervisor',
            'vehicleMovement'
        ])
        ->latest()
        ->paginate(10);

        $transportCompleted = TransportAllocation::where('type', 'home-health')
        ->whereIn('transport_request_id', $requestIds)
        ->whereHas('vehicleMovement', function($q) {
            $q->where('status', 'end');
        })
        ->with([
            'homehealthRequest',
            'driver',
            'vehicle',
            'supervisor',
            'vehicleMovement'
        ])
        ->latest()
        ->paginate(10);

        $cancelledMovements = $cancelledMovements
            ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_movements.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name',
            )
            ->latest()->get();
        $cancelledRequests = $cancelledRequests
            ->leftJoin('employees', 'home_health_requests.cancel_by', '=', 'employees.id')
            ->select(
                'home_health_requests.*',
                'employees.name as cancelled_by'
            )
            ->latest()->get();

        $combinedCancelled = collect($cancelledRequests)
            ->merge($cancelledMovements)
            ->sortByDesc('booking_date');

        $drivers = Employee::where('type', 'DRIVER')
        ->whereNotIn('id', function ($query) {
            $query->select('driver_id')
                ->from('vehicle_movements')
                ->where('status', 'start');
        })->pluck('id', 'name');
        $vehicles = Vehicle::whereNotIn('type', ['Ambulance', 'Tanker'])
        ->whereNotIn('id', function ($query) {
            $query->select('vehicle_id')
                ->from('vehicle_movements')
                ->where('status', 'start');
        })
        ->get();

        $user = Auth::user();        

        return view('back.homehealth-request', compact('homehealthRequests', 'transportAllocations', 'transportCompleted', 'combinedCancelled', 'drivers', 'vehicles', 'user'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $requestData = $request->except('_token', 'vehicle_id', 'driver_id', 'type');
        $requestData['user_id'] = auth()->id();
        HomeHealthRequest::insert($requestData);

        $user = User::where('username', '7934')->first();
        if($user) $user->notify(new GeneralNotification(
            'Home Health Request has been raised',
            url('/home-health/request')
        ));

        return redirect()->back()->with('success', 'Request created successfully!');
    }

    public function update(Request $request, $id)
    {
        $requestData = $request->except('_token', '_method', 'status', 'type', 'vehicle_id', 'driver_id');
        $requestData['user_id'] = auth()->id();
        $requestData['update_flag'] = 1;
        
// dd($requestData, $id);
        HomeHealthRequest::where('id', $id)->update($requestData);

        return redirect()->back()->with('success', 'Request updated successfully!');
    }

    public function allocate(Request $request)
    {
        //dd($request->all());
        $requestData['transport_request_id'] = $request->id;
        $requestData['type'] = 'home-health';
        $requestData['driver_id'] = $request->driver_id;
        $requestData['vehicle_id'] = $request->vehicle_id;
        $supervisor = Employee::where('employee_id', auth()->user()->username)->first();
        $requestData['supervisor_id'] = @$supervisor->id;
        $requestData['user_id'] = auth()->id();

        $allocation_id = TransportAllocation::insertGetId($requestData);

        $moveData['vehicle_id'] = $request['vehicle_id'];
        $moveData['driver_id'] = $request['driver_id'];
        $moveData['allocation_id'] = $allocation_id;
        $moveData['date'] = $request['booking_date'];
        $moveData['time_in'] = $request['booking_time'];
        $moveData['place'] = $request['address'];
        $moveData['purpose'] = $request['service_type'];
        $moveData['department'] = 'Home Health';
        $moveData['type'] = 'home-health';
        $moveData['status'] = 'allot';
        $moveData['user_id'] = auth()->id();
        $moveData['created_at'] = now();

        VehicleMovement::insertGetId($moveData);
        HomeHealthRequest::where('id', $request->id)->update(['status'=>'Completed','allot_type' => 1]);

        $driver = Employee::find($request->driver_id);
        $vehicle = Vehicle::find($request->vehicle_id);
        $user = User::where('username', $driver->employee_id)->first();

        $title = "New Trip Assigned";
        $message = "A new trip has been scheduled.";
        $fcmId = @$user->fcm_id;

        // dd($title, $message, $fcmId);
        if($fcmId) NotificationController::sendNotification($title, $message, $fcmId);
        
        $user = User::where('id', $request['user_id'])->first();
        if($user) $user->notify(new GeneralNotification(
            "Home Health Trip has been allotted.<br/><b>Vehicle:</b> {$vehicle->reg_no} <b>Driver:</b> {$driver->name}",
            url('/home-health/request')
        ));

        return redirect()->back()->with('success', 'Allocation done successfully!');
    }

    public function destroy($id)
    {
        HomeHealthRequest::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Request deleted successfully!');
    }

    public function import(Request $request)
    {
        // Implement your import logic here
    }

    public function search(Request $request)
    {
        $term = $request->input('search');

        $results = HomeHealthRequest::where(function ($query) use ($term) {
            $query->where('model', 'like', "%{$term}%")
                ->orWhere('reg_no', 'like', "%{$term}%")
                ->orWhere('type', 'like', "%{$term}%")
                ->orWhere('driver_id', 'like', "%{$term}%")
                ->orWhere('fuel_type', 'like', "%{$term}%");
        })->get();

        return response()->json($results);
    }

     public function report(Request $request)
    {
        // $HomeHealthDetails = HomeHealthRequest::whereNotNull('service_type');
        $transportDetails = TransportAllocation::
            join('transport_requests', 'transport_requests.id', '=', 'transport_allocations.transport_request_id')
            ->join('vehicles', 'vehicles.id', '=', 'transport_allocations.vehicle_id')
            ->join('vehicle_movements', 'vehicle_movements.allocation_id', '=', 'transport_allocations.id')
            ->where('vehicle_movements.type', 'home-health')
            ->whereNotNull('transport_requests.request_type');

        $vehicleMovements = VehicleMovement::where('type', 'home-health')->where('status', 'end')->whereNull('vehicle_movements.deleted_at');

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();
            $transportDetails = $transportDetails->whereBetween('vehicle_movements.date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $transportDetails = $transportDetails->whereDate('vehicle_movements.date', '>=', $fromDate);
        } elseif ($toDate) {
            $transportDetails = $transportDetails->whereDate('vehicle_movements.date', '<=', $toDate);
        }

        $vehicleMovementIds = $vehicleMovements->pluck('allocation_id');

        $transportDetails = $transportDetails
            ->leftJoin('home_health_requests', 'home_health_requests.id', '=', 'transport_allocations.transport_request_id')
            ->whereIn('transport_allocations.id', $vehicleMovementIds)
            ->select(
                'home_health_requests.*',
                'transport_allocations.id'
            )
            ->orderBy('home_health_requests.service_type')
            ->orderByDesc('home_health_requests.created_at');

            // dd($transportDetails, $vehicleMovementIds);

        $HomeHealthAJDetails = (clone $transportDetails)
            ->where('home_health_requests.aj_patient', 'Yes')
            ->select(
                'home_health_requests.id',
                'home_health_requests.service_type',
                'home_health_requests.booking_date',
                'home_health_requests.address',
            )
            ->orderBy('home_health_requests.service_type')
            ->orderByDesc('home_health_requests.created_at')
            ->get();

        $HomeHealthAJGrouped = $HomeHealthAJDetails->groupBy('service_type');

        $HomeHealthAJChartData = (clone $transportDetails)
            ->where('home_health_requests.aj_patient', 'Yes')
            ->select('home_health_requests.service_type', DB::raw('count(*) as data_count'))
            ->groupBy('home_health_requests.service_type')
            ->orderByDesc('data_count')
            ->limit(10)
            ->pluck('data_count', 'service_type')
            ->toArray();

        $HomeHealthNonAJDetails = (clone $transportDetails)
            ->where('home_health_requests.aj_patient', 'No')
            ->select(
                'home_health_requests.id',
                'home_health_requests.service_type',
                'home_health_requests.booking_date',
                'home_health_requests.address',
            )
            ->orderBy('home_health_requests.service_type')
            ->orderByDesc('home_health_requests.created_at')
            ->get();

        $HomeHealthNonAJGrouped = $HomeHealthNonAJDetails->groupBy('service_type');

        $HomeHealthNonAJChartData = (clone $transportDetails)
            ->where('home_health_requests.aj_patient', 'No')
            ->select('home_health_requests.service_type', DB::raw('count(*) as data_count'))
            ->groupBy('home_health_requests.service_type')
            ->orderByDesc('data_count')
            ->limit(10)
            ->pluck('data_count', 'service_type')
            ->toArray();
            
        return view('back.homehealth-report', compact('HomeHealthAJGrouped', 'HomeHealthAJChartData', 'HomeHealthNonAJGrouped', 'HomeHealthNonAJChartData'));
    }

}
