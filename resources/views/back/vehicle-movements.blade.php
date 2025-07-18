@extends('layouts.back.index')

@section('content')

<style>
    .text-bg-success {
        color: #fff !important;
        background-color: #198754 !important;
    }

    .text-bg-danger {
        color: #fff !important;
        background-color: #dc3545 !important;
    }
	.scroll{
		height: 500px;
        overflow-y: auto;
	}
    .timeline {
        position: relative;
        max-width: 600px;
        margin: auto;
    }

    .timeline-item {
        display: flex;
        align-items: flex-start;
        position: relative;
        margin-bottom: 0px;
    }

    .circle {
        width: 60px;
        height: 30px;
        border-radius: 50%;
        background: #164966;
        color: white;
        text-align: center;
        line-height: 30px;
        font-weight: 700;
        margin-right: 10px; font-size: 12px;
    }

    .circle.bg-success { background: #198754 !important }

    .timeline-item::before {
        content: "";
        position: absolute;
        top: 30px;
        left: 9px;
        width: 3px;
        height: calc(100% - 20px);
        background: #aaa;
        z-index: 0;
    }

    .content {
        flex-grow: 1;
    }

    .content h2,
    .content h3 {
        margin: 0 0 5px;
        color: #000;
    }

    .content h5 { font-size: 1rem}

    .summary {
        padding-left: 40px;
    }
</style>

<section class="section">
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <!-- Filter Form and Action Buttons -->
            <form action="{{ route('vehicle-movements.index') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <!-- Date Range -->
                    <div class="col-md-6">
                        <label class="form-label text-secondary large-label">From Date</label>
                        <input type="date" name="from_date" class="form-control border-primary" value="{{ @$request->from_date ?? now()->format('Y-m-d') }}" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-secondary large-label">To Date</label>
                        <input type="date" name="to_date" class="form-control border-primary" value="{{ @$request->to_date ?? now()->format('Y-m-d') }}" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-secondary large-label">Vehicle</label>
                        <select name="vehicle_id" name="vehicles_id" class="form-select border-primary">
                            <option value="">All</option>
                            @foreach(@$vehicles as $vehicle)
                            <option value="{{@$vehicle->id}}" data-driver_id="{{@$vehicle->employee_id}}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>{{@$vehicle->reg_no}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-secondary large-label">Driver Name</label>
                        <select name="driver_id" id="drivers_id" class="form-select border-primary">
                            <option value="">All</option>
                            @foreach($drivers as $name => $id)
                            <option value="{{ $id }}" {{ request('driver_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 pt-4">
                    <!-- Left Side Buttons (Always Visible) -->
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">
                            Search
                        </button>
                        <button type="submit" id="export" name="submit" value="export" class="btn btn-primary">
                            Download
                        </button>
                    </div>

                    <!-- Right Side Buttons (Conditional) -->
                    @if(in_array($user->register_by, ['DRIVER']))
                    <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                        <button type="button" id="new-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vehicleModal">
                            New
                        </button>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">

            @if(in_array($user->register_by, ['DRIVER']))
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Movement Request</h5>
                </div>

                <div class="table-responsive mt-3">
                    @if(@$tripDetails->count())
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Vehicle</th>
                                <th>Destination</th>
                                <th>Request Reason</th>
                                <th>Contact No.</th>
                                <th></th>
                            </tr>

                            @forelse(@$tripDetails as $data)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse(@$data->date)->format('d/m/Y') }}</td>
                                <td>{{ $data->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', @$data->time_in)->format('g:i A') : '-' }}</td>
                                <td>{{@$data->vehicle_name}}</td>
                                <td>
                                    <strong>{{@$data->place}}</strong>
                                </td>
                                <td>{{@$data->purpose}}</td>
                                <td>{{@$data->phone}}</td>
                                <td class="text-center">
                                    @if(@$data->status == 'start')
                                    <button class="btn btn-primary view-btn"
                                        data-id="{{ $data->id }}"
                                        data-date="{{ $data->date }}"
                                        data-time_out="{{ $data->time_out }}"
                                        data-time_in="{{ $data->time_in }}"
                                        data-km_out="{{ $data->km_out }}"
                                        data-km_in="{{ $data->km_in }}"
                                        data-km_covered="{{ $data->km_covered }}"
                                        data-travel_time="{{ $data->travel_time }}"
                                        data-place="{{ $data->place }}"
                                        data-purpose="{{ $data->purpose }}"
                                        data-driver_id="{{ $data->driver_id }}"
                                        data-vehicle_id="{{ $data->vehicle_id }}"
                                        data-fuel_fill="{{ $data->fuel_fill }}"
                                        data-department="{{ $data->department }}"
                                        data-status="end"
                                        data-bs-toggle="modal" data-bs-target="#vehicleModal">
                                        End Trip
                                    </button>
                                    @elseif(@$data->status == 'allot')
                                    <form id="" action="{{ route('vehicle-movements.update', [@$data->id]) }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Are you sure?\nDo you want to start the ride?')">
                                        @csrf
                                        <input type="hidden" name="date" value="{{@$data->date}}" />
                                        <input type="hidden" name="time_in" value="{{ now()->format('H:i') }}" />
                                        @php
                                        $moveData = App\Models\VehicleMovement::where('vehicle_id', $data->vehicle_id)->where('status', 'end')->latest()->first();
                                        @endphp
                                        <input type="hidden" name="km_in" value="{{@$moveData->km_out}}" />
                                        <input type="hidden" name="status" value="start" />
                                        <input type="hidden" name="fuel_fill" value="No" />
                                        <button type="submit" class="btn btn-danger">Accept & Start</button>
                                    </form>
                                    @else
                                    <button class="btn btn-success view-btn" disabled>Completed</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No data found.</td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
            @endif
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Vehicle Wise Report</h5>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr align="center">
                                <th>Vehicle</th>
                                <th>Total Time Covered (Hrs)</th>
                                <th>Date</th>
                                <th>Total Km Covered (Km)</th>
                                <th>Trip Count</th>
                                <th>Purpose</th>
                                <th>Driver</th>
                                <th>Location</th>
                            </tr>

                            @foreach ($groupedMovements as $vehicleName => $movements)
                            @php
                                $totalKm = 0;
                                $totalMinutes = 0;
                            @endphp

                            @foreach ($movements as $movement)
                            @php
                                $km = max(0,$movement->km_out - $movement->km_in);
                                $totalKm += $km;

                                $start = $movement->time_out ? \Carbon\Carbon::createFromFormat('H:i', substr(@$movement->time_out, 0, 5)) : null;
                                $end = $movement->time_in ? \Carbon\Carbon::createFromFormat('H:i', substr(@$movement->time_in, 0, 5)) : null;

                                $diffInMinutes = $end && $start ? $end->diffInMinutes($start) : 0;
                                $totalMinutes += $diffInMinutes;

                                $duration = \Carbon\CarbonInterval::minutes($diffInMinutes)->cascade()->format('%H:%I');

                            @endphp

                            <tr align="center">
                                @if ($loop->first)
                                <td rowspan="{{ $movements->count() }}"><strong>{{ $vehicleName }}</strong></td>
                                @endif
                                <td class="ps-3">{{ $duration }} </td>
                                <td class="ps-3">{{ \Carbon\Carbon::parse($movement->date)->format('d/m/Y') }}</td>
                                <td class="ps-3">{{ $km }}</td>
                                <td class="ps-3">1</td>
                                <td class="ps-3">{{ $movement->purpose }}</td>
                                <td class="ps-3">{{ $movement->driver_name }}</td>
                                <td class="ps-3">
                                    <a class="text-danger view-btn"
                                        data-id="{{ $movement->id }}"
                                        data-date="{{ $movement->date }}"
                                        data-time_out="{{ $movement->time_out }}"
                                        data-time_in="{{ $movement->time_in }}"
                                        data-km_out="{{ $movement->km_out }}"
                                        data-km_in="{{ $movement->km_in }}"
                                        data-km_covered="{{ $movement->km_covered }}"
                                        data-travel_time="{{ $movement->travel_time }}"
                                        data-place="{{ $movement->place }}"
                                        data-purpose="{{ $movement->purpose }}"
                                        data-driver_id="{{ $movement->driver_id }}"
                                        data-vehicle_id="{{ $movement->vehicle_id }}"
                                        data-fuel_fill="{{ $movement->fuel_fill }}"
                                        data-department="{{ $movement->department }}"
                                        data-meter_image="{{ $movement->meter_image }}"
                                        data-location_details="{{ $movement->location_details }}"
                                        data-bs-toggle="modal" data-bs-target="#vehicleModal">
                                        <strong>{{ $movement->place }}</strong>
                                    </a>
                                </td>
                            </tr>
                            @endforeach

                            <tr align="center" style="background-color: #eef; color: #dc3545; font-weight: bold;">
                                <td></td>
                                @php
                                $totalDuration = \Carbon\CarbonInterval::minutes($totalMinutes)->cascade()->format('%H:%I');
                                @endphp
                                <td>{{ $totalDuration }} </td>
                                <td></td>
                                <td>{{ $totalKm }}</td>
                                <td>{{ $movements->count() }}</td>
                                <td colspan=3></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Driver Wise Report</h5>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr align="center">
                                <th>Driver Name</th>
                                <th>Total Time Covered (Hrs)</th>
                                <th>Date</th>
                                <th>Total Km Covered (Km)</th>
                                <th>Trip Count</th>
                                <th>Purpose</th>
                                <th>Vehicle</th>
                                <th>Location</th>
                            </tr>

                            @foreach ($groupedDriverMovements as $driverName => $movements)
                            @php
                                $totalKm = 0;
                                $totalMinutes = 0;
                                @endphp

                                @foreach ($movements as $movement)
                                @php
                                $km =  max(0,$movement->km_out - $movement->km_in);
                                $totalKm += $km;

                                $start = $movement->time_out ? \Carbon\Carbon::createFromFormat('H:i', substr(@$movement->time_out, 0, 5)) : null;
                                $end = $movement->time_in ? \Carbon\Carbon::createFromFormat('H:i', substr(@$movement->time_in, 0, 5)) : null;

                                $diffInMinutes = $end && $start ? $end->diffInMinutes($start) : 0;
                                $totalMinutes += $diffInMinutes;

                                $duration = \Carbon\CarbonInterval::minutes($diffInMinutes)->cascade()->format('%H:%I');
                            @endphp

                            <tr align="center">
                                @if ($loop->first)
                                <td rowspan="{{ $movements->count() }}"><strong>{{ $driverName }}</strong></td>
                                @endif
                                <td class="ps-3">{{ $duration }} </td>
                                <td class="ps-3">{{ \Carbon\Carbon::parse($movement->date)->format('d/m/Y') }}</td>
                                <td class="ps-3">{{ $km }}</td>
                                <td class="ps-3">1</td>
                                <td class="ps-3">{{ $movement->purpose }}</td>
                                <td class="ps-3">{{ $movement->vehicle_name }}</td>
                                <td class="ps-3">
                                    <a class="text-danger view-btn"
                                        data-id="{{ $movement->id }}"
                                        data-date="{{ $movement->date }}"
                                        data-time_out="{{ $movement->time_out }}"
                                        data-time_in="{{ $movement->time_in }}"
                                        data-km_out="{{ $movement->km_out }}"
                                        data-km_in="{{ $movement->km_in }}"
                                        data-km_covered="{{ $movement->km_covered }}"
                                        data-travel_time="{{ $movement->travel_time }}"
                                        data-place="{{ $movement->place }}"
                                        data-purpose="{{ $movement->purpose }}"
                                        data-driver_id="{{ $movement->driver_id }}"
                                        data-vehicle_id="{{ $movement->vehicle_id }}"
                                        data-fuel_fill="{{ $movement->fuel_fill }}"
                                        data-department="{{ $movement->department }}"
                                        data-meter_image="{{ $movement->meter_image }}"
                                        data-location_details="{{ $movement->location_details }}"
                                        data-bs-toggle="modal" data-bs-target="#vehicleModal">
                                        <strong>{{ $movement->place }}</strong>
                                    </a>
                                </td>
                            </tr>
                            @endforeach

                            <tr align="center" style="background-color: #eef; color: #dc3545; font-weight: bold;">
                                <td></td>
                                @php
                                $totalDuration = \Carbon\CarbonInterval::minutes($totalMinutes)->cascade()->format('%H:%I');
                                @endphp
                                <td>{{ $totalDuration }} </td>
                                <td></td>
                                <td>{{ $totalKm }}</td>
                                <td>{{ $movements->count() }}</td>
                                <td colspan=3></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row g-3 align-items-center mb-4">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Complete Report</h5>
                </div>
                <!-- Search Input -->
                <!-- <div class="col-12">
                    <input type="text" id="searchInput" class="form-control border-primary" placeholder="Search">
                </div> -->
                <!-- Vehicle Table -->
                <div class="table-responsive">
                    <table class="table table-borderless data-table">
                        <thead>
                            <tr style="color: white;">
                                <th class="ps-3 py-2">Date</th>
                                <th class="ps-3 py-2">Time Out</th>
                                <th class="ps-3 py-2">Time In</th>
                                <th class="ps-3 py-2">Km Covered</th>
                                <th class="ps-3 py-2">Place</th>
                                <th class="ps-3 py-2">Purpose</th>
                                <th class="ps-3 py-2">Vehicle No</th>
                                <th class="ps-3 py-2">Driver</th>
                                <th class="ps-3 py-2"></th>
                            </tr>
                        </thead>

                        <tbody id="vehicleTableBody">
                            @forelse($vehicleMovements->whereNotNull('allocation_id') as $dataRow)
                            <tr class="border-bottom align-middle">
                                <td class="ps-3">{{ \Carbon\Carbon::parse($dataRow->date)->format('d/m/Y') }}</td>
                                <td>{{ $dataRow->time_out ? \Carbon\Carbon::createFromFormat('H:i:s', @$dataRow->time_out)->format('g:i A') : '-' }}</td>
                                <td>{{ $dataRow->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', @$dataRow->time_in)->format('g:i A') : '-' }}</td>
                                <td class="ps-3">{{ $dataRow->km_out - $dataRow->km_in }}</td>
                                <td class="ps-3">{{ $dataRow->place }}</td>
                                <td class="ps-3">{{ $dataRow->purpose }}</td>
                                <td class="ps-3">{{ $dataRow->vehicle_name }}</td>
                                <td class="ps-3">{{ $dataRow->driver_name }}</td>
                                <td class="ps-3">
                                    <button type="button" class="btn btn-sm btn-primary d-flex justify-content-center view-btn"
                                        data-id="{{ $dataRow->id }}"
                                        data-date="{{ $dataRow->date }}"
                                        data-time_out="{{ $dataRow->time_out }}"
                                        data-time_in="{{ $dataRow->time_in }}"
                                        data-km_out="{{ $dataRow->km_out }}"
                                        data-km_in="{{ $dataRow->km_in }}"
                                        data-km_covered="{{ $dataRow->km_covered }}"
                                        data-travel_time="{{ $dataRow->travel_time }}"
                                        data-place="{{ $dataRow->place }}"
                                        data-purpose="{{ $dataRow->purpose }}"
                                        data-driver_id="{{ $dataRow->driver_id }}"
                                        data-vehicle_id="{{ $dataRow->vehicle_id }}"
                                        data-fuel_fill="{{ $dataRow->fuel_fill }}"
                                        data-department="{{ $dataRow->department }}"
                                        data-meter_image="{{ $dataRow->meter_image }}"
                                        data-location_details="{{ $dataRow->location_details }}"
                                        data-bs-toggle="modal" data-bs-target="#vehicleModal">
                                        View
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">No vehicles found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <nav aria-label="Page navigation" class="my-0">
                    <ul class="pagination justify-content-center flex-wrap">
                        {{ $vehicleMovements->links() }}
                    </ul>
                </nav>
            </div>

            <div class="row g-3 align-items-center mb-4">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Manual Movement Report</h5>
                </div>
                <!-- Search Input -->
                <!-- <div class="col-12">
                    <input type="text" id="searchInput" class="form-control border-primary" placeholder="Search">
                </div> -->
                <!-- Vehicle Table -->
                <div class="table-responsive">
                    <table class="table table-borderless data-table">
                        <thead>
                            <tr style="color: white;">
                                <th class="ps-3 py-2">Date</th>
                                <th class="ps-3 py-2">Time Out</th>
                                <th class="ps-3 py-2">Time In</th>
                                <th class="ps-3 py-2">Km Covered</th>
                                <th class="ps-3 py-2">Place</th>
                                <th class="ps-3 py-2">Purpose</th>
                                <th class="ps-3 py-2">Vehicle No</th>
                                <th class="ps-3 py-2">Driver</th>
                                <th class="ps-3 py-2"></th>
                            </tr>
                        </thead>

                        <tbody id="vehicleTableBody">
                            @forelse($vehicleManualMovements->whereNull('allocation_id') as $dataRow)
                            <tr class="border-bottom align-middle">
                                <td class="ps-3">{{ \Carbon\Carbon::parse($dataRow->date)->format('d/m/Y') }}</td>
                                <td>{{ $dataRow->time_out ? \Carbon\Carbon::createFromFormat('H:i:s', @$dataRow->time_out)->format('g:i A') : '-' }}</td>
                                <td>{{ $dataRow->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', @$dataRow->time_in)->format('g:i A') : '-' }}</td>
                                <td class="ps-3">{{ $dataRow->km_out - $dataRow->km_in }}</td>
                                <td class="ps-3">{{ $dataRow->place }}</td>
                                <td class="ps-3">{{ $dataRow->purpose }}</td>
                                <td class="ps-3">{{ $dataRow->vehicle_name }}</td>
                                <td class="ps-3">{{ $dataRow->driver_name }}</td>
                                <td class="ps-3">
                                    <button type="button" class="btn btn-sm btn-primary d-flex justify-content-center view-btn"
                                        data-id="{{ $dataRow->id }}"
                                        data-date="{{ $dataRow->date }}"
                                        data-time_out="{{ $dataRow->time_out }}"
                                        data-time_in="{{ $dataRow->time_in }}"
                                        data-km_out="{{ $dataRow->km_out }}"
                                        data-km_in="{{ $dataRow->km_in }}"
                                        data-km_covered="{{ $dataRow->km_covered }}"
                                        data-travel_time="{{ $dataRow->travel_time }}"
                                        data-place="{{ $dataRow->place }}"
                                        data-purpose="{{ $dataRow->purpose }}"
                                        data-driver_id="{{ $dataRow->driver_id }}"
                                        data-vehicle_id="{{ $dataRow->vehicle_id }}"
                                        data-fuel_fill="{{ $dataRow->fuel_fill }}"
                                        data-department="{{ $dataRow->department }}"
                                        data-meter_image="{{ $dataRow->meter_image }}"
                                        data-location_details="{{ $dataRow->location_details }}"
                                        data-bs-toggle="modal" data-bs-target="#vehicleModal">
                                        View
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">No vehicles found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <nav aria-label="Page navigation" class="my-0">
                    <ul class="pagination justify-content-center flex-wrap">
                        {{ $vehicleManualMovements->links() }}
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Import Modal -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header py-3" style="background: #164966;">
                        <h5 class="modal-title text-white mb-0" id="modalTitles">
                            <i class="fas fa-file-import me-2"></i>Import Vehicle
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-4">
                        <form action="{{ route('vehicle-movements.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label text-secondary">Import File (.xlsx)</label>
                                <input type="file" name="file" class="form-control border-primary" required accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" / accept=".pdf,.jpg,.jpeg,.png,.svg" />
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-primary me-2" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Import Data
                                </button>
                                <a href="{{ asset('assets/import/Medlead Vehicle Import.xlsx') }}" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Sample File</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Modal -->
        <div class="modal fade" id="vehicleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="vehicleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content p-0">
                    <!-- Modal Header -->
                    <div class="modal-header py-3" style="background: #164966;">
                        <h5 class="modal-title text-white mb-0" id="modalTitle">
                            <i class="fas fa-user-md me-2"></i>{{ isset($vehicle) ? 'Vehicle Movement' : 'Vehicle Movement' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-3">
                        <form id="vehicleForm" action="{{ route('vehicle-movements.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="vehicleMovementId" name="id">
                            <input type="hidden" id="status" name="status">

                            <div class="card shadow-sm p-4">

                                <!-- Geo Location -->
                                <div class="row" id="map-section">
                                    <div class="col-md-9">
                                        <!-- <label class="form-label text-secondary mb-2">Geo Location</label> -->
                                        <div id="map" class="map-container" style="height: 500px;"></div>
                                    </div>
                                    <div class="col-md-3">
										<div class="scroll">
                                            <div class="timeline"></div>
										</div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label>Date</label>
                                        <input type="date" id="date" name="date" class="form-control" value="{{ now()->toDateString() }}" readOnly required />
                                    </div>

                                    <div class="col-md-4">
                                        <label>Driver</label>
                                        <select
                                            class="form-select"
                                            id="driver_id" name="driver_id"
                                            aria-label="Default select example" required>
                                            <option value="">Select Driver</option>
                                            @foreach($drivers as $name => $id)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Vehicle</label>
                                        <select
                                            class="form-select"
                                            id="vehicle_id" name="vehicle_id"
                                            aria-label="Default select example" required>
                                            <option value="">Select Vehicle</option>
                                            @foreach(@$vehicles as $vehicle)
                                            <option value="{{@$vehicle->id}}" data-driver_id="{{@$vehicle->employee_id}}">{{@$vehicle->reg_no}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label>Time In</label>
                                        <input type="time" id="time_in" name="time_in" class="form-control" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label>Km In</label>
                                        <input type="number" id="km_in" name="km_in" class="form-control" readOnly required />
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label>Time Out</label>
                                        <input type="time" id="time_out" name="time_out" class="form-control" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label>Km Out</label>
                                        <input type="number" id="km_out" name="km_out" class="form-control" required />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label>Km Covered</label>
                                        <input type="number" id="km_covered" name="km_covered" class="form-control" readOnly required />
                                    </div>
                                    <div class="col-md-6">
                                        <label>Travel Time (Hrs)</label>
                                        <input type="text" id="travel_time" name="travel_time" class="form-control" readOnly required />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label>Place / Visit</label>
                                        <input type="text" id="place" name="place" class="form-control" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label>Purpose</label>
                                        <input type="text" id="purpose" name="purpose" class="form-control" required />
                                    </div>
                                    <!-- <div class="col-md-1">
                                        <button type="button" class="btn btn-success  mt-4 text-center" data-bs-toggle="collapse" href="#collapseExample" aria-expanded="true">
                                            <i class="bi bi-plus-circle-fill"></i>
                                        </button>
                                    </div> -->
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <label>Fuel Consumption</label>
                                        <select
                                            class="form-select"
                                            id="fuel_fill" name="fuel_fill"
                                            aria-label="Default select example">
                                            <option value=""></option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <label>Department</label>
                                        <input type="text" name="department" id="department" class="form-control" />
                                        <!-- <select
                                            class="form-select"
                                            aria-label="Default select example" name="department" id="department">
                                            <option value="">Select</option>
                                            @foreach(@$departments as $dept)
                                            <option value="{{@$dept}}">{{@$dept}}</option>
                                            @endforeach
                                        </select> -->
                                    </div>
                                    <div class="col-md-12 mt-3" id="image">
                                        <label>Image</label><br/>
                                        <img id="vehicleImage" src="" alt="Vehicle Image" class="img-fluid" style="max-height: 200px; max-width: 100%;" />
                                    </div>
                                </div>

                                <div class="Fuel box mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Petrol / Diesel Qty In Ltr</label>
                                            <input type="text" id="fuel_qty" name="fuel_qty" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <h5>Meter Reading In KMs</h5>
                                        <div class="col-md-6">
                                            <label>Opening</label>
                                            <input type="text" id="fuel_km_in" name="fuel_km_in" class="form-control" value="0" readOnly />
                                        </div>
                                        <div class="col-md-6">
                                            <label>Closing</label>
                                            <input type="text" id="fuel_km_out" name="fuel_km_out" class="form-control" />
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label>Total KMs</label>
                                            <input type="number" id="fuel_km_covered" name="fuel_km_covered" class="form-control" readOnly />
                                        </div>
                                        <div class="col-md-6">
                                            <label>Average Km/Ltr (Mileage)</label>
                                            <input type="number" id="mileage" name="mileage" class="form-control" readOnly />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" id="saveChanges" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i> Submit
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#searchInput').on('keyup', function() {
            const searchQuery = $(this).val();
            fetchVehicles(searchQuery, 1); // Initial fetch with page 1
        });

        // Handle pagination click
        $(document).on('click', '.paginations a', function(e) {
            e.preventDefault();
            const page = $(this).attr('href').split('page=')[1];
            const searchQuery = $('#searchInput').val();
            fetchVehicles(searchQuery, page);
        });

        // Function to fetch vehicles
        function fetchVehicles(searchQuery, page) {
            $.ajax({
                url: '/api/search-vehicles',
                type: 'GET',
                data: {
                    search: searchQuery,
                    page: page
                },
                success: function(response) {
                    $('#vehicleTableBody').empty();

                    // Append new rows
                    response.forEach(function(vehicle) {
                        $('#vehicleTableBody').append(`
                            <tr class="border-bottom align-middle">
                                <td class="ps-3 pe-3">${vehicle.reg_no}</td>
                                <td class="ps-3 pe-3">${vehicle.type}</td>
                                <td class="ps-3 pe-3">${vehicle.reg_no}</td>
                                <td class="ps-3 pe-3">${vehicle.driver_id}</td>
                                <td class="ps-3 pe-3">${vehicle.fuel_type}</td>
                                <td class="ps-3 pe-3">
                                    <button type="button" class="btn btn-sm btn-primary d-flex justify-content-center view-btn" 
                                        data-id="${vehicle.id}" 
                                        data-model="${vehicle.reg_no}"
                                        data-type="${vehicle.type}"
                                        data-reg_no="${vehicle.reg_no}"
                                        data-driver_id="${vehicle.driver_id}"
                                        data-fuel_type="${vehicle.fuel_type}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#vehicleModal">
                                        View
                                    </button>
                                </td>
                            </tr>
                        `);
                    });

                    // Update pagination links
                    $('.pagination').html(response.links);
                }
            });
        }
    });
</script>

<script>
    $(document).ready(function() {
        $("#fuel_fill")
            .change(function() {
                $(this)
                    .find("option:selected")
                    .each(function() {
                        var optionValue = $(this).attr("value") == 'Yes' ? 'Fuel' : '';
                        if (optionValue) {
                            $(".box")
                                .not("." + optionValue)
                                .hide();
                            $("." + optionValue).show();
                        } else {
                            $(".box").hide();
                        }
                    });
            })
            .change();
    });
</script>

<script>
    $(document).ready(function() {
        $("#map-section").hide();
        $('#image').hide();
        const form = document.getElementById('vehicleForm');
        const inputs = form.querySelectorAll('input, button, select, textarea');
        $('#new-btn').on('click', function() {
            inputs.forEach(input => {
                input.disabled = false;
            });
            $('#saveChanges').show();
        });
        $(document).on('click', '.view-btn', function() {
            const id = $(this).data('id');
            const timeIn = $(this).data('time_in')?.slice(0, 5);
            const timeOut = $(this).data('time_out')?.slice(0, 5);
            var timeDiff = 0;
            if (timeIn && timeOut) {
                const start = new Date(`1970-01-01T${timeIn}`);
                const end = new Date(`1970-01-01T${timeOut}`);

                if (!isNaN(start) && !isNaN(end)) {
                    const diffMs = end - start;
                    const diffMinutes = Math.floor(diffMs / 60000);

                    const hours = Math.floor(diffMinutes / 60);
                    const minutes = diffMinutes % 60;

                    timeDiff = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:00`;                    
                }
            }

            // Fill form fields dynamically
            $('#vehicleMovementId').val(id);
            $('#date').val($(this).data('date'));
            $('#driver_id').val($(this).data('driver_id'));
            $('#vehicle_id').val($(this).data('vehicle_id'));
            $('#time_in').val(timeIn);
            $('#time_out').val(timeOut);
            $('#km_in').val($(this).data('km_in'));
            $('#km_out').val($(this).data('km_out'));
            $('#km_covered').val($(this).data('km_covered'));
            $('#travel_time').val(timeDiff || '');
            $('#place').val($(this).data('place'));
            $('#purpose').val($(this).data('purpose'));
            $('#fuel_fill').val($(this).data('fuel_fill'));
            $('#department').val($(this).data('department'));
            $('#meter_image').val($(this).data('meter_image'));
            $('#status').val($(this).data('status'));
            var meter_image = $(this).data('meter_image');
            if (meter_image) {
                $('#vehicleImage').attr('src', 'storage/meter_images/'+meter_image);
                $('#image').show();
            } else {
                $('#image').hide();
            }
            var location_details = $(this).data('location_details');
            if (location_details) {
                initMap(location_details);
                $("#map-section").show();
            } else {
                $("#map-section").hide();                
            }
            // loadVehicleDocs(id);

            // Set form action for update
            $('#vehicleForm').attr('action', '/vehicle-movements/' + id);
            $('#vehicleForm').append('<input type="hidden"  name="_method" value="POST">');
            $('#saveChanges').hide();
            $('#modalTitle').text('Vehicle Movement Information');

            // Disable inputs if not ADMIN/COORDINATOR
            @if(in_array($user['register_by'], ['ADMIN', 'SUPERVISOR']))
            $('#vehicleForm :input').prop('disabled', true);
            // $('#saveChanges').hide();
            @else
            $('#vehicleForm :input').prop('disabled', false);
            // $('#saveChanges').show();
            @endif
        });

        $('#vehicle_id').on('change', function() {
            let id = $(this).val();
            loadVehicleMoveData(id);
            loadVehicleFuelData(id);
        });

        function loadVehicleMoveData(vehicleId) {
            fetch(`/vehicle-move-data/${vehicleId}`)
                .then(response => {
                    return response.json();
                })
                .then(data => {
                    $('#km_in').val(data.km_out);
                })
                .catch(error => {
                    console.error('There was a problem fetching the vehicle docs:', error);
                    alert('Failed to load vehicle document data.');
                });
        }

        function loadVehicleFuelData(vehicleId) {
            fetch(`/vehicle-fuel-data/${vehicleId}`)
                .then(response => {
                    return response.json();
                })
                .then(data => {
                    $('#fuel_km_in').val(data.km_out ?? 0);
                })
                .catch(error => {
                    console.error('There was a problem fetching the vehicle docs:', error);
                    alert('Failed to load vehicle document data.');
                });
        }

        $('#km_out').on('input', function() {
            let km_out = parseInt($(this).val(), 10) || 0;
            let km_in = parseInt($('#km_in').val(), 10) || 0;

            let total = km_out - km_in;
            $('#km_covered').val(total);
        });

        $('#time_out, #time_in').on('input', function() {
            let timeOut = $('#time_out').val();
            let timeIn = $('#time_in').val();

            if (timeOut && timeIn) {
                let [outHour, outMin] = timeOut.split(':').map(Number);
                let [inHour, inMin] = timeIn.split(':').map(Number);

                let outTotal = outHour * 60 + outMin;
                let inTotal = inHour * 60 + inMin;

                let diff = outTotal - inTotal;
                if (diff < 0) diff = 0;

                const hours = Math.floor(diff / 60);
                const minutes = diff % 60;

                const total = `${hours} Hrs ${minutes} Mins`;

                $('#travel_time').val(total);
            } else {
                $('#travel_time').val('0');
            }
        });

        function calculateMileage() {
            let kmIn = parseInt($('#fuel_km_in').val(), 10) || 0;
            let kmOut = parseInt($('#fuel_km_out').val(), 10) || 0;
            let fuelUsed = parseFloat($('#fuel_qty').val()) || 0;

            let distance = kmOut - kmIn;
            if (distance < 0) distance = 0;

            $('#fuel_km_covered').val(distance);

            let mileage = fuelUsed > 0 ? (distance / fuelUsed).toFixed(2) : '';
            $('#mileage').val(mileage);
        }

        $('#fuel_km_in, #fuel_km_out, #fuel_qty').on('input', calculateMileage);

        function loadVehicleDocs(vehicleId) {
            fetch(`/vehicle-docs/${vehicleId}`)
                .then(response => {
                    return response.json();
                })
                .then(data => {
                    if (data.fitness) {
                        $('#fitness_expiry').val(data.fitness.to_date);
                        // $('#fitness_file').val(data.fitness.doc_file);
                        $('#fitness_link').attr('href', '/storage/' + data.fitness.doc_file);
                    }

                    if (data.insurance) {
                        $('#insurance_from').val(data.insurance.from_date);
                        $('#insurance_expiry').val(data.insurance.to_date);
                        // $('#insurance_doc').val(data.insurance.doc_file);
                        $('#insurance_link').attr('href', '/storage/' + data.insurance.doc_file);
                    }

                    if (data.emission) {
                        $('#emission_from').val(data.emission.from_date);
                        $('#emission_expiry').val(data.emission.to_date);
                        // $('#emission_doc').val(data.emission.doc_file);
                        $('#emission_link').attr('href', '/storage/' + data.emission.doc_file);
                    }

                    if (data.permit) {
                        $('#permit_from').val(data.permit.from_date);
                        $('#permit_expiry').val(data.permit.to_date);
                        // $('#permit_doc').val(data.permit.doc_file);
                        $('#permit_link').attr('href', '/storage/' + data.permit.doc_file);
                    }

                    if (data.tax) {
                        $('#tax_from').val(data.tax.from_date);
                        $('#tax_expiry').val(data.tax.to_date);
                        // $('#tax_doc').val(data.tax.doc_file);
                        $('#tax_link').attr('href', '/storage/' + data.tax.doc_file);
                    }

                })
                .catch(error => {
                    console.error('There was a problem fetching the vehicle docs:', error);
                    alert('Failed to load vehicle document data.');
                });
        }

        $('#vehicleModal').on('hidden.bs.modal', function() {
            $('#vehicleForm').trigger('reset');
            // $('#vehicleForm').attr('action',
            //     '{{ route('vehicle-movements.store') }}');
            $('#vehicleForm input[name="_method"]').remove(); // remove hidden method input
            $('#saveChanges').text('Submit');
            // $('#modalTitle').text('Vehicle Movement');
            $('#vehicleForm :input').prop('disabled', false);
            $('#saveChanges').show();
        });

        $("#type").change(function() {
            var optionValue = $(this).val();
            $("#type_name").text(optionValue + " Name");
            $("#type_address").text(optionValue + " Address");
        });

    });

    function initMap(location_input) {
        const rawCoords = location_input
            .trim()
            .split(';')
            .filter(Boolean)
            .map(line => {
                const parts = line.split(',');
                return {
                    timestamp: parseInt(parts[0]),
                    lat: parseFloat(parts[1]),
                    lng: parseFloat(parts[2]),
                    speed: parseFloat(parts[3]),
                    status: parseInt(parts[4]),
                    accuracy: parseFloat(parts[5]),
                    current_timestamp: parseInt(parts[6])
                };
            });

        const cleanCoords = rawCoords.filter((coord, i, arr) => {
            const prev = arr[i - 1];
            return (
                coord.lat !== 0 &&
                coord.lng !== 0 &&
                (!prev || coord.lat !== prev.lat || coord.lng !== prev.lng)
            );
        });

        const timeOut = $('#time_out').val();
        const timeIn = $('#time_in').val();

        const timestampIn = new Date(`1970-01-01T${timeIn}`).getTime();
        const timestampOut = new Date(`1970-01-01T${timeOut}`).getTime();

        const origin = { lat: cleanCoords[0].lat, lng: cleanCoords[0].lng };
        const destination = { lat: cleanCoords[cleanCoords.length - 1].lat, lng: cleanCoords[cleanCoords.length - 1].lng };

        const map = new google.maps.Map(document.getElementById("map"), {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            gestureHandling: 'greedy'
        });

        const bounds = new google.maps.LatLngBounds();
        cleanCoords.forEach(coord => bounds.extend({ lat: coord.lat, lng: coord.lng }));
        map.fitBounds(bounds);

        const routePath = new google.maps.Polyline({
            path: cleanCoords.map(coord => ({ lat: coord.lat, lng: coord.lng })),
            geodesic: true,
            strokeColor: "#007BFF",
            strokeOpacity: 0.8,
            strokeWeight: 4
        });
        routePath.setMap(map);

        const geocoder = new google.maps.Geocoder();

        function getAddress(lat, lng, element) {
            geocoder.geocode({ location: { lat, lng } }, (results, status) => {
                if (status === "OK" && results[0]) {
                    element.textContent = results[0].formatted_address;
                } else {
                    element.textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                }
            });
        }

        const timelineContainer = document.querySelector(".timeline");
        timelineContainer.innerHTML = "";

        const formatTime = (ts) => {
            const d = new Date(ts);
            return d.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        };

        const formatDuration = (ms) => {
            const totalMinutes = Math.floor(ms / 60000);
            const hours = Math.floor(totalMinutes / 60);
            const minutes = totalMinutes % 60;
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:00 `;
        };

        new google.maps.Marker({
            position: origin,
            map: map,
            title: "Start Point",
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 8,
                fillColor: "#198754",
                fillOpacity: 1,
                strokeColor: "#fff",
                strokeWeight: 1
            },
            label: {
                text: "S",
                color: "#FFFFFF",
                fontWeight: "bold"
            }
        });

        new google.maps.Marker({
            position: destination,
            map: map,
            title: "End Point",
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 8,
                fillColor: "#dc3545",
                fillOpacity: 1,
                strokeColor: "#fff",
                strokeWeight: 1
            },
            label: {
                text: "E",
                color: "#FFFFFF",
                fontWeight: "bold"
            }
        });

        const startItem = document.createElement("div");
        startItem.classList.add("timeline-item");
        startItem.innerHTML = `
            <div class="circle bg-success text-white">Start</div>
            <div class="content">
                <p><strong><span id="addr-start">Loading...</span></strong></p>
                <p>Time: <strong><span class="text-success font-bold">${formatTime(timestampIn)}</span></strong></p>
            </div>
        `;
        timelineContainer.appendChild(startItem);
        getAddress(origin.lat, origin.lng, document.getElementById("addr-start"));

        let totalHaltDuration = 0;
        let haltCount = 0;

        for (let i = 0; i < cleanCoords.length - 1; i++) {
            const point = cleanCoords[i];
            const nextPoint = cleanCoords[i + 1];

            if (point.status === 0) {
                const haltDuration = nextPoint.timestamp - point.timestamp;
                totalHaltDuration += haltDuration;
                haltCount++;

                new google.maps.Marker({
                    position: { lat: point.lat, lng: point.lng },
                    map: map,
                    title: "Halt Point",
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: "#ffc107",
                        fillOpacity: 1,
                        strokeWeight: 1,
                        strokeColor: "#000"
                    },
                    label: {
                        text: "H",
                        color: "#000000",
                        fontWeight: "bold"
                    }
                });

                const haltItem = document.createElement("div");
                haltItem.classList.add("timeline-item");
                const haltId = `addr-halt-${i}`;
                haltItem.innerHTML = `
                    <div class="circle bg-warning text-dark mt-2">Stop</div>
                    <div class="content">
                        <p><strong><span id="${haltId}">Loading...</span></strong></p>
                        <p>Halt Start: <span class="text-success font-bold">${formatTime(point.timestamp)}</span><br/>
                        Halt End: <span class="text-success font-bold">${formatTime(nextPoint.timestamp)}</span><br/>
                        Duration: <span class="text-danger font-bold">${formatDuration(haltDuration)}</span></p>
                    </div>
                `;
                timelineContainer.appendChild(haltItem);
                getAddress(point.lat, point.lng, document.getElementById(haltId));
            }
        }

        const endItem = document.createElement("div");
        endItem.classList.add("timeline-item");
        endItem.innerHTML = `
            <div class="circle bg-danger text-white">End</div>
            <div class="content">
                <p><strong><span id="addr-end">Loading...</span></strong></p>
                <p>Time: <strong><span class="text-success font-bold">${formatTime(timestampOut)}</span></strong></p>
            </div>
        `;
        timelineContainer.appendChild(endItem);
        getAddress(destination.lat, destination.lng, document.getElementById("addr-end"));

        const totalTripTime = timestampOut - timestampIn;
        const summary = document.createElement("div");
        summary.classList.add("summary", "mt-3");
        summary.innerHTML = `
            <p><strong>Trip Duration: </strong><span class="badge text-bg-success">${formatDuration(totalTripTime)}</span></p>
            <p><strong>Halt Duration: </strong><span class="badge text-bg-success">${formatDuration(totalHaltDuration)}</span></p>
            <p><strong>Total Stops: </strong><span class="badge text-bg-danger">${haltCount}</span></p>
        `;
        timelineContainer.appendChild(summary);
    }

    // window.onload = initMap;
</script>

<script>
    $(document).ready(function() {
        $('#vehicle_id, #vehicles_id').on('change', function() {
            var driverId = $(this).find(':selected').data('driver_id');
            $('#driver_id, #drivers_id').val(driverId || '');
        });
    });
</script>

<!-- <script>
    function initMap() {
        // Raw GPS data string
        const input = `
      1747763199424,12.9128458,74.8337592,0,0.0,82.5,1747763200135;
      1747838763020,12.9128971,74.8336479,0,0.0,13.506,1747838763256;
      1747838764000,12.9129540,74.8335200,0,0.0,9.0,1747838764500;
      1747838764800,12.9131000,74.8333000,0,0.0,7.0,1747838765000;
    `;

        // Parse into coordinates
        const coords = input
            .trim()
            .split(';')
            .filter(Boolean)
            .map(line => {
                const parts = line.split(',');
                return {
                    lat: parseFloat(parts[1]),
                    lng: parseFloat(parts[2])
                };
            });

        if (coords.length < 2) {
            alert("Need at least two points for directions.");
            return;
        }

        // Set origin, destination, and waypoints
        const origin = coords[0];
        const destination = coords[coords.length - 1];
        const waypoints = coords.slice(1, -1).map(coord => ({
            location: coord,
            stopover: true
        }));

        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: origin,
        });

        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer({
            map: map
        });

        const request = {
            origin: origin,
            destination: destination,
            waypoints: waypoints,
            optimizeWaypoints: true,
            travelMode: 'DRIVING',
        };

        directionsService.route(request, (result, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);
            } else {
                alert('Directions request failed due to ' + status);
            }
        });
    }

    // Auto-run the function
    window.onload = initMap;
</script> -->

<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&v=beta&libraries=marker&map_ids={{ config('services.google_maps.map_id') }}"
    async defer>
</script>

@endsection