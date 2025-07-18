@extends('layouts.back.index')

@section('content')

<section class="section">
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <!-- Filter Form and Action Buttons -->
            <form action="{{ route('vehicle-jobs.index') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <!-- Date Range -->
                    <div class="col-md-6">
                        <label class="form-label text-secondary large-label">From Date</label>
                        <input type="date" name="from_date" class="form-control border-primary" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-secondary large-label">To Date</label>
                        <input type="date" name="to_date" class="form-control border-primary" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-secondary large-label">Vehicle No</label>
                        <select name="vehicle_id" class="form-select border-primary">
                            <option value="">All</option>
                            @foreach(@$vehicles as $vehicle)
                            <option value="{{@$vehicle->id}}" data-driver_id="{{@$vehicle->employee_id}}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>{{@$vehicle->reg_no}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-secondary large-label">Service Type</label>
                        <select name="service_type" class="form-select border-primary">
                            <option value="">All</option>
                            @foreach(['Regular','Repair', 'Accidental Repair'] as $typeData)
                            <option value="{{ $typeData }}" {{ request('service_type') == $typeData ? 'selected' : '' }}>{{ $typeData }}</option>
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
                        <!-- <a data-bs-toggle="modal" data-bs-target="#historyModal" data-vehicle_id="{{ $vehicle->id }}" class="btn btn-primary">
                            Download
                        </a> -->
                    </div>

                    <!-- Right Side Buttons (Conditional) -->
                    <!-- @if(in_array($user->register_by, ['DRIVER','SUPERVISOR','ADMIN'])) -->
                    <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                        <button type="button" id="new-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vehicleModal">
                            New
                        </button>
                    </div>
                    <!-- @endif -->
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">

            <div class="row g-3">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Pending Service Request</h5>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Date</th>
                                <th>Job Card No</th>
                                <th>Vehicle No</th>
                                <th>Bill Amount</th>
                                <th>Service Type</th>
                                <th>Km</th>
                                <th></th>
                            </tr>
                            @php
                            $jobsArray = in_array($user->register_by, ['DRIVER', 'SUPERVISOR'])
                            ? $vehicleJobs->where('approve', '!=', 1)
                            : $vehicleJobs->where('accept', '1')->where('approve', '!=', 1);
                            @endphp
                            @forelse($jobsArray as $dataRow)
                            <tr class="border-bottom align-middle">
                                <td class="ps-3">{{ \Carbon\Carbon::parse($dataRow->date)->format('d/m/Y') }}</td>
                                <td class="ps-3">{{ $dataRow->card_no }}</td>
                                <td class="ps-3">{{ $dataRow->vehicle_name }}</td>
                                <td class="ps-3">{{ $dataRow->bill_amount }}</td>
                                <td class="ps-3">{{ $dataRow->service_type }}</td>
                                <td class="ps-3">{{ $dataRow->km_recorded }}</td>
                                <td class="ps-3">
                                    <button type="button" class="btn btn-sm btn-primary d-flex justify-content-center view-btn"
                                        data-id="{{ $dataRow->id }}"
                                        data-vehicle_id="{{ $dataRow->vehicle_id }}"
                                        data-card_no="{{ $dataRow->card_no }}"
                                        data-date="{{ $dataRow->date }}"
                                        data-warranty="{{ $dataRow->warranty }}"
                                        data-insurance="{{ $dataRow->insurance }}"
                                        data-service_type="{{ $dataRow->service_type }}"
                                        data-service_desc="{{ $dataRow->service_desc }}"
                                        data-km_recorded="{{ $dataRow->km_recorded }}"
                                        data-checkout_driver="{{ $dataRow->checkout_driver }}"
                                        data-checkout_supervisor="{{ $dataRow->checkout_supervisor }}"
                                        data-checkout_date="{{ $dataRow->checkout_date }}"
                                        data-checkout_time="{{ $dataRow->checkout_time }}"
                                        data-gatepass_no="{{ $dataRow->gatepass_no }}"
                                        data-service_center="{{ $dataRow->service_center }}"
                                        data-contact_no="{{ $dataRow->contact_no }}"
                                        data-service_date="{{ $dataRow->service_date }}"
                                        data-estimation="{{ $dataRow->estimation }}"
                                        data-estimation_cost="{{ $dataRow->estimation_cost }}"
                                        data-estimation_doc="{{ $dataRow->estimation_doc }}"
                                        data-est_repair_time="{{ $dataRow->est_repair_time }}"
                                        data-substitute_vehicle="{{ $dataRow->substitute_vehicle }}"
                                        data-checkin_driver="{{ $dataRow->checkin_driver }}"
                                        data-checkin_supervisor="{{ $dataRow->checkin_supervisor }}"
                                        data-checkin_date="{{ $dataRow->checkin_date }}"
                                        data-checkin_time="{{ $dataRow->checkin_time }}"
                                        data-insurance_desc="{{ $dataRow->insurance_desc }}"
                                        data-insurance_amount="{{ $dataRow->insurance_amount }}"
                                        data-bill_desc="{{ $dataRow->bill_desc }}"
                                        data-bill_amount="{{ $dataRow->bill_amount }}"
                                        data-approve="{{ in_array($user->register_by, ['ADMIN']) ? 1 : 0 }}"
                                        data-bs-toggle="modal" data-bs-target="#vehicleModal">
                                        @if(in_array($user->register_by, ['DRIVER','SUPERVISOR']))
                                        Update
                                        @else
                                        Verify
                                        @endif
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No job card found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <nav aria-label="Page navigation" class="my-0">
                    <ul class="pagination justify-content-center flex-wrap">
                        {{ $vehicleJobs->links() }}
                    </ul>
                </nav>
            </div>

            <div class="row g-3 align-items-center mb-2">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Approved Service Request</h5>
                </div>
                <!-- Search Input -->
                <!-- <div class="col-12">
                    <input type="text" id="searchInput" class="form-control border-primary" fuel_qtyholder="Search">
                </div> -->
                <!-- Vehicle Table -->
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Date</th>
                                <th>Job Card No</th>
                                <th>Vehicle No</th>
                                <th>Bill Amount</th>
                                <th>Service Type</th>
                                <th>Km</th>
                                <th></th>
                            </tr>
                            @forelse($vehicleJobs->where('approve', 1) as $dataRow)
                            <tr class="border-bottom align-middle">
                                <td class="ps-3">{{ \Carbon\Carbon::parse($dataRow->date)->format('d/m/Y') }}</td>
                                <td class="ps-3">{{ $dataRow->card_no }}</td>
                                <td class="ps-3">{{ $dataRow->vehicle_name }}</td>
                                <td class="ps-3">{{ $dataRow->bill_amount }}</td>
                                <td class="ps-3">{{ $dataRow->service_type }}</td>
                                <td class="ps-3">{{ $dataRow->km_recorded }}</td>
                                <td class="ps-3 d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-sm btn-primary view-btn"
                                        data-id="{{ $dataRow->id }}"
                                        data-vehicle_id="{{ $dataRow->vehicle_id }}"
                                        data-date="{{ $dataRow->date }}"
                                        data-card_no="{{ $dataRow->card_no }}"
                                        data-bill_amount="{{ $dataRow->bill_amount }}"
                                        data-service_type="{{ $dataRow->service_type }}"
                                        data-km_recorded="{{ $dataRow->km_recorded }}"
                                        data-bs-toggle="modal" data-bs-target="#vehicleModal" disabled>
                                        Approved
                                    </button>
                                    <a href="{{ url('/vehicle-jobs/pdf/' . $dataRow->id) }}" class="btn btn-sm btn-primary">
                                        Print
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No job card found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <nav aria-label="Page navigation" class="my-0">
                    <ul class="pagination justify-content-center flex-wrap">
                        {{ $vehicleJobs->links() }}
                    </ul>
                </nav>
            </div>

            <div class="row g-3 align-items-center mb-2">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Vehicle Wise Service Report</h5>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr align="center">
                                <th rowspan="2"></th>
                                <th rowspan="2">Date</th>
                                <th rowspan="2">Total Cost in INR</th>
                                <th rowspan="2">Purpose</th>
                                <th colspan="2">Meter Reading in KMs</th>
                                <th rowspan="2">Total Km</th>
                            </tr>
                            <tr align="center">
                                <th>Opening</th>
                                <th>Closing</th>
                            </tr>

                            @foreach($vehicleJobReport as $vehicleName => $jobs)
                            @php $rowCount = count($jobs);
                            $totalBill = 0;
                            $totalKm = 0;
                            @endphp
                            @foreach($jobs as $index => $job)
                            @php
                            $totalBill += @$job->bill_amount;
                            $totalKm += @$job->km_recorded;
                            @endphp
                            <tr align="center">
                                @if($index == 0)
                                <td rowspan="{{ $rowCount }}">{{ $vehicleName }}</td>
                                @endif
                                <td>{{ \Carbon\Carbon::parse($job->created_at)->format('d/m/Y') }}</td>
                                <td>{{@$job->bill_amount}}</td>
                                <td>
                                    <a class="view-btn" data-id="{{ $job->id }}"
                                        data-vehicle_id="{{ $job->vehicle_id }}"
                                        data-card_no="{{ $job->card_no }}"
                                        data-date="{{ $job->date }}"
                                        data-warranty="{{ $job->warranty }}"
                                        data-insurance="{{ $job->insurance }}"
                                        data-service_type="{{ $job->service_type }}"
                                        data-service_desc="{{ $job->service_desc }}"
                                        data-km_recorded="{{ $job->km_recorded }}"
                                        data-checkout_driver="{{ $job->checkout_driver }}"
                                        data-checkout_supervisor="{{ $job->checkout_supervisor }}"
                                        data-checkout_date="{{ $job->checkout_date }}"
                                        data-checkout_time="{{ $job->checkout_time }}"
                                        data-gatepass_no="{{ $job->gatepass_no }}"
                                        data-service_center="{{ $job->service_center }}"
                                        data-contact_no="{{ $job->contact_no }}"
                                        data-service_date="{{ $job->service_date }}"
                                        data-estimation="{{ $job->estimation }}"
                                        data-estimation_cost="{{ $job->estimation_cost }}"
                                        data-estimation_doc="{{ $job->estimation_doc }}"
                                        data-est_repair_time="{{ $job->est_repair_time }}"
                                        data-substitute_vehicle="{{ $job->substitute_vehicle }}"
                                        data-checkin_driver="{{ $job->checkin_driver }}"
                                        data-checkin_supervisor="{{ $job->checkin_supervisor }}"
                                        data-checkin_date="{{ $job->checkin_date }}"
                                        data-checkin_time="{{ $job->checkin_time }}"
                                        data-insurance_desc="{{ $job->insurance_desc }}"
                                        data-insurance_amount="{{ $job->insurance_amount }}"
                                        data-bill_desc="{{ $job->bill_desc }}"
                                        data-bill_amount="{{ $job->bill_amount }}"
                                        data-view="yes"
                                        data-bs-toggle="modal" data-bs-target="#vehicleModal" style="text-decoration: underline; color: blue">
                                        {{@$job->service_desc}}
                                    </a>
                                </td>
                                <td>-</td>
                                <td>-</td>
                                <td>{{@$job->km_recorded}}</td>
                            </tr>
                            @endforeach
                            <tr align="center">
                                <td></td>
                                <td></td>
                                <td><b class="text-danger">{{@$totalBill}}</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b class="text-danger">{{@$totalKm}}</b></td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Service History Modal -->
        @foreach ($vehicles as $vehicle)
        <div class="modal fade" id="historyModal{{ $vehicle->id }}" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header py-3" style="background: #164966;">
                        <h5 class="modal-title text-white mb-0" id="modalTitles">
                            <i class="fas fa-file-history me-2"></i>Service History of {{ $vehicle->reg_no }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-4">

                        <!-- Search Input -->
                        <!-- <div class="col-12">
                    <input type="text" id="searchInput" class="form-control border-primary" fuel_qtyholder="Search">
                </div> -->
                        <!-- Vehicle Table -->
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th>Date</th>
                                        <th>Job Card No</th>
                                        <th>Vehicle No</th>
                                        <th>Bill Amount</th>
                                        <th>Service Type</th>
                                        <th>Km</th>
                                        <th></th>
                                    </tr>
                                    @forelse($vehicleJobs->where('vehicle_id', $vehicle->id)->where('approve', 1) as $dataRow)
                                    <tr class="border-bottom align-middle">
                                        <td class="ps-3">{{ \Carbon\Carbon::parse($dataRow->date)->format('d/m/Y') }}</td>
                                        <td class="ps-3">{{ $dataRow->card_no }}</td>
                                        <td class="ps-3">{{ $dataRow->vehicle_name }}</td>
                                        <td class="ps-3">{{ $dataRow->bill_amount }}</td>
                                        <td class="ps-3">{{ $dataRow->service_type }}</td>
                                        <td class="ps-3">{{ $dataRow->km_recorded }}</td>
                                        <td class="ps-3 d-flex justify-content-center gap-2">
                                            <!-- <button type="button" class="btn btn-sm btn-primary view-btn"
                                        data-id="{{ $dataRow->id }}"
                                        data-vehicle_id="{{ $dataRow->vehicle_id }}"
                                        data-date="{{ $dataRow->date }}"
                                        data-card_no="{{ $dataRow->card_no }}"
                                        data-bill_amount="{{ $dataRow->bill_amount }}"
                                        data-service_type="{{ $dataRow->service_type }}"
                                        data-km_recorded="{{ $dataRow->km_recorded }}"
                                        data-bs-toggle="modal" data-bs-target="#vehicleModal">
                                        View
                                    </button> -->
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">No job card found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <nav aria-label="Page navigation" class="my-0">
                            <ul class="pagination justify-content-center flex-wrap">
                                {{ $vehicleJobs->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

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
                        <form action="{{ route('vehicle-jobs.import') }}" method="POST" enctype="multipart/form-data">
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
                            <i class="fas fa-user-md me-2"></i>{{ isset($vehicle) ? 'Update Job Card' : 'Job Card' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-3">
                        
                        <form id="vehicleForm" action="{{ route('vehicle-jobs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="vehicleJobId" name="id">
                            <input type="hidden" id="accept" name="accept">
                            <input type="hidden" id="approve" name="approve">

                            <div class="card shadow-sm p-4">

                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Job Card No.</label>
                                        <input type="text" id="card_no" name="card_no" class="form-control" value="{{@$vehicleJobs[0]->id+1}}" readOnly required />
                                    </div>
                                    <div class="col-md-6">
                                        <label>Date</label>
                                        <input type="date" id="date" name="date" class="form-control" value="{{ now()->toDateString() }}" readOnly required />
                                    </div>
                                </div>
                                <div class="row mt-3">

                                    <div class="col-md-6">
                                        <label>Warranty</label>
                                        <select class="form-select" name="warranty" id="warranty" aria-label="Default select example" required>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>

                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Insurance</label>
                                        <select class="form-select" name="insurance" id="insurance" aria-label="Default select example" required>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label>Service Type</label>
                                        <select class="form-select" name="service_type" id="service_type" aria-label="Default select example" required>
                                            @foreach(['Regular','Repair', 'Accidental Repair'] as $typeData)
                                            <option value="{{ $typeData }}">{{ $typeData }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Description</label>
                                        <input type="text" name="service_desc" id="service_desc" class="form-control" required />
                                    </div>
                                </div>

                            </div>

                            <div class="row mt-3">
                                <h5 class="title">Vehicle Information</h5>
                            </div>
                            <div class="row">

                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Vehicle No.</label>
                                    <select
                                        class="form-select"
                                        id="vehicle_id" name="vehicle_id"
                                        aria-label="Default select example" required>
                                        <option value="">Select Vehicle</option>
                                        @foreach(@$vehicles as $vehicle)
                                        <option value="{{@$vehicle->id}}" data-type="{{ @$vehicle->type }}" data>{{@$vehicle->reg_no}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Vehicle Type</label>
                                    <input type="text" id="vehicle_type" name="" class="form-control" value="" readOnly />
                                </div>

                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Insurance Due Date</label>
                                    <input type="text" id="insurance_expiry_date" name="" class="form-control" value="" readOnly />
                                </div>
                                <div class="col-md-6">
                                    <label>KM Recorded</label>
                                    <input type="number" id="km_recorded" name="km_recorded" class="form-control" required />
                                </div>

                            </div>

                            <div class="row mt-3">
                                <h5 class="title">Vehicle Movement</h5>
                            </div>

                            <div class="row">
                                <div class="text-danger bg-light p-2 font-bold">CHECK OUT</div>
                                <div class="col-md-6">
                                    <label>Driver Name</label>
                                    <select id="checkout_driver" name="checkout_driver" class="form-select border-primary" required>
                                        <option value="">Select</option>
                                        @foreach($drivers as $name => $id)
                                        <option value="{{ $id }}" {{ request('driver_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Supervisor Name</label>
                                    <select id="checkout_supervisor" name="checkout_supervisor" class="form-select border-primary">
                                        <option value="">Select</option>
                                        @foreach($supervisors as $sup)
                                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label>Date</label>
                                    <input type="date" id="checkout_date" name="checkout_date" class="form-control" value="{{ now()->toDateString() }}" readOnly required />
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label>Time</label>
                                    <input type="time" id="checkout_time" name="checkout_time" class="form-control" value="{{ now()->format('H:i') }}" readOnly />
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label>Gate Pass No.</label>
                                    <input type="number" id="gatepass_no" name="gatepass_no" class="form-control" required />
                                </div>

                            </div>

                            <div class="row mt-3">
                                <h5 class="title">Service Center Details</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Service Centre Name</label>
                                    <input type="text" id="service_center" name="service_center" class="form-control" required />
                                </div>
                                <div class="col-md-6">
                                    <label>Contact Number</label>
                                    <input type="text" id="contact_no" name="contact_no" class="form-control" required />
                                </div>

                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Service Date</label>
                                    <input type="date" id="service_date" name="service_date" class="form-control" required />
                                </div>
                                <div class="col-md-6">
                                    <label>Last Service Date</label>
                                    <input type="date" id="last_service_date" name="last_service_date" class="form-control" value="" readOnly />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <h5 class="title">Service Details</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Estimation Provided</label>
                                    <select class="form-select" id="estimation" name="estimation" aria-label="Default select example" required>
                                        <option value=""></option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>

                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="Yes box">
                                        <label>Estimation Cost</label>
                                        <input type="number" id="estimation_cost" name="estimation_cost" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="Yes box">
                                        <label>Document</label>
                                        <a id="fileLink" class="form-label text-secondary ml-4" href="#" target="_blank">View Doc</a>
                                        <input type="file" id="estimation_doc" name="estimation_doc" class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Estimated Repair Time</label>
                                    <input type="text" id="est_repair_time" name="est_repair_time" class="form-control" required />
                                </div>
                                <div class="col-md-6">
                                    <label>Substitute Vehicle Allotted</label>
                                    <input type="text" id="substitute_vehicle" name="substitute_vehicle" class="form-control" required />
                                </div>

                            </div>
                            @if(in_array($user->register_by, ['DRIVER','SUPERVISOR']))
                            <div class="row mt-3 d-flex justify-content-center" id="history">
                                <!-- <h5 class="title">Previous Service History</h5> -->
                                <div class="col-md-6 text-center mt-3">
                                    <a id="historyBtn" data-bs-toggle="modal" data-bs-target="#historyModal" class="btn btn-danger">Service History</a>
                                    <button type="submit" name="accept" value="0" class="btn btn-success">Save</button>
                                </div>
                            </div>
                            @endif

                            <!-- <div class="row">
                                <div class="col-md-4">
                                    <label>Date of Service</label>
                                    <input type="text" class="form-control"/>
                                </div>
                                <div class="col-md-4">
                                    <label>Service Description</label>
                                    <input type="text" class="form-control"/>
                                </div>
                                <div class="col-md-4">
                                    <label>Cost</label>
                                    <input type="text" class="form-control"/>
                                </div>
                            </div> -->

                            <div class="row mt-5">
                                <div class="col-md-12">
                                    <div class="text-danger bg-light p-2 font-bold">CHECK IN</div>
                                </div>

                                <div class="col-md-6">
                                    <label>Driver Name</label>
                                    <select id="checkin_driver" name="checkin_driver" class="form-select border-primary">
                                        <option value="">Select</option>
                                        @foreach($drivers as $name => $id)
                                        <option value="{{ $id }}" {{ request('driver_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Supervisor Name</label>
                                    <select id="checkin_supervisor" name="checkin_supervisor" class="form-select border-primary">
                                        <option value="">Select</option>
                                        @foreach($supervisors as $sup)
                                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label>Date</label>
                                    <input type="date" id="checkin_date" name="checkin_date" class="form-control" value="{{ now()->toDateString() }}" readOnly />
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label>Time</label>
                                    <input type="time" id="checkin_time" name="checkin_time" class="form-control" />
                                </div>

                                <!--                
                            
                            <div class="col-md-6 mt-3">
                                <label>Gate Pass No.</label>
                                <input type="text" class="form-control"/>
                            </div> -->

                            </div>

                            <!-- <div class="row mt-3">
                                <h5 class="title">Financial Summary</h5>
                                </div> -->
                            <!-- <div class="row">
                                <h6>Parts Total</h6>
                                <div class="col-md-6">
                                    <label>Description</label>
                                    <input type="text" class="form-control"/>
                                </div>
                                <div class="col-md-6">
                                    <label>Amount</label>
                                    <input type="text" class="form-control"/>
                                </div>
                            
                            </div> -->
                            <!-- <div class="row mt-3">
                            <h6>Labour Total</h6>
                            <div class="col-md-6">
                                <label>Description</label>
                                <input type="text" class="form-control"/>
                            </div>
                            <div class="col-md-6">
                                <label>Amount</label>
                                <input type="text" class="form-control"/>
                            </div>
                           
                            </div>
                            <div class="row mt-3">
                                <h6>Tax</h6>
                                <div class="col-md-6">
                                    <label>Description</label>
                                    <input type="text" class="form-control"/>
                                </div>
                                <div class="col-md-6">
                                    <label>Amount</label>
                                    <input type="text" class="form-control"/>
                                </div>
                            
                            </div>
                            <div class="row mt-3">
                                <h6>G.Total</h6>
                                <div class="col-md-6">
                                    <label>Description</label>
                                    <input type="text" class="form-control"/>
                                </div>
                                <div class="col-md-6">
                                    <label>Amount</label>
                                    <input type="text" class="form-control"/>
                                </div>
                            
                            </div> -->
                            <div class="row mt-3">
                                <h6>Insurance Claim</h6>
                                <div class="col-md-6">
                                    <label>Description</label>
                                    <input type="text" id="insurance_desc" name="insurance_desc" class="form-control" />
                                </div>
                                <div class="col-md-6">
                                    <label>Amount</label>
                                    <input type="text" id="insurance_amount" name="insurance_amount" class="form-control" />
                                </div>

                            </div>
                            <div class="row mt-3">
                                <h6>Bill Details</h6>
                                <div class="col-md-6">
                                    <label>Description</label>
                                    <input type="text" id="bill_desc" name="bill_desc" class="form-control" />
                                </div>
                                <div class="col-md-6">
                                    <label>Amount</label>
                                    <input type="text" id="bill_amount" name="bill_amount" class="form-control" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12 text-center">
                                    <button type="submit" id="saveChanges" name="accept" value="1" class="btn btn-primary"
                                        onclick="return confirm('Are you sure?\nDo you want to {{ in_array($user->register_by, ['DRIVER', 'SUPERVISOR']) ? 'submit the job card?' : 'approve the job card?' }}')">
                                        @if(in_array($user->register_by, ['DRIVER','SUPERVISOR']))
                                        <i class="fas fa-save me-2"></i> Save & Send for Approval
                                        @else
                                        Confirm & Approve
                                        @endif
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
    $('#historyBtn').hide();

    $('#vehicleModal').on('submit', function(e) {
        const mobile = $('#contact_no').val();
        const isValid = /^[6-9]\d{9}$/.test(mobile);
        var $btn = $(this).find('button[type="submit"]');

        if (!isValid) {
            e.preventDefault();
            alert('Invalid mobile number');

            setTimeout(function() {
                $btn.prop('disabled', false).text('Submit');
            }, 20);
        }
    });

    $('#vehicle_id').on('change', function() {
        var id = $(this).val();
        var vehicleType = $(this).find('option:selected').data('type');
        $('#vehicle_type').val(vehicleType || '');
        if (id) {
            const targetModal = '#historyModal' + id;
            $('#historyBtn').attr('data-bs-target', targetModal);
            $('#historyBtn').show();
            loadVehicleMoveData(id);
            loadVehicleDocs(id);
            loadVehicleServiceData(id);
        }
    });

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
                    $('#insurance_expiry_date').val(data.insurance.to_date);
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

    function loadVehicleMoveData(vehicleId) {
        fetch(`/vehicle-move-data/${vehicleId}`)
            .then(response => {
                return response.json();
            })
            .then(data => {
                $('#km_recorded').val(data.km_out);
            })
            .catch(error => {
                console.error('There was a problem fetching the vehicle docs:', error);
                alert('Failed to load vehicle document data.');
            });
    }

    function loadVehicleServiceData(vehicleId) {
        fetch(`/vehicle-service-data/${vehicleId}`)
            .then(response => {
                return response.json();
            })
            .then(data => {
                $('#last_service_date').val(data.service_date);
            })
            .catch(error => {
                console.error('There was a problem fetching the vehicle docs:', error);
                alert('Failed to load vehicle document data.');
            });
    }
</script>

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
    $("select").change(function() {
        $(this).find("option:selected").each(function() {
            var optionValue = $(this).attr("value");
            if (optionValue) {
                $(".box").not("." + optionValue).hide();
                $("." + optionValue).show();
            } else {
                $(".box").hide();
            }
        });
    }).change();
</script>

<script>
    $(document).ready(function() {
        $('#saveChanges').show();
        $('#fileLink').hide();
        
        const form = document.getElementById('vehicleForm');
        const inputs = form.querySelectorAll('input, button, select, textarea');
        $('#new-btn').on('click', function() {
            inputs.forEach(input => {
                input.disabled = false;
            });
            $('#footer-btn').show();
        });
        $(document).on('click', '.view-btn', function() {
            const id = $(this).data('id');

            // Fill form fields dynamically
            $('#vehicleJobId').val(id);
            $('#vehicle_id').val($(this).data('vehicle_id'));
            $('#card_no').val($(this).data('card_no'));
            $('#date').val($(this).data('date'));
            $('#warranty').val($(this).data('warranty'));
            $('#insurance').val($(this).data('insurance'));
            $('#service_type').val($(this).data('service_type'));
            $('#service_desc').val($(this).data('service_desc'));
            $('#km_recorded').val($(this).data('km_recorded'));
            $('#checkout_driver').val($(this).data('checkout_driver'));
            $('#checkout_supervisor').val($(this).data('checkout_supervisor'));
            $('#checkout_date').val($(this).data('checkout_date'));
            $('#checkout_time').val($(this).data('checkout_time'));
            $('#gatepass_no').val($(this).data('gatepass_no'));
            $('#service_center').val($(this).data('service_center'));
            $('#contact_no').val($(this).data('contact_no'));
            $('#service_date').val($(this).data('service_date'));
            $('#estimation').val($(this).data('estimation'));
            $('#estimation_cost').val($(this).data('estimation_cost'));
            $('#est_repair_time').val($(this).data('est_repair_time'));
            $('#substitute_vehicle').val($(this).data('substitute_vehicle'));
            $('#checkin_driver').val($(this).data('checkin_driver'));
            $('#checkin_supervisor').val($(this).data('checkin_supervisor'));
            $('#checkin_date').val($(this).data('checkin_date'));
            $('#checkin_time').val($(this).data('checkin_time'));
            $('#insurance_desc').val($(this).data('insurance_desc'));
            $('#insurance_amount').val($(this).data('insurance_amount'));
            $('#bill_desc').val($(this).data('bill_desc'));
            $('#bill_amount').val($(this).data('bill_amount'));
            var view = $(this).data('view');            
            var docFile = $(this).data('estimation_doc');
            if(view == 'yes') 
            $('#saveChanges').hide();
            else 
            $('#saveChanges').show();            
             if(docFile) {
                $('#estimation_doc').hide();
                $('#fileLink').show();
                $('#fileLink').attr('href', 'storage/'+docFile);
            } else {
                $('#estimation_doc').show();
                $('#fileLink').hide();
            }
            $('#accept').val($(this).data('accept'));
            $('#approve').val($(this).data('approve'));

            // loadVehicleDocs(id);

            // Set form action for update
            $('#vehicleForm').attr('action', '/vehicle-jobs/' + id);
            //$('#saveChanges').text('Confirm & Approve');
            $('#history').hide();
            $('#modalTitle').text('Job Card');

            // Disable inputs if not ADMIN/COORDINATOR

        });

        $('#vehicleModal').on('hidden.bs.modal', function() {
            $('#vehicleForm').trigger('reset');
            $('#history').show();
            // $('#vehicleForm').attr('action',
            //     '{{ route('vehicle-jobs.store') }}');
            $('#vehicleForm input[name="_method"]').remove(); // remove hidden method input
            // $('#saveChanges').text('Save Changes');
            $('#modalTitle').text('Job Card');
            $('#vehicleForm :input').prop('disabled', false);
            $('#footer-btn').show();
        });

        $("#type").change(function() {
            var optionValue = $(this).val();
            $("#type_name").text(optionValue + " Name");
            $("#type_address").text(optionValue + " Address");
        });

    });
</script>

@endsection