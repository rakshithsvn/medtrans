<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\TransportAllocation;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\VehicleMovement;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class TransportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $departments = [
            'Accounts','Maintenance','Corporate Desk','Pharmacy','Purchase','MRD','Laboratory','AJ Tower','HR','Oncology','Bloodbank','Marketing','Front Office','Operations','Housekeeping','Corporate Accounts','Biomedical','Billing','MHA','Transplant','Stores','Hospital Supervisor Civil','HIC','Radiology','Admin 7th','Admin','EDP'
        ];
        $requests = [
            'City Purchase',
            'Parcel',
            'ESI Hospital Visit',
            'Excise Office Visit',
            'Bank Visit',
            'Auditor Office Visit',
            'Guest Pick Up',
            'DHO Office Visit',
            'Advocate Office Visit',
            'Mechanical Servicing',
            'Commisioner Office',
            'Blood Sample',
            'Marketing - one to one visit',
            'Camp Visit',
            'Medical College Visit',
            'AJ Tower Visit',
            'Management Work',
            'Wenlock Hospital Visit',
            'Hostel Trip',
            'Others' 
        ];
        $this->middleware('auth');
        view()->share('title', 'Transport Requisition');
        view()->share('departments', $departments);
        view()->share('requests', $requests);
    }

    public function index(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $transportRequestData = TransportRequest::latest();
        $cancelledRequests = TransportRequest::withTrashed()
            ->whereNotNull('transport_requests.cancel_by')
            ->whereNotNull('transport_requests.cancel_reason');
        $cancelledMovements = VehicleMovement::withTrashed()->where('vehicle_movements.type', 'transport')->where('vehicle_movements.status', 'cancel');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();
            $transportRequestData = $transportRequestData->whereBetween('booking_date', [$fromDate, $toDate]);
            $cancelledRequests = $cancelledRequests->whereBetween('booking_date', [$fromDate, $toDate]);
            $cancelledMovements = $cancelledMovements->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $transportRequestData = $transportRequestData->whereDate('booking_date', '>=', $fromDate);
            $cancelledRequests = $cancelledRequests->whereDate('booking_date', '>=', $fromDate);
            $cancelledMovements = $cancelledMovements->whereDate('date', '>=', $fromDate);
        } elseif ($toDate) {
            $transportRequestData = $transportRequestData->whereDate('booking_date', '<=', $toDate);
            $cancelledRequests = $cancelledRequests->whereDate('booking_date', '<=', $toDate);
            $cancelledMovements = $cancelledMovements->whereDate('date', '<=', $toDate);
        }
        $transportRequests = (clone $transportRequestData)->whereNull('allot_type')->latest()->paginate(10);

        $requestIds = (clone $transportRequestData)->pluck('id');
        $transportAllocations = TransportAllocation::where('type', 'transport')
        ->whereIn('transport_request_id', $requestIds)
        ->whereHas('vehicleMovement', function($q) {
            $q->where('status', 'allot');
        })
        ->with([
            'transportRequest',
            'driver',
            'vehicle',
            'supervisor',
            'vehicleMovement'
        ])
        ->latest()
        ->paginate(10);

        $transportCompleted = TransportAllocation::where('type', 'transport')
        ->whereIn('transport_request_id', $requestIds)
        ->whereHas('vehicleMovement', function($q) {
            $q->where('status', 'end');
        })
        ->with([
            'transportRequest',
            'driver',
            'vehicle',
            'supervisor',
            'vehicleMovement'
        ])
        ->latest()
        ->paginate(10);

        $user = Auth::user();
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
            ->leftJoin('employees', 'transport_requests.cancel_by', '=', 'employees.id')
            ->select(
                'transport_requests.*',
                'employees.name as cancelled_by'
            )
            ->latest()->get();

        $combinedCancelled = collect($cancelledRequests)
            ->merge($cancelledMovements)
            ->sortByDesc('booking_date');

// dd($transportAllocations);
        return view('back.transport-request', compact('transportRequests', 'transportAllocations', 'transportCompleted', 'combinedCancelled', 'user', 'drivers', 'vehicles'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $requestData = $request->except('_token', '_method', 'transport_request_id', 'vehicle_id', 'driver_id', 'type');
        $requestData['user_id'] = auth()->id();

        TransportRequest::insert($requestData);

        $user = User::where('username', '7934')->first();
        if($user) $user->notify(new GeneralNotification(
            'Transport Request has been raised',
            url('/transport/request')
        ));

        return redirect()->back()->with('success', 'Request created successfully!');
    }

    public function update(Request $request, $id)
    {
        $requestData = $request->except('_token', '_method', 'type', 'vehicle_id', 'driver_id');
        $requestData['user_id'] = auth()->id();
        $requestData['update_flag'] = 1;
        
//  dd($requestData, $id);
        TransportRequest::where('id', $id)->update($requestData);

        return redirect()->back()->with('success', 'Request updated successfully!');
    }

    public function destroy($id)
    {
        TransportRequest::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Request deleted successfully!');
    }

    public function import(Request $request)
    {
        // Implement your import logic here
    }

    public function search(Request $request)
    {
        $term = $request->input('search');

        $results = TransportRequest::where(function ($query) use ($term) {
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
        $requestData['transport_request_id'] = $request->id;
        $requestData['type'] = 'transport';
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
        $moveData['place'] = $request['destination'];
        $moveData['purpose'] = $request['reason'];
        $moveData['department'] = $request['department'];
        $moveData['contact_no'] = $request['contact_no'];
        $moveData['type'] = 'transport';
        $moveData['status'] = 'allot';
        $moveData['user_id'] = auth()->id();
        $moveData['created_at'] = now();

        VehicleMovement::insertGetId($moveData);
        TransportRequest::where('id', $request->id)->update(['allot_type' => 1]);

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
            "Trip has been allotted.<br/><b>Vehicle:</b> {$vehicle->reg_no} <b>Driver:</b> {$driver->name}",
            url('/transport/request')
        ));

        return redirect()->back()->with('success', 'Allocation done successfully!');
    }

    public function report(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $vehicleMovements = VehicleMovement::where('type', 'transport')->where('status', 'end')->whereNull('vehicle_movements.deleted_at');

        $transportRequests = TransportRequest::query();

        $transportAllocations = TransportAllocation::where('type', 'transport')
            ->with([
                'transportRequest',
                'driver',
                'vehicle',
                'supervisor'
            ]);

        $transportDetails = TransportAllocation::
            join('transport_requests', 'transport_requests.id', '=', 'transport_allocations.transport_request_id')
            ->join('vehicles', 'vehicles.id', '=', 'transport_allocations.vehicle_id')
            ->join('vehicle_movements', 'vehicle_movements.allocation_id', '=', 'transport_allocations.id')
            ->where('vehicle_movements.type', 'transport')
            ->whereNotNull('transport_requests.request_type');

        $cancelledMovements = VehicleMovement::withTrashed()
            ->where('vehicle_movements.type', 'transport')
            ->where('status', 'cancel');

        if ($fromDate && $toDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();
            $vehicleMovements = $vehicleMovements->whereBetween('date', [$fromDate, $toDate]);
            $cancelledMovements = $cancelledMovements->whereBetween('date', [$fromDate, $toDate]);
            $transportRequests = $transportRequests->whereBetween('booking_date', [$fromDate, $toDate]);
            $transportAllocations = $transportAllocations->whereHas('transportRequest', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('transport_requests.booking_date', [$fromDate, $toDate]);
            });
            $transportDetails = $transportDetails->whereBetween('transport_requests.booking_date', [$fromDate, $toDate]);
        
        } elseif ($fromDate) {
            $vehicleMovements = $vehicleMovements->whereDate('date', '>=', $fromDate);
            $cancelledMovements = $cancelledMovements->whereDate('date', '>=', $fromDate);
            $transportRequests = $transportRequests->whereDate('booking_date', '>=', $fromDate);
            $transportAllocations = $transportAllocations->whereHas('transportRequest', function ($query) use ($fromDate) {
                $query->whereDate('transport_requests.booking_date', '>=', $fromDate);
            });
            $transportDetails = $transportDetails->whereDate('transport_requests.booking_date', '>=', $fromDate);

        } elseif ($toDate) {
            $vehicleMovements = $vehicleMovements->whereDate('date', '<=', $toDate);
            $cancelledMovements = $cancelledMovements->whereDate('date', '<=', $toDate);
            $transportRequests = $transportRequests->whereDate('booking_date', '<=', $toDate);
            $transportAllocations = $transportAllocations->whereHas('transportRequest', function ($query) use ($toDate) {
                $query->whereDate('transport_requests.booking_date', '<=', $toDate);
            });
            $transportDetails = $transportDetails->whereDate('transport_requests.booking_date', '<=', $toDate);

        }

        $vehicleMovementIds = $vehicleMovements->pluck('allocation_id');

        $transportRequests = $transportRequests->latest()->paginate(10);

        $transportAllocations = $transportAllocations
            // ->whereIn('id', $vehicleMovementIds)
            ->latest()
            ->paginate(10);

        $transportDetails = $transportDetails
            ->whereIn('transport_allocations.id', $vehicleMovementIds)
            ->select(
                'transport_allocations.id',
                'transport_requests.request_type as transport_type',
                'transport_requests.booking_date',
                'transport_requests.department',
                'transport_requests.destination',
                'vehicles.reg_no as vehicle_no',
                'vehicle_movements.km_covered as km_covered',
            )
            ->orderBy('transport_requests.request_type')
            ->orderByDesc('transport_requests.created_at')
            ->get();

        $transportGrouped = $transportDetails->groupBy('transport_type');

        $user = Auth::user();        
        $drivers = Employee::where('type', 'DRIVER')->pluck('id', 'name');
        $vehicles = Vehicle::whereNotIn('type', ['Ambulance', 'Tanker'])->get();

        $cancelledMovements = $cancelledMovements
            ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_movements.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name',
            )
            ->get();

// dd($transportGrouped);
        return view('back.transport-report', compact('transportRequests', 'transportDetails', 'transportGrouped', 'transportAllocations', 'cancelledMovements', 'user', 'drivers', 'vehicles'));
    }

}
