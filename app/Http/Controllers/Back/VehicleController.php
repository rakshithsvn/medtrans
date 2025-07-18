<?php

namespace App\Http\Controllers\Back;

use App\Exports\CustomExport;
use App\Http\Controllers\Controller;
use App\Models\AmbulanceAllocation;
use App\Models\AmbulanceRequest;
use App\Models\Employee;
use App\Models\HomeHealthRequest;
use App\Models\TransportAllocation;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMovement;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Auth;
use DB;
use Storage;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $departments = [
            'Accounts',
            'Maintenance',
            'Corporate Desk',
            'Pharmacy',
            'Purchase',
            'MRD',
            'Laboratory',
            'AJ Tower',
            'HR',
            'Oncology',
            'Bloodbank',
            'Marketing',
            'Front Office',
            'Operations',
            'Housekeeping',
            'Corporate Accounts',
            'Biomedical',
            'Billing',
            'MHA',
            'Transplant',
            'Stores',
            'Hospital Supervisor Civil',
            'HIC',
            'Radiology',
            'Admin 7th',
            'Admin',
            'EDP'
        ];
        view()->share('departments', $departments);
        view()->share('title', 'Vehicle Details');
    }

    public function index()
    {
        $vehicles = Vehicle::latest()->paginate(10);
        $drivers = Employee::where('type', 'DRIVER')->pluck('id', 'name');
        $user = Auth::user();
        return view('back.vehicles', compact('vehicles', 'user', 'drivers'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $requestData = $request->all();
        $requestData['user_id'] = auth()->id();
        $requestData['created_at'] = now();

        $vehicle = Vehicle::create($requestData);

        $documents = [
            'fitness' => [
                'from' => null,
                'to' => $request->fitness_expiry,
                'file' => $request->file('fitness_file'),
            ],
            'insurance' => [
                'from' => $request->insurance_from,
                'to' => $request->insurance_expiry,
                'file' => $request->file('insurance_file'),
            ],
            'emission' => [
                'from' => $request->emission_from,
                'to' => $request->emission_expiry,
                'file' => $request->file('emission_file'),
            ],
            'permit' => [
                'from' => $request->permit_from,
                'to' => $request->permit_expiry,
                'file' => $request->file('permit_file'),
            ],
            'tax' => [
                'from' => $request->tax_from,
                'to' => $request->tax_expiry,
                'file' => $request->file('tax_file'),
            ],
        ];

        foreach ($documents as $type => $data) {
            if ($data['from'] || $data['to'] || $data['file']) {
                $path = null;

                if ($data['file']) {
                    $path = $data['file']->store("vehicle_docs", 'public');
                }

                DB::table('vehicle_docs')->insert([
                    'vehicle_id' => $vehicle->id,
                    'doc_type' => $type,
                    'from_date' => $data['from'],
                    'to_date' => $data['to'],
                    'doc_file' => $path,
                    'user_id' => auth()->id()
                ]);
            }
        }

        return redirect()->route('vehicles.index')->with('success', 'Vehicle added successfully.');
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $vehicle->update($request->all());

        $documents = [
            'fitness' => [
                'from' => null,
                'to' => $request->fitness_expiry,
                'file' => $request->file('fitness_file'),
            ],
            'insurance' => [
                'from' => $request->insurance_from,
                'to' => $request->insurance_expiry,
                'file' => $request->file('insurance_file'),
            ],
            'emission' => [
                'from' => $request->emission_from,
                'to' => $request->emission_expiry,
                'file' => $request->file('emission_file'),
            ],
            'permit' => [
                'from' => $request->permit_from,
                'to' => $request->permit_expiry,
                'file' => $request->file('permit_file'),
            ],
            'tax' => [
                'from' => $request->tax_from,
                'to' => $request->tax_expiry,
                'file' => $request->file('tax_file'),
            ],
        ];

        foreach ($documents as $type => $data) {
            if ($data['from'] || $data['to'] || $data['file']) {
                $existing = DB::table('vehicle_docs')
                    ->where('vehicle_id', $vehicle->id)
                    ->where('doc_type', $type)
                    ->first();

                $path = $existing->doc_file ?? null;

                if ($data['file']) {
                    // Upload new file
                    $path = $data['file']->store("vehicle_docs", 'public');

                    // Optional: delete old file
                    if ($existing && $existing->doc_file && Storage::disk('public')->exists($existing->doc_file)) {
                        Storage::disk('public')->delete($existing->doc_file);
                    }
                }

                if ($existing) {
                    // Update existing record
                    DB::table('vehicle_docs')
                        ->where('id', $existing->id)
                        ->update([
                            'from_date' => $data['from'],
                            'to_date' => $data['to'],
                            'doc_file' => $path, // new or old
                            'updated_at' => now(),
                        ]);
                } else {
                    // Insert new
                    DB::table('vehicle_docs')->insert([
                        'vehicle_id' => $vehicle->id,
                        'doc_type' => $type,
                        'from_date' => $data['from'],
                        'to_date' => $data['to'],
                        'doc_file' => $path,
                        'user_id' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Vehicle updated successfully!');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted.');
    }

    public function import(Request $request)
    {
        // Implement your import logic here
    }

    public function search(Request $request)
    {
        $term = $request->input('search');

        $results = Vehicle::where(function ($query) use ($term) {
            $query->where('model', 'like', "%{$term}%")
                ->orWhere('reg_no', 'like', "%{$term}%")
                ->orWhere('type', 'like', "%{$term}%")
                // ->orWhere('driver_id', 'like', "%{$term}%")
                ->orWhere('fuel_type', 'like', "%{$term}%");
        })->get();

        return response()->json($results);
    }

    public function getVehicleDocs($vehicleId)
    {
        $docs = DB::table('vehicle_docs')->where('vehicle_id', $vehicleId)->get();

        if ($docs->isEmpty()) {
            return response()->json(['message' => 'No records found.'], 404);
        }

        $grouped = $docs->keyBy('doc_type')->map(function ($doc) {
            return [
                'doc_file' => $doc->doc_file,
                'from_date' => $doc->from_date,
                'to_date' => $doc->to_date,
            ];
        });

        return response()->json($grouped);
    }

    // Vehicle Movement
    public function movementIndex(Request $request)
    {
        $title = "Vehicle Movements";

        if (in_array(Auth::user()->register_by, ['DRIVER'])) {
            $employee = Employee::where('employee_id', Auth::user()->username)->first();
            $drivers = Employee::where('id', $employee->id)->where('type', 'DRIVER')->pluck('id', 'name');
            $vehicles = Vehicle::where('employee_id', $employee->id)->get();
            $request->merge([
                'driver_id' => $employee->id
            ]);
            $driverId = $request->input('driver_id');
            $vehicleId = $request->input('vehicle_id');

            $tripDetails = App\Models\VehicleMovement::whereNull('vehicle_movements.deleted_at')
                ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
                ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
                ->select(
                    'vehicle_movements.*',
                    'vehicles.reg_no as vehicle_name',
                    'employees.mobile as phone'
                )
                // ->where('vehicle_movements.driver_id', $request->driver_id)
                // ->where('vehicle_movements.date', $request->date)
                ->latest()->get();
        } else {
            $drivers = Employee::where('type', 'DRIVER')->pluck('id', 'name');
            $vehicles = Vehicle::get();
            $vehicleId = $request->input('vehicle_id');
            $driverId = $request->input('driver_id');
            $tripDetails = null;
        }

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $vehicleMovements = VehicleMovement::where('status', 'end');

        if ($fromDate && $toDate) {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate = Carbon::parse($toDate)->endOfDay();
            $vehicleMovements = $vehicleMovements->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $vehicleMovements = $vehicleMovements->whereDate('date', '>=', $fromDate);
        } elseif ($toDate) {
            $vehicleMovements = $vehicleMovements->whereDate('date', '<=', $toDate);
        } else {
            $vehicleMovements->whereDate('date', '>=', Carbon::today());
        }

        if ($vehicleId) {
            $vehicleMovements = $vehicleMovements->where('vehicle_id', $vehicleId);
        }

        if ($driverId) {
            $vehicleMovements = $vehicleMovements->where('driver_id', $driverId);
        }   

        $vehicleManualMovements = (clone $vehicleMovements)
        ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
        ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
        ->select(
            'vehicle_movements.*',
            'vehicles.reg_no as vehicle_name',
            'employees.name as driver_name'
        )
        ->whereNull('vehicle_movements.allocation_id')
        ->latest()->paginate(10);

        $vehicleMovementReport = (clone $vehicleMovements)
        ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
        ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
        ->select(
            'vehicle_movements.date',
            'vehicle_movements.time_in',
            'vehicle_movements.time_out',
            'vehicle_movements.km_covered',
            'vehicle_movements.place',
            'vehicle_movements.purpose',
            'vehicle_movements.department',
            'vehicles.reg_no as vehicle_name',
            'employees.name as driver_name'
        )
        ->whereNotNull('vehicle_movements.allocation_id')
        ->get();

        $vehicleMovements = (clone $vehicleMovements)
        ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
        ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
        ->select(
            'vehicle_movements.*',
            'vehicles.reg_no as vehicle_name',
            'employees.name as driver_name'
        )
        ->whereNotNull('vehicle_movements.allocation_id')
        ->latest()->paginate(10);

        $groupedMovements = $vehicleMovements->getCollection()->groupBy('vehicle_name');
        $groupedDriverMovements = $vehicleMovements->getCollection()->groupBy('driver_name');
        $vehicleMovements->setCollection($groupedMovements->flatten());
        // dd($groupedMovements, $vehicleMovements);
        $user = Auth::user();
        if ($request->submit == 'export'){
            return Excel::download(new CustomExport($vehicleMovementReport), 'movement_report.xlsx');
        }

        return view('back.vehicle-movements', compact('tripDetails', 'vehicleMovements', 'vehicleManualMovements', 'groupedMovements', 'groupedDriverMovements', 'user', 'drivers', 'vehicles', 'title', 'request'));
    }

    public function movementStore(Request $request)
    {
        //dd($request->all());
        $requestData = $request->except('_token', 'fuel_qty', 'fuel_km_in', 'fuel_km_out', 'fuel_km_covered', 'total_km', 'mileage');
        $requestData['user_id'] = auth()->id();
        $requestData['created_at'] = now();

        $movement_id = VehicleMovement::insertGetId($requestData);

        if ($requestData['fuel_fill'] == 'Yes')
            DB::table('vehicle_fuels')->insert([
                'movement_id' => $movement_id,
                'vehicle_id' => $request->vehicle_id,
                'driver_id' => $request->driver_id,
                'date' => $request->date,
                'place' => $request->place,
                'purpose' => $request->purpose,
                'fuel_fill' => $request->fuel_fill,
                'department' => $request->department,
                'fuel_qty' => $request->fuel_qty,
                'km_in' => $request->fuel_km_in,
                'km_out' => $request->fuel_km_out,
                'km_covered' => $request->fuel_km_covered,
                'mileage' => $request->mileage,
                'user_id' => auth()->id(),
                'created_at' => now(),
            ]);

        // if (@$requestData['allocation_id']) {
        //     if ($requestData['type'] == 'transport') {
        //         TransportAllocation::where('id', $requestData['allocation_id'])->update(['movement_id' => $movement_id]);
        //     } else {
        //         AmbulanceAllocation::where('id', $requestData['allocation_id'])->update(['movement_id' => $movement_id]);
        //     }
        // }

        return redirect()->back()->with('success', 'Vehicle Movements added successfully!');
    }

    public function movementUpdate(Request $request, $id)
    {
        $requestData = $request->except('_token', '_method', 'fuel_qty', 'fuel_km_in', 'fuel_km_out', 'fuel_km_covered', 'total_km', 'mileage');

        VehicleMovement::where('id', $id)->update($requestData);

        $fuelData = DB::table('vehicle_fuels')->where('movement_id', $id)->first();

        if (!$fuelData && $request->fuel_fill === 'Yes') {
            DB::table('vehicle_fuels')->insert([
                'movement_id'  => $id,
                'vehicle_id'   => $request->vehicle_id,
                'driver_id'    => $request->driver_id,
                'date'         => $request->date,
                'place'        => $request->place,
                'purpose'      => $request->purpose,
                'fuel_fill'    => $request->fuel_fill,
                'department'   => $request->department,
                'fuel_qty'     => $request->fuel_qty,
                'km_in'        => $request->fuel_km_in,
                'km_out'       => $request->fuel_km_out,
                'km_covered'   => $request->fuel_km_covered,
                'mileage'      => $request->mileage,
                'user_id'      => auth()->id(),
                'created_at'   => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Vehicle Movements updated successfully!');
    }

    public function movementCancel(Request $request, $id)
    {
        // dd($request->all(),'dd');
        if (in_array($request->type, ['transport'])) {
            $allocation = TransportAllocation::find($id);
            $request_user_id = $allocation?->transportRequest?->user_id ?? null;
        }
        else if (in_array($request->type, ['home-health'])) {
            $allocation = TransportAllocation::find($id);
            $request_user_id = $allocation?->homeHealthRequest?->user_id ?? null;
        } else {
            $allocation = AmbulanceAllocation::find($id);
            $request_user_id = $allocation?->ambulanceRequest?->user_id ?? null;
            $tech_id = $allocation?->technician_id ?? null;
        }
        $allot_user_id = @$allocation->user_id;
        $request_user = User::where('id', @$request_user_id)->first();
        $allot_user = User::where('id', @$allot_user_id)->first();

        $user = User::find(auth()->id());
        $employee = Employee::where('employee_id', $user->username)->first();

        $requestData['status'] = 'cancel';
        $requestData['cancel_by'] = @$employee->id;
        $requestData['cancel_reason'] = @$request->cancel_reason;
        $requestData['deleted_at'] = now();

        $trip = VehicleMovement::where('type', $request->type)->where('allocation_id', $allocation->id);

        if($trip->first()->status !== 'allot') {
            return redirect()->back()->with('error', 'Cannot cancel the trip as its processed already!');            
        }
        $trip->update($requestData);
        $allocation->update(["deleted_at" => now()]);

        $driver = Employee::find($allocation->driver_id);
        $vehicle = Vehicle::find($allocation->vehicle_id);
        $user = User::where('username', $driver->employee_id)->first();

        $title = "Trip Cancelled";
        $message = "Trip has been cancelled.";
        $fcmId = @$user->fcm_id;

        // dd($title, $message, $fcmId);
        if($fcmId) NotificationController::sendNotification($title, $message, $fcmId);

        $request_user = User::find($request_user_id);
        $allot_user = User::find($allot_user_id);

        if($request_user && $request_user->id !== $user->id) $request_user->notify(new GeneralNotification(
            "Trip has been cancelled.<br/><b>Vehicle:</b> {$vehicle->reg_no} <b>Driver:</b> {$driver->name}",
            url('/transport/request')
        ));

        if($allot_user && $allot_user->id !== $user->id) $allot_user->notify(new GeneralNotification(
            "Trip has been cancelled.<br/><b>Vehicle:</b> {$vehicle->reg_no} <b>Driver:</b> {$driver->name}",
            url('/transport/request')
        ));

        if(@$tech_id) {
            $tech = Employee::find($tech_id);
            $tech_user = User::where('username', $tech['employee_id'])->first();
            if($tech_user) $tech_user->notify(new GeneralNotification(
                "Trip has been cancelled.<br/><b>Vehicle:</b> {$vehicle->reg_no} <b>Driver:</b> {$driver->name}",
                url('/transport/request')
            ));
        }

        return redirect()->back()->with('success', 'Vehicle Trip cancelled successfully!');
    }

    public function requestCancel(Request $request, $id)
    {
        // dd($request->all(),'jjj');
        if (in_array($request->type, ['transport'])) {
            $tripRequest = TransportRequest::find($id);
            $request_user_id = $tripRequest?->user_id ?? null;
        }
        else if (in_array($request->type, ['home-health'])) {
            $tripRequest = HomeHealthRequest::find($id);
            $request_user_id = $tripRequest?->user_id ?? null;
        } else {
            $tripRequest = AmbulanceRequest::find($id);
            $request_user_id = $tripRequest?->user_id ?? null;
        }
        $request_user = User::where('id', @$request_user_id)->first();

        $user = User::find(auth()->id());
        $employee = Employee::where('employee_id', $user->username)->first();

        $requestData['cancel_by'] = @$employee->id;
        $requestData['cancel_reason'] = @$request->cancel_reason;
        $requestData['deleted_at'] = now();

        $tripRequest->update($requestData);

        $request_user = User::find($request_user_id);

        if($request_user && $request_user->id !== $user->id) $request_user->notify(new GeneralNotification(
            "Trip has been cancelled.<br/><b>Type:</b> {$request->type}",
            url('/transport/request')
        ));

        return redirect()->back()->with('success', 'Vehicle Trip cancelled successfully!');
    }

    public function getVehicleMoveData($vehicleId)
    {
        $data = VehicleMovement::where('vehicle_id', $vehicleId)->latest()->first();

        if (!$data) {
            return response()->json(['message' => 'No records found.'], 404);
        }

        return response()->json($data);
    }

    // Vehicle Fuel
    public function fuelIndex(Request $request)
    {
        $title = "Fuel Consumption";
        if (in_array(Auth::user()->register_by, ['DRIVER'])) {
            $employee = Employee::where('employee_id', Auth::user()->username)->first();
            $drivers = Employee::where('id', $employee->id)->where('type', 'DRIVER')->pluck('id', 'name');
            $vehicles = Vehicle::where('employee_id', $employee->id)->get();
            $request->merge([
                'driver_id' => $employee->id
            ]);
            $driverId = $request->input('driver_id');
            $vehicleId = $request->input('vehicle_id');
        } else {
            $drivers = Employee::where('type', 'DRIVER')->pluck('id', 'name');
            $vehicles = Vehicle::get();
            $vehicleId = $request->input('vehicle_id');
            $driverId = $request->input('driver_id');
        }

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $vehicleFuels = DB::table('vehicle_fuels');

        if ($fromDate && $toDate) {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate = Carbon::parse($toDate)->endOfDay();
            $vehicleFuels = $vehicleFuels->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $vehicleFuels = $vehicleFuels->whereDate('date', '>=', $fromDate);
        } elseif ($toDate) {
            $vehicleFuels = $vehicleFuels->whereDate('date', '<=', $toDate);
        } else {
            $vehicleFuels = $vehicleFuels->whereDate('date', '>=', Carbon::today());
        }

        if ($vehicleId) {
            $vehicleFuels = $vehicleFuels->where('vehicle_id', $vehicleId);
        }

        if ($driverId) {
            $vehicleFuels = $vehicleFuels->where('driver_id', $driverId);
        }

        $vehicleFuelReport = (clone $vehicleFuels)
        ->leftJoin('vehicles', 'vehicle_fuels.vehicle_id', '=', 'vehicles.id')
        ->leftJoin('employees', 'vehicle_fuels.driver_id', '=', 'employees.id')
        ->select(
            'vehicle_fuels.date',
            'vehicle_fuels.fuel_qty',
            'vehicle_fuels.km_in',
            'vehicle_fuels.km_out',
            'vehicle_fuels.km_covered',
            'vehicle_fuels.mileage',
            'vehicles.reg_no as vehicle_name',
            'employees.name as driver_name'
        )
        ->get();

        $vehicleFuels = $vehicleFuels
            ->leftJoin('vehicles', 'vehicle_fuels.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_fuels.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_fuels.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name'
            )
            ->latest()->paginate(10);

        $groupedFuels = $vehicleFuels->getCollection()->groupBy('vehicle_name');
        $vehicleFuels->setCollection($groupedFuels->flatten());

        if ($request->submit == 'export'){
            return Excel::download(new CustomExport($vehicleFuelReport), 'fuel_report.xlsx');
        }

        $user = Auth::user();

        return view('back.vehicle-fuels', compact('vehicleFuels', 'groupedFuels', 'user', 'drivers', 'vehicles', 'title', 'request'));
    }

    public function fuelStore(Request $request)
    {
        //dd($request->all());
        $requestData = $request->except('_token');
        $requestData['user_id'] = auth()->id();
        $requestData['created_at'] = now();

        $fuel_id = DB::table('vehicle_fuels')->insertGetId($requestData);

        return redirect()->back()->with('success', 'Fuel Consumption Data added successfully!');
    }

    public function fuelUpdate(Request $request, $id)
    {
        $requestData = $request->except('_token', '_method');
        // dd($requestData);
        DB::table('vehicle_fuels')->where('id', $id)->update($requestData);

        return redirect()->back()->with('success', 'Fuel Consumption details updated successfully!');
    }

    public function getVehicleFuelData($vehicleId)
    {
        $fuel = DB::table('vehicle_fuels')
                    ->where('vehicle_id', $vehicleId)
                    ->latest()
                    ->first();

        if ($fuel) {
            return response()->json([
                'source' => 'vehicle_fuels',
                'km' => $fuel->km_out,
                'data' => $fuel
            ]);
        } else {
            $vehicle = Vehicle::find($vehicleId);

            if (!$vehicle) {
                return response()->json(['message' => 'Vehicle not found.'], 404);
            }

            return response()->json([
                'source' => 'vehicles',
                'km' => $vehicle->km_in,
                'data' => $vehicle
            ]);
        }
    }

    // Vehicle Checklist
    public function checklistIndex(Request $request)
    {
        $title = "Vehicle Checklist";
        if (in_array(Auth::user()->register_by, ['DRIVER'])) {
            $employee = Employee::where('employee_id', Auth::user()->username)->first();
            $drivers = Employee::where('id', $employee->id)->where('type', 'DRIVER')->pluck('id', 'name');
            $vehicles = Vehicle::where('employee_id', $employee->id)->get();
            $request->merge([
                'driver_id' => $employee->id
            ]);
            $driverId = $request->input('driver_id');
            $vehicleId = $request->input('vehicle_id');
        } else {
            $drivers = Employee::where('type', 'DRIVER')->pluck('id', 'name');
            $vehicles = Vehicle::get();
            $vehicleId = $request->input('vehicle_id');
            $driverId = $request->input('driver_id');
        }

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $vehicleChecklist = DB::table('vehicle_checklist');

        if ($fromDate && $toDate) {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate = Carbon::parse($toDate)->endOfDay();
            $vehicleChecklist = $vehicleChecklist->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $vehicleChecklist = $vehicleChecklist->whereDate('date', '>=', $fromDate);
        } elseif ($toDate) {
            $vehicleChecklist = $vehicleChecklist->whereDate('date', '<=', $toDate);
        } else {
            $vehicleChecklist = $vehicleChecklist->whereDate('date', '>=', Carbon::today());
        }

        if ($vehicleId) {
            $vehicleChecklist = $vehicleChecklist->where('vehicle_id', $vehicleId);
        }

        if ($driverId) {
            $vehicleChecklist = $vehicleChecklist->where('driver_id', $driverId);
        }

        $vehicleChecklist = $vehicleChecklist
            ->leftJoin('vehicles', 'vehicle_checklist.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('employees', 'vehicle_checklist.driver_id', '=', 'employees.id')
            ->select(
                'vehicle_checklist.*',
                'vehicles.reg_no as vehicle_name',
                'employees.name as driver_name'
            )
            ->latest()->paginate(10);

        $user = Auth::user();

        return view('back.vehicle-checklist', compact('vehicleChecklist', 'user', 'drivers', 'vehicles', 'title', 'request'));
    }

    public function checklistStore(Request $request)
    {
        //dd($request->all());
        $requestData = $request->except('_token');
        $requestData['inspections'] = json_encode($request->input('inspections', []));
        $requestData['user_id'] = auth()->id();
        $requestData['created_at'] = now();

        DB::table('vehicle_checklist')->insertGetId($requestData);

        return redirect()->back()->with('success', 'Checklist Data added successfully!');
    }

    public function checklistUpdate(Request $request, $id)
    {
        $requestData = $request->except('_token', '_method');
        $requestData['inspections'] = json_encode($request->input('inspections', []));
        DB::table('vehicle_checklist')->where('id', $id)->update($requestData);

        return redirect()->back()->with('success', 'Checklist details updated successfully!');
    }

    // Vehicle Job
    public function jobIndex(Request $request)
    {
        $title = "Vehicle Jobs";

        if (in_array(Auth::user()->register_by, ['DRIVER'])) {
            $employee = Employee::where('employee_id', Auth::user()->username)->first();
            $drivers = Employee::where('id', $employee->id)->where('type', 'DRIVER')->pluck('id', 'name');
            $vehicles = Vehicle::where('employee_id', $employee->id)->get();
            $request->merge([
                'driver_id' => $employee->id
            ]);
            $driverId = $request->input('driver_id');
            $vehicleId = $request->input('vehicle_id');
        } else {
            $drivers = Employee::where('type', 'DRIVER')->pluck('id', 'name');
            $vehicles = Vehicle::get();
            $driverId = $request->input('driver_id');
            $vehicleId = $request->input('vehicle_id');
        }
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $serviceType = $request->input('service_type');
        $supervisors = Employee::where('type', 'SUPERVISOR')->get();
        $vehicleIds = $vehicles->pluck('id');
        $vehicleJobs = DB::table('vehicle_jobs')->whereIn('vehicle_id', $vehicleIds);

        if ($fromDate && $toDate) {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate = Carbon::parse($toDate)->endOfDay();
            $vehicleJobs = $vehicleJobs->whereBetween('vehicle_jobs.created_at', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $vehicleJobs = $vehicleJobs->whereDate('vehicle_jobs.created_at', '>=', $fromDate);
        } elseif ($toDate) {
            $vehicleJobs = $vehicleJobs->whereDate('vehicle_jobs.created_at', '<=', $toDate);
        }

        if ($vehicleId) {
            $vehicleJobs = $vehicleJobs->where('vehicle_id', $vehicleId);
        }

        if ($serviceType) {
            $vehicleJobs = $vehicleJobs->where('service_type', $serviceType);
        }

        $query = $vehicleJobs;

        $vehicleJobReport = (clone $query)
            ->leftJoin('vehicles', 'vehicle_jobs.vehicle_id', '=', 'vehicles.id')
            ->select(
                'vehicle_jobs.*',
                'vehicles.reg_no as vehicle_names'
            )
            ->get()
            ->groupBy('vehicle_id');

        $vehicleJobRep = (clone $query)
            ->where('vehicle_jobs.approve', 1)
            ->latest()
            ->paginate(10);

        $vehicleJobReport = $vehicleJobRep->getCollection()->groupBy('vehicle_names');

        $vehicleJobs = (clone $vehicleJobs)
            ->leftJoin('vehicles', 'vehicle_jobs.vehicle_id', '=', 'vehicles.id')
            ->select(
                'vehicle_jobs.*',
                'vehicles.reg_no as vehicle_name'
            )
            ->latest()
            ->paginate(10);

        $user = Auth::user();

        return view('back.vehicle-jobs', compact('vehicleJobs', 'vehicleJobReport', 'user', 'drivers', 'supervisors', 'vehicles', 'title', 'request'));
    }

    public function jobStore(Request $request)
    {
        //dd($request->all());
        $requestData = $request->except('_token');
        $requestData['user_id'] = auth()->id();
        $requestData['created_at'] = now();

        if ($request->hasFile("estimation_doc")) {
            $file = $request->file('estimation_doc');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'uploads/service/' . $fileName;
            Storage::disk('public')->put($filePath, file_get_contents($file));
            $requestData['estimation_doc'] = $filePath;
        }

        DB::table('vehicle_jobs')->insertGetId($requestData);

        return redirect()->back()->with('success', 'Vehicle Jobs created successfully!');
    }

    public function jobUpdate(Request $request, $id)
    {
        $requestData = $request->except('_token', '_method');
        if ($request->hasFile("estimation_doc")) {
            $file = $request->file('estimation_doc');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'uploads/service/' . $fileName;
            Storage::disk('public')->put($filePath, file_get_contents($file));
            $requestData['estimation_doc'] = $filePath;
        }
        //  dd($requestData);
        DB::table('vehicle_jobs')->where('id', $id)->update($requestData);
        $data = DB::table('vehicle_jobs')->find($id);

        if($data->approve == 1) {
            $message = 'Vehicle Jobs approved successfully!';  
            $request_user = User::find($data->user_id);
            if($request_user) $request_user->notify(new GeneralNotification(
                "Service Request has been approved",
                url('/vehicle-jobs')
            ));
        }
        else {
            $message = 'Vehicle Jobs updated successfully!';
        }
        return redirect()->back()->with('success', $message);
    }

    public function getVehicleServiceData($vehicleId)
    {
        $service = DB::table('vehicle_jobs')->where('vehicle_id', $vehicleId)->latest()->get();

        if (!$service) {
            return response()->json(['message' => 'No records found.'], 404);
        }

        return response()->json($service);
    }

    public function downloadPdf($id)
    {
        //$vehicleJob = DB::table('vehicle_jobs')->find($id);
        $vehicleJob = DB::table('vehicle_jobs as vj')
            ->leftJoin('vehicles as v', 'vj.vehicle_id', '=', 'v.id')
            ->leftJoin('employees as cd', 'vj.checkout_driver', '=', 'cd.id')
            ->leftJoin('employees as cs', 'vj.checkout_supervisor', '=', 'cs.id')
            ->leftJoin('employees as cid', 'vj.checkin_driver', '=', 'cid.id')
            ->leftJoin('employees as cis', 'vj.checkin_supervisor', '=', 'cis.id')
            ->where('vj.id', $id)
            ->select(
                'vj.*',
                'v.reg_no as vehicle_reg_no',
                'v.type as vehicle_type',
                // 'v.insurance_expiry_date',
                'cd.name as checkout_driver_name',
                'cs.name as checkout_supervisor_name',
                'cid.name as checkin_driver_name',
                'cis.name as checkin_supervisor_name'
            )
            ->first();

        $html = view('pdf.vehicle_job', compact('vehicleJob'))->render();

        $dompdf = new Dompdf([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfPath = storage_path("app/public/vehicle-job-{$id}.pdf");
        file_put_contents($pdfPath, $dompdf->output());

        return response()->download($pdfPath)->deleteFileAfterSend(true);
    }

    public function vehicleSummary(Request $request, $type)
    {
        $vehicleMovements = VehicleMovement::where('status', 'end');
        $cancelledMovements = VehicleMovement::withTrashed()->where('status', 'cancel');

        $vehicleFuels = null;
        $vehicleJobs = null;

        // if ($type == 'vehicle') {
            if( $request->vehicle_id) $vehicleMovements->where('vehicle_movements.vehicle_id', $request->vehicle_id);
            if( $request->vehicle_id) $cancelledMovements->where('vehicle_movements.vehicle_id', $request->vehicle_id);

            $vehicleFuels = DB::table('vehicle_fuels')
                ->leftJoin('vehicles', 'vehicle_fuels.vehicle_id', '=', 'vehicles.id')
                ->where('vehicle_fuels.vehicle_id', $request->vehicle_id);

            $vehicleJobs = DB::table('vehicle_jobs')
                ->leftJoin('vehicles', 'vehicle_jobs.vehicle_id', '=', 'vehicles.id')
                ->where('vehicle_jobs.vehicle_id', $request->vehicle_id);
        // } else {
            if( $request->driver_id) $vehicleMovements->where('vehicle_movements.driver_id', $request->driver_id);
            if( $request->driver_id) $cancelledMovements->where('vehicle_movements.driver_id', $request->driver_id);
        // }

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate = Carbon::parse($toDate)->endOfDay();

            $vehicleMovements->whereBetween('date', [$fromDate, $toDate]);
            $cancelledMovements->whereBetween('date', [$fromDate, $toDate]);
            if ($vehicleFuels) $vehicleFuels->whereBetween('date', [$fromDate, $toDate]);
            if ($vehicleJobs) $vehicleJobs->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $fromDate = Carbon::parse($fromDate)->startOfDay();

            $vehicleMovements->whereDate('date', '>=', $fromDate);
            $cancelledMovements->whereDate('date', '>=', $fromDate);
            if ($vehicleFuels) $vehicleFuels->whereDate('date', '>=', $fromDate);
            if ($vehicleJobs) $vehicleJobs->whereDate('date', '>=', $fromDate);
        } elseif ($toDate) {
            $toDate = Carbon::parse($toDate)->endOfDay();

            $vehicleMovements->whereDate('date', '<=', $toDate);
            $cancelledMovements->whereDate('date', '<=', $toDate);
            if ($vehicleFuels) $vehicleFuels->whereDate('date', '<=', $toDate);
            if ($vehicleJobs) $vehicleJobs->whereDate('date', '<=', $toDate);
        }

        $totalHours = 0;
        $duration = '00:00';

        if ($vehicleMovements->count() > 0) {
            $vehicleMovements = (clone $vehicleMovements)->get();
            foreach ($vehicleMovements as $vehicleMovement) {
                if ($vehicleMovement->time_in && $vehicleMovement->time_out) {
                    $start = Carbon::createFromFormat('H:i', substr($vehicleMovement->time_out, 0, 5));
                    $end = Carbon::createFromFormat('H:i', substr($vehicleMovement->time_in, 0, 5));
                    
                    $diffInMinutes = $end->diffInMinutes($start);
                    $totalHours += $diffInMinutes;
                }
            }
            $duration = CarbonInterval::minutes($totalHours)->cascade()->format('%H:%I');
        } else {
            $duration = '00:00';
        }

        $vehicleSummary = [
            'totalTrips' => (clone $vehicleMovements)->count(),
            'totalKms' => (clone $vehicleMovements)->sum('km_covered'),
            'totalHours' => $duration,
            'totalFuel' => $vehicleFuels ? (clone $vehicleFuels)->sum('fuel_qty') : 0,
            'totalCancel' => $cancelledMovements->get()->count(),
            'totalService' => $vehicleJobs ? (clone $vehicleJobs)->sum('bill_amount') : 0,
        ];

        return response()->json($vehicleSummary);
    }
}
