@extends('layouts.back.index')

@section('content')
<style>
    .scroll {
        height: 275px;
        overflow-y: scroll;
    }

    canvas {
        max-width: 100%;

    }

    .bg-dark {
        background-color: #dfdfdf !important;
    }

    .space {
        margin-top: 28px;
    }
</style>

<section class="section">
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <!-- Filter Form and Action Buttons -->
            <form action="{{ route('ambulance.report') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <!-- Date Range -->
                    <div class="col-md-6">
                        <label>From Date</label>
                        <input type="date" name="from_date" class="form-control border-primary" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label>To Date</label>
                        <input type="date" name="to_date" class="form-control border-primary" value="{{ request('to_date') }}">
                    </div>
                    <!-- <div class="col-md-6 mt-3">
                        <label>Ambulance Type</label>
                        <select
                            class="form-select"
                            aria-label="Default select example">
                            <option value="1">Select</option>
                            <option value="2">All</option>
                            <option value="3">In-house</option>
                            <option value="4">Tied-Up</option>
                        </select>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Status</label>
                        <select
                            class="form-select"
                            aria-label="Default select example">
                            <option value="1">Select</option>
                            <option value="2">Approved</option>
                            <option value="3">Pending</option>
                        </select>
                    </div> -->
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 pt-4">
                    <!-- Left Side Buttons (Always Visible) -->
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">
                            Search
                        </button>
                        <!-- <button type="submit" name="submit" value="export" class="btn btn-primary">
                            Download
                        </button> -->
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm p-4">

        <div class="row d-flex justify-content-center">
            <div class="col-md-12 d-flex justify-content-center mb-4">
                <button class="btn btn-primary">
                    <h5 class="text-white mb-0">Total Ambulance Booked : {{ @$ambulanceHelpDeskDetails->count() + @$ambulanceHelpDeskExtDetails->count() + @$ambulanceWardDetails->count() + @$ambulanceWardExtDetails->count() }}</h5>
                </button>
            </div>

            <div class="row mt-3">
                <h6 class="text-white mb-0">Ambulance Booked (Help Desk): {{@$ambulanceHelpDeskDetails->count() + @$ambulanceHelpDeskExtDetails->count()}}</h6>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> </h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="ambulanceHelpDesk" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="ambulanceHelpDesk" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="ambulanceHelpDesk">Download</button>
                        </div>
                    </div>
                    <div class="chart-container" id="ambulanceHelpDeskGraph">
                        <canvas id="ambulanceHelpDeskChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="ambulanceHelpDeskLegend"></div>
                    <div class="table-container d-none mt-2" id="ambulanceHelpDeskTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Ambulance Type</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Trip</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive scroll">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>In-House Ambulance : {{@$ambulanceHelpDeskDetails->count()}}</th>
                                <th>Date</th>
                                <th>Total Visit</th>
                                <th>Location</th>
                            </tr>
                            @forelse ($ambulanceHelpDeskGrouped as $ambulanceType => $requests)

                            <!-- <tr>
                                <td rowspan="{{@$requests->count()}}">{{ $ambulanceType }}</td>
                            </tr>
                            <tr> -->
                            @foreach ($requests as $index=>$row)
                            <tr>
                                @if ($index === 0)
                                <td rowspan="{{@$requests->count()}}">{{ $ambulanceType }}</td>
                                @endif
                                <td>{{ \Carbon\Carbon::parse($row->booking_date)->format('d/m/Y') }}</td>
                                @if ($index === 0)
                                <td rowspan="{{@$requests->count()}}">{{@$requests->count()}}</td>
                                @endif
                                <td>{{ $row->destination }}</td>
                            </tr>

                            @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No data found.</td>
                            </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> </h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="ambulanceHelpDeskExt" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="ambulanceHelpDeskExt" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="ambulanceHelpDeskExt">Download</button>
                        </div>
                    </div>
                    <div class="chart-container" id="ambulanceHelpDeskExtGraph">
                        <canvas id="ambulanceHelpDeskExtChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="ambulanceHelpDeskExtLegend"></div>
                    <div class="table-container d-none mt-2" id="ambulanceHelpDeskExtTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Ambulance Type</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Trip</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive scroll">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>External Ambulance : {{@$ambulanceHelpDeskExtDetails->count()}}</th>
                                <th>Date</th>
                                <th>Total Visit</th>
                                <th>Location</th>
                            </tr>
                            @forelse ($ambulanceHelpDeskExtGrouped as $ambulanceType => $requests)

                            <!-- <tr>
                                <td rowspan="{{@$requests->count()}}">{{ $ambulanceType }}</td>
                            </tr>
                            <tr> -->
                            @foreach ($requests as $index=>$row)
                            <tr>
                                @if ($index === 0)
                                <td rowspan="{{@$requests->count()}}">{{ $ambulanceType }}</td>
                                @endif
                                <td>{{ \Carbon\Carbon::parse($row->booking_date)->format('d/m/Y') }}</td>
                                @if ($index === 0)
                                <td rowspan="{{@$requests->count()}}">{{@$requests->count()}}</td>
                                @endif
                                <td>{{ $row->destination }}</td>
                            </tr>

                            @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No data found.</td>
                            </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <h6 class="text-white mb-0">Ambulance Booked (Ward): {{ @$ambulanceWardDetails->count() + @$ambulanceWardExtDetails->count() }}</h6>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> </h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="ambulanceWard" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="ambulanceWard" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="ambulanceWard">Download</button>
                        </div>
                    </div>
                    <div class="chart-container" id="ambulanceWardGraph">
                        <canvas id="ambulanceWardChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="ambulanceWardLegend"></div>
                    <div class="table-container d-none mt-2" id="ambulanceWardTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Ambulance Type</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Trip</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive scroll">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>In-House Ambulance : {{@$ambulanceWardDetails->count()}}</th>
                                <th>Date</th>
                                <th>Total Visit</th>
                                <th>Location</th>
                            </tr>
                            @forelse ($ambulanceWardGrouped as $ambulanceType => $requests)

                            <!-- <tr>
                                <td rowspan="{{@$requests->count()}}">{{ $ambulanceType }}</td>
                            </tr>
                            <tr> -->
                            @foreach ($requests as $index=>$row)
                            <tr>
                                @if ($index === 0)
                                <td rowspan="{{@$requests->count()}}">{{ $ambulanceType }}</td>
                                @endif
                                <td>{{ \Carbon\Carbon::parse($row->booking_date)->format('d/m/Y') }}</td>
                                @if ($index === 0)
                                <td rowspan="{{@$requests->count()}}">{{@$requests->count()}}</td>
                                @endif
                                <td>{{ $row->destination }}</td>
                            </tr>

                            @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No data found.</td>
                            </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>

            <!-- <div class="row">
                <h6 class="text-white mb-0">External Ambulance: {{@$ambulanceWardExtDetails->count()}}</h6>
            </div> -->

            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> </h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="ambulanceWardExt" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="ambulanceWardExt" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="ambulanceWardExt">Download</button>
                        </div>
                    </div>
                    <div class="chart-container" id="ambulanceWardExtGraph">
                        <canvas id="ambulanceWardExtChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="ambulanceWardExtLegend"></div>
                    <div class="table-container d-none mt-2" id="ambulanceWardExtTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Ambulance Type</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Trip</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive scroll">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>External Ambulance : {{@$ambulanceWardExtDetails->count()}}</th>
                                <th>Date</th>
                                <th>Total Visit</th>
                                <th>Location</th>
                            </tr>
                            @forelse ($ambulanceWardExtGrouped as $ambulanceType => $requests)

                            <!-- <tr>
                                <td rowspan="{{@$requests->count()}}">{{ $ambulanceType }}</td>
                            </tr>
                            <tr> -->
                            @foreach ($requests as $index=>$row)
                            <tr>
                                @if ($index === 0)
                                <td rowspan="{{@$requests->count()}}">{{ $ambulanceType }}</td>
                                @endif
                                <td>{{ \Carbon\Carbon::parse($row->booking_date)->format('d/m/Y') }}</td>
                                @if ($index === 0)
                                <td rowspan="{{@$requests->count()}}">{{@$requests->count()}}</td>
                                @endif
                                <td>{{ $row->destination }}</td>
                            </tr>

                            @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No data found.</td>
                            </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>

            <!-- <div class="col-md-6 text-center bg-dark p-2"><button class="btn btn-secondary">Help Desk Booking</button>

<div class="row mt-3">
    <div class="col-md-6"><button class="btn btn-success">In-House</button><br><button class="btn btn-primary mt-2">100</button><br><button class="btn btn-primary mt-2">Udupi : 80 <br> Puttur : 20</button></div>
    <div class="col-md-6"><button class="btn btn-success">Tied-Up</button><br><button class="btn btn-primary mt-2">0</button></div>
</div>

    </div>
     <div class="col-md-6 text-center bg-light p-2"><button class="btn btn-secondary">Ward Booking</button>

<div class="row mt-3">
    <div class="col-md-6"><button class="btn btn-success">In-House</button><br><button class="btn btn-primary mt-2">50</button><br><button class="btn btn-primary mt-2">Udupi : 30 <br> Puttur : 20</button></div>
    <div class="col-md-6"><button class="btn btn-success">Tied-Up</button><br><button class="btn btn-primary mt-2">20</button><br><button class="btn btn-primary mt-2">Sai Ambulance : 10 <br> IS Ambulance : 10</button></div>
</div>
     </div> -->

        </div>
    </div>

    <!-- <div
        class="modal fade"
        id="ambulanceModal"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        tabindex="-1"
        aria-labelledby="ambulanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content p-0">
                <div class="modal-header py-3" style="background: #164966">
                    <h5 class="modal-title text-white mb-0" id="modalTitle">
                        <i class="fas fa-user-md me-2"></i>Ambulance Request
                    </h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-3">
                    <form
                        id="requestForm"
                        action="{{ route('ambulance.store') }}"
                        method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="id" name="id" />
                        <input type="hidden" id="type" name="type" value="{{@$type}}" />
                        <input type="hidden" id="form_date" name="form_date" value="{{ now()->toDateString() }}" readOnly required />

                        @if(@$type == 'ward')
                        <input type="hidden" id="accept" name="accept" value="0" />
                        <div class="card shadow-sm p-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="patientName">Patient Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="patient_name"
                                        id="patient_name"
                                        value="" />
                                </div>
                                <div class="col-md-4">
                                    <label for="mrNo">MR No.</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="mr_no"
                                        id="mr_no"
                                        value="" />
                                </div>
                                <div class="col-md-4">
                                    <label for="formDate">Contact No.</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        name="contact_no"
                                        id="contact_no"
                                        value="" required />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <h5>Ambulance Transport Required on</h5>
                                <div class="col-md-6">
                                    <label for="requiredDate">Date</label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        name="booking_date"
                                        id="booking_date"
                                        value="" />
                                </div>
                                <div class="col-md-6">
                                    <label for="requiredTime">Time</label>
                                    <input
                                        type="time"
                                        class="form-control"
                                        name="booking_time"
                                        id="booking_time"
                                        value="" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="consultant">Treating Consultant</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="consultant"
                                        id="consultant"
                                        value="" />
                                </div>
                                <div class="col-md-6">
                                    <label for="ward">Ward/ICU</label>
                                    <select name="ward" id="ward" class="form-select ">
                                        <option value="">Select</option>
                                        @foreach(['Ward', 'ICU'] as $typeData)
                                        <option value="{{ $typeData }}" {{ request('ward') == $typeData ? 'selected' : '' }}>{{ $typeData }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="dropAddress">Drop Destination</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="destination"
                                        id="destination"
                                        value="" />
                                </div>
                                <div class="col-md-6">
                                    <label for="patient_type">Type</label>
                                    <select name="patient_type" id="patient_type" class="form-select ">
                                        <option value="">Select</option>
                                        @foreach(['Stable', 'Unstable', 'Deadbody'] as $typeData)
                                        <option value="{{ $typeData }}" {{ request('patient_type') == $typeData ? 'selected' : '' }}>{{ $typeData }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="requester_name">Person Requesting for Ambulance</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="requester_name"
                                        id="requester_name"
                                        value="" />
                                </div>
                                <div class="col-md-6">
                                    <label for="requester_relation">Relation to Patient</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="requester_relation"
                                        id="requester_relation"
                                        value="" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="staffName">Name of PRE/Staff Nurse/Hospital Supervisor</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="staff_name"
                                        id="staff_name"
                                        value="" />
                                </div>
                                <div class="col-md-6">
                                    <label for="ambulanceType">Ambulance Type</label>
                                    <select name="ambulance_type" id="ambulance_type" class="form-select ">
                                        <option value="">Select</option>
                                        @foreach(['Ward', 'ICU'] as $typeData)
                                        <option value="{{ $typeData }}" {{ request('ambulance_type') == $typeData ? 'selected' : '' }}>{{ $typeData }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3 d-flex justify-content-center">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success px-5" id="submit-btn">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                        @else
                        <input type="hidden" id="accept" name="accept" value="1" />
                        <input type="hidden" id="form_date" name="form_date" value="{{ now()->toDateString() }}" readOnly required />
                        <div class="card shadow-sm p-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="patientName">Name of called patient</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="requester_name"
                                        id="requester_name"
                                        value="" />
                                </div>
                                <div class="col-md-4">
                                    <label for="mrNo">Relationship with patient</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="requester_relation"
                                        id="requester_relation"
                                        value="" />
                                </div>
                                <div class="col-md-4">
                                    <label for="formDate">Date</label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        name="booking_date"
                                        id="booking_date"
                                        value="{{ now()->toDateString() }}" readOnly required />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="patientName">Patient Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="patient_name"
                                        id="patient_name"
                                        value="" />
                                </div>
                                <div class="col-md-4">
                                    <label for="patientName">Age</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        name="patient_age"
                                        id="patient_age"
                                        value="" />
                                </div>
                                <div class="col-md-4">
                                    <label for="patientName">Sex</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="patient_sex"
                                        id="patient_sex"
                                        value="" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="consultant">Request From</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="from_location"
                                        id="from_location"
                                        value="" />
                                </div>
                                <div class="col-md-4">
                                    <label for="patient_location">Location of Patient</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="patient_location"
                                        id="patient_location"
                                        value="" />
                                </div>
                                <div class="col-md-4">
                                    <label for="reason">Reason for Request</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="reason"
                                        id="reason"
                                        value="" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label>Technician Allotted</label>
                                    <select class="form-select" name="technician_allotted" id="technician">
                                        <option value="">-- Select --</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <div class="box" id="">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Name</label>
                                                <input class="form-control" type="text" name="technician_name" />
                                            </div>
                                            <div class="col-md-6">
                                                <label>Phone</label>
                                                <input class="form-control" type="text" name="technician_phone" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3 d-flex justify-content-center">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success px-5" id="submit-btn">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div> -->

    <!-- <div
        class="modal fade"
        id="allocateModal"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        tabindex="-1"
        aria-labelledby="allocateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content p-0">
                <div class="modal-header py-3" style="background: #164966">
                    <h5 class="modal-title text-white mb-0" id="modalTitle">
                        <i class="fas fa-user-md me-2"></i>Allocate Ambulance
                    </h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-3">
                    <form id="allocateForm" action="{{ route('ambulance.allocate') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="ambulance_request_id" name="ambulance_request_id" value="{{ $requestId ?? '' }}" />

                        @if(@$type== 'ward')
                        <div class="card shadow-sm p-4">
                            <h5 class="title mt-3">Inhouse Ambulance</h5>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Received at ED/Admission Desk by (Name)</label>
                                    <input type="text" class="form-control" name="received_by" />
                                </div>
                                <div class="col-md-6">
                                    <label>Time</label>
                                    <input type="time" class="form-control" name="received_time" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Hospital Supervisor Name</label>
                                    <select id="supervisor_id" name="supervisor_id" class="form-select ">
                                        <option value="">Select</option>
                                        @foreach($supervisors as $sup)
                                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Ambulance Arranged</label>
                                    <select class="form-select" name="ambulance_arranged">
                                        <option value="">Select</option>
                                        <option value="External Ambulance ICU">External Ambulance ICU</option>
                                        <option value="External Ambulance Normal">External Ambulance Normal</option>
                                        <option value="Hospital ICU Ambulance">Hospital ICU Ambulance</option>
                                        <option value="Hospital Normal Ambulance">Hospital Normal Ambulance</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="patientName">Patient Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="a_patient_name"
                                        value="" readOnly />
                                </div>
                                <div class="col-md-6">
                                    <label for="mrNo">MR No.</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="a_mr_no"
                                        value="" readOnly />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Ambulance Type</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="a_ambulance_type"
                                        value="" readOnly />
                                </div>
                                <div class="col-md-6">
                                    <label>Contact No.</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="a_contact_no"
                                        value="" readOnly />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Place</label>
                                    <input type="text" class="form-control" name="place" id="place" readonly />
                                </div>
                                <div class="col-md-3">
                                    <label>Km</label>
                                    <input type="number" class="form-control" name="km" />
                                </div>
                                <div class="col-md-3">
                                    <label>Total Cost</label>
                                    <input type="number" class="form-control" name="total_cost" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label for="confirmed">Confirm</label>
                                    <select id="confirmed" class="form-select" name="confirmed" required>
                                        <option value="">-- Select --</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <div id="confirmedYes">
                                        <div class="row mt-3">
                                            <h5 class="title">Allot Ambulance</h5>
                                            <div class="col-md-6">
                                                <label>Vehicle</label>
                                                <select class="form-select" aria-label="Default select example" name="vehicle_id" id="vehicle_id">
                                                    <option value="">Select</option>
                                                    @foreach(@$vehicles as $vehicle)
                                                    <option value="{{@$vehicle->id}}" data-driver_id="{{@$vehicle->employee_id}}">{{@$vehicle->reg_no}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Driver Name</label>
                                                <select class="form-select" aria-label="Default select example" name="driver_id" id="driver_id">
                                                    <option value="">Select</option>
                                                    @foreach(@$drivers as $driver=>$id)
                                                    <option value="{{@$id}}">{{@$driver}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>
                                    </div>

                                    <div id="confirmedNo">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Reason</label>
                                                <input class="form-control" type="text" name="no_reason" />
                                            </div>
                                            <div class="col-md-6">
                                                <label>Allot Tied-up Ambulance</label>
                                                <select class="form-select" name="tied_up_ambulance">
                                                    <option value="">-- Select --</option>
                                                    <option value="1">Ambulance 1</option>
                                                    <option value="2">Ambulance 2</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <h6>Point of Contact</h6>
                                            <div class="col-md-6">
                                                <label>Name</label>
                                                <input class="form-control" type="text" name="poc_name" />
                                            </div>
                                            <div class="col-md-6">
                                                <label>Phone</label>
                                                <input class="form-control" type="text" name="poc_phone" />
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row mt-3" id="allot-footer">
                                        <div class="col-md-6">
                                            <label>Technician Allotted</label>
                                            <select class="form-select" name="technician_allotted" id="technician">
                                                <option value="">-- Select --</option>
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="box" id="technicianYes">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Name</label>
                                                        <input class="form-control" type="text" name="technician_name" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Phone</label>
                                                        <input class="form-control" type="text" name="technician_phone" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <button type="submit" id="allot-btn" class="btn btn-md btn-primary">Allot Ambulance</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card shadow-sm p-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="patientName">Name of called patient</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name=""
                                        id="h_requester_name"
                                        value="" readOnly />
                                </div>
                                <div class="col-md-4">
                                    <label for="mrNo">Relationship with patient</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name=""
                                        id="h_requester_relation"
                                        value="" readOnly />
                                </div>
                                <div class="col-md-4">
                                    <label for="formDate">Date</label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        name=""
                                        id="h_form_date"
                                        value="{{ now()->toDateString() }}" readOnly required />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="patientName">Patient Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name=""
                                        id="h_patient_name"
                                        value="" readOnly />
                                </div>
                                <div class="col-md-4">
                                    <label for="patientName">Age</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        name=""
                                        id="h_patient_age"
                                        value="" readOnly />
                                </div>
                                <div class="col-md-4">
                                    <label for="patientName">Sex</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name=""
                                        id="h_patient_sex"
                                        value="" readOnly />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="consultant">Request From</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name=""
                                        id="h_from_location"
                                        value="" readOnly />
                                </div>
                                <div class="col-md-4">
                                    <label for="patient_location">Location of Patient</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name=""
                                        id="h_patient_location"
                                        value="" readOnly />
                                </div>
                                <div class="col-md-4">
                                    <label for="reason">Reason for Request</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name=""
                                        id="h_reason"
                                        value="" readOnly />
                                </div>
                            </div>

                            <div class="row mt-3">

                                <div class="col-md-8">
                                    <div class="box" id="">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Technician Name</label>
                                                <input class="form-control" type="text" name="" id="h_technician_name" readOnly />
                                            </div>
                                            <div class="col-md-6">
                                                <label>Technician Phone</label>
                                                <input class="form-control" type="text" name="" id="h_technician_phone" readOnly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <h5 class="title">INFORMATION DESK / EMERGENCY DEPARTMENT</h5>
                                <div class="col-md-4">
                                    <label>Name of person receiving call</label>
                                    <input class="form-control" type="text" name="receiving_person" id="receiving_person" />
                                </div>
                                <div class="col-md-4">
                                    <label>Time of Receiving call</label>
                                    <input class="form-control" type="time" name="time_receive_call" id="time_receive_call" />
                                </div>
                                <div class="col-md-4">
                                    <label>Time of departure of ambulance to site</label>
                                    <input class="form-control" type="time" name="time_departure" id="time_departure" />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label>Time of reaching the location</label>
                                    <input class="form-control" type="time" name="time_reaching" id="time_reaching" />
                                </div>
                                <div class="col-md-4">
                                    <label>Time of reaching back casualty</label>
                                    <input type="text" class="form-control" name="time_reaching_back" id="time_reaching_back" />
                                </div>
                                <div class="col-md-4">
                                    <label>Nature of illness of the patients</label>
                                    <input type="text" class="form-control" name="nature_of_illness" id="nature_of_illness" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Type of Care Given</label>
                                    <input type="text" class="form-control" name="type_of_care" id="type_of_care" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <h5 class="title">Allot Ambulance</h5>
                                <div class="col-md-6">
                                    <label>Vehicle</label>
                                    <select class="form-select" aria-label="Default select example" name="vehicle_id" id="vehicle_id">
                                        <option value="">Select</option>
                                        @foreach(@$vehicles as $vehicle)
                                        <option value="{{@$vehicle->id}}" data-driver_id="{{@$vehicle->employee_id}}">{{@$vehicle->reg_no}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Driver Name</label>
                                    <select class="form-select" aria-label="Default select example" name="driver_id" id="driver_id">
                                        <option value="">Select</option>
                                        @foreach(@$drivers as $driver=>$id)
                                        <option value="{{@$id}}">{{@$driver}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <div class="row mt-3 d-flex justify-content-center">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success px-5" id="submit-btn">
                                        Submit
                                    </button>
                                   
                                </div>
                            </div>
                        </div>
                        @endif
                    </form>


                </div>
            </div>
        </div>
    </div> -->

    @endsection

    @section('script')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Register plugins
            Chart.register(ChartDataLabels);

            const graphColors = [
                '#A3D8FF', '#D4A5A5', '#FFABAB', '#4CAF50', '#D8BFD8', '#C7CEEA', '#FFDDC1', '#A8E6CF', '#FFD3B6', '#74B9FF', '#FF9AA2', '#B5EAD7', '#FEC3A6', '#85C1E9', '#F9EBEA'
            ];

            const ambulanceHelpDeskReport = @json(@$ambulanceHelpDeskChartData);
            const ambulanceHelpDeskExtReport = @json(@$ambulanceHelpDeskExtChartData);
            const ambulanceWardReport = @json(@$ambulanceWardChartData);
            const ambulanceWardExtReport = @json(@$ambulanceWardExtChartData);

            const ambulanceHelpDeskChart = initBarChart('ambulanceHelpDeskChart', ambulanceHelpDeskReport, [
                'rgba(255, 99, 132, 0.6)', // Red
                'rgba(54, 162, 235, 0.6)', // Blue
                'rgba(255, 206, 86, 0.6)', // Yellow
                'rgba(75, 192, 192, 0.6)', // Green
                'rgba(153, 102, 255, 0.6)', // Purple
                'rgba(255, 159, 64, 0.6)' // Orange
            ], 'ambulanceHelpDeskLegend', 'In-house');
            const ambulanceHelpDeskExtChart = initBarChart('ambulanceHelpDeskExtChart', ambulanceHelpDeskExtReport, [
                'rgba(255, 99, 132, 0.6)', // Red
                'rgba(54, 162, 235, 0.6)', // Blue
                'rgba(255, 206, 86, 0.6)', // Yellow
                'rgba(75, 192, 192, 0.6)', // Green
                'rgba(153, 102, 255, 0.6)', // Purple
                'rgba(255, 159, 64, 0.6)' // Orange
            ], 'ambulanceHelpDeskExtLegend', 'External');

            const ambulanceWardChart = initBarChart('ambulanceWardChart', ambulanceWardReport, [
                'rgba(255, 99, 132, 0.6)', // Red
                'rgba(54, 162, 235, 0.6)', // Blue
                'rgba(255, 206, 86, 0.6)', // Yellow
                'rgba(75, 192, 192, 0.6)', // Green
                'rgba(153, 102, 255, 0.6)', // Purple
                'rgba(255, 159, 64, 0.6)' // Orange
            ], 'ambulanceWardLegend', 'In-house');
            const ambulanceWardExtChart = initBarChart('ambulanceWardExtChart', ambulanceWardExtReport, [
                'rgba(255, 99, 132, 0.6)', // Red
                'rgba(54, 162, 235, 0.6)', // Blue
                'rgba(255, 206, 86, 0.6)', // Yellow
                'rgba(75, 192, 192, 0.6)', // Green
                'rgba(153, 102, 255, 0.6)', // Purple
                'rgba(255, 159, 64, 0.6)' // Orange
            ], 'ambulanceWardExtLegend', 'External');
            // const paymentTypeChart = initDoughnutChart('paymentTypeChart', @json(@$paymentWiseReport), graphColors.slice(1, 5), 'paymentWiseLegend', 'Kms');
            // const areaWiseChart = initHorizontalBarChart('areaWiseChart', @json(@$areaWiseReport), graphColors[7], 'areaWiseLegend');

            // Initialize tables with data
            initTables();

            function initBarChart(chartId, data, color, legendId, measure) {
                const ctx = document.getElementById(chartId).getContext('2d');
                const labels = Object.keys(data);
                const values = Object.values(data);

                // createLegend(legendId, labels, values, color, measure);

                return new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Patient Count',
                            data: values,
                            backgroundColor: color,
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            //     padding: {
                            //         left: 15,
                            //         right: 15,
                            //         top: 15,
                            //         bottom: 15
                            //     }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label} : ${context.raw} ${measure}`;
                                    }
                                }
                            },
                            datalabels: {
                                color: '#396A7D',
                                anchor: 'end',
                                align: 'top',
                                formatter: (value) => value,
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Total Visit',
                                    color: '#666',
                                    font: {
                                        size: 14,
                                        weight: 'bold'
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: `${measure} Ambulance`,
                                    color: '#666',
                                    font: {
                                        size: 14,
                                        weight: 'bold'
                                    }
                                }
                            }
                        },
                        onClick: function(event, elements) {
                            if (elements.length) {
                                const index = elements[0].index;
                                const exData = labels[index];
                                const url = 'get-ambulance-data';
                                let type;
                                switch (chartId) {
                                    case 'ambulanceHelpDeskChart':
                                        type = 'driver';
                                        break;

                                    default:
                                        type = 'default';
                                }
                                // getTableData(exData, type, url);
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            }

            // Toggle Functionality
            document.querySelectorAll('.toggle-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');
                    const name = this.getAttribute('name');

                    // Hide both graph and table containers
                    document.querySelectorAll(`#${target}Graph, #${target}Table`).forEach(el => el.classList.add('d-none'));

                    // Also hide/show the legend if it exists
                    const legend = document.getElementById(`${target}Legend`);
                    if (legend) {
                        if (name === "graph") {
                            legend.classList.remove('d-none');
                        } else {
                            legend.classList.add('d-none');
                        }
                    }

                    // Show the corresponding container based on the clicked button
                    if (name === "graph") {
                        document.getElementById(`${target}Graph`).classList.remove('d-none');
                    } else {
                        document.getElementById(`${target}Table`).classList.remove('d-none');
                    }

                    // Update button active states
                    document.querySelectorAll(`.toggle-btn[data-target="${target}"]`).forEach(btn => {
                        btn.classList.remove('btn-primary', 'active');
                        btn.classList.add('btn-secondary');
                    });
                    this.classList.remove('btn-secondary');
                    this.classList.add('btn-primary', 'active');
                });
            });

            // Download functionality
            document.querySelectorAll('.download-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    const target = this.getAttribute('data-target');
                    const isGraphVisible = !document.getElementById(`${target}Graph`).classList.contains('d-none');

                    if (isGraphVisible) {
                        await downloadChart(target);
                    } else {
                        downloadTable(target);
                    }
                });
            });

            async function downloadChart(target) {
                let chartId;
                // Map targets to correct chart IDs
                switch (target) {
                    case 'ambulanceHelpDesk':
                        chartId = 'ambulanceHelpDeskChart';
                        break;
                    case 'ambulanceHelpDeskExt':
                        chartId = 'ambulanceHelpDeskExtChart';
                        break;
                    case 'ambulanceWard':
                        chartId = 'ambulanceWardChart';
                        break;
                    case 'ambulanceWardExt':
                        chartId = 'ambulanceWardExtChart';
                        break;
                    default:
                        console.error('Unknown target:', target);
                        return;
                }

                const canvas = document.getElementById(chartId);
                if (!canvas) {
                    alert('Chart not available for download');
                    return;
                }

                const legend = document.getElementById(`${target}Legend`);

                // Create container for capture
                const container = document.createElement('div');
                container.style.position = 'fixed';
                container.style.left = '0';
                container.style.top = '0';
                container.style.zIndex = '99999';
                container.style.backgroundColor = 'white';
                container.style.padding = '20px';
                container.style.display = 'flex';
                container.style.flexDirection = 'column';
                container.style.gap = '20px';

                // Clone canvas
                const chartClone = document.createElement('canvas');
                chartClone.width = canvas.width;
                chartClone.height = canvas.height;
                const ctx = chartClone.getContext('2d');

                // Draw white background
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, chartClone.width, chartClone.height);
                ctx.drawImage(canvas, 0, 0);
                container.appendChild(chartClone);

                // Clone legend if exists
                if (legend) {
                    const legendClone = legend.cloneNode(true);

                    // Reset legend styles for full expansion
                    legendClone.style.display = 'block';
                    legendClone.style.backgroundColor = 'white';
                    legendClone.style.padding = '10px';
                    legendClone.style.marginTop = '0';
                    legendClone.style.maxHeight = 'none';
                    legendClone.style.overflow = 'visible';
                    legendClone.style.width = '100%';

                    // Reset child element styles
                    const items = legendClone.querySelectorAll('div');
                    items.forEach(item => {
                        item.style.whiteSpace = 'normal';
                        item.style.overflow = 'visible';
                        item.style.textOverflow = 'clip';
                        item.style.maxWidth = 'none';
                        item.style.margin = '2px 0';
                    });

                    container.appendChild(legendClone);
                }

                document.body.appendChild(container);

                try {
                    const canvasImage = await html2canvas(container, {
                        scale: 2,
                        logging: false,
                        useCORS: true,
                        allowTaint: true,
                        scrollX: 0,
                        scrollY: -window.scrollY, // Fix for scroll position issues
                        onclone: (clonedDoc) => {
                            // Ensure cloned elements maintain correct styles
                            const clonedContainer = clonedDoc.getElementById(container.id);
                            if (clonedContainer) {
                                clonedContainer.style.maxWidth = '100vw';
                                clonedContainer.style.overflow = 'visible';
                            }
                        }
                    });

                    canvasImage.toBlob(blob => {
                        saveAs(blob, `${target}_report.png`);
                    }, 'image/png', 1);

                } catch (error) {
                    console.error('Error generating chart:', error);
                    alert('Error downloading chart. Please try again.');
                } finally {
                    document.body.removeChild(container);
                }
            }

            function createLegend(legendId, labels, values, colors, measure) {
                const legendContainer = document.getElementById(legendId);
                if (!legendContainer) return;

                legendContainer.innerHTML = '';

                labels.forEach((label, index) => {
                    const legendItem = document.createElement('div');
                    legendItem.style.display = 'flex';
                    legendItem.style.alignItems = 'center';
                    legendItem.style.marginBottom = '5px';

                    const colorBox = document.createElement('div');
                    colorBox.className = 'legend-color-box';
                    colorBox.style.backgroundColor = colors[index % colors.length];

                    const labelText = document.createElement('span');
                    // Show full label information in legend
                    labelText.textContent = `${label}: ${values[index]} ${measure}`;

                    legendItem.appendChild(colorBox);
                    legendItem.appendChild(labelText);
                    legendContainer.appendChild(legendItem);
                });
            }

            function downloadTable(target) {
                let table;
                switch (target) {
                    case 'ambulanceHelpDesk':
                        table = document.querySelector('#ambulanceHelpDeskTable table');
                        break;
                    case 'ambulanceHelpDeskExt':
                        table = document.querySelector('#ambulanceHelpDeskExtTable table');
                        break;
                    case 'ambulanceWard':
                        table = document.querySelector('#ambulanceWardTable table');
                        break;
                    case 'ambulanceWardExt':
                        table = document.querySelector('#ambulanceWardExtTable table');
                        break;
                    default:
                        return;
                }

                let csv = [];
                table.querySelectorAll("tr").forEach(row => {
                    let rowData = [];
                    row.querySelectorAll("th, td").forEach(col => rowData.push(col.innerText.trim()));
                    csv.push(rowData.join(","));
                });

                const csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", `${target}_data.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            function initTables() {
                const reports = {
                    'ambulanceHelpDesk': ambulanceHelpDeskReport,
                    'ambulanceHelpDeskExt': ambulanceHelpDeskExtReport,
                    'ambulanceWard': ambulanceWardReport,
                    'ambulanceWardExt': ambulanceWardExtReport
                };

                Object.entries(reports).forEach(([target, data]) => {
                    const tableBody = document.querySelector(`#${target}Table tbody`);
                    if (!tableBody) return;

                    tableBody.innerHTML = Object.entries(data)
                        .map(([label, value]) =>
                            `<tr>
                            <td style="padding: 4px 8px;">${label}</td>
                            <td style="padding: 4px 8px;">${value}</td>
                        </tr>`
                        )
                        .join('');
                });
            }

            async function getTableData(tableData, dataType, url) {
                try {
                    const params = new URLSearchParams(window.location.search);

                    // Extract values from URL
                    const type = params.get('type') || ''; // Default to empty string if not found
                    const km_covered = params.get('km_covered') || '';
                    const travel_time = params.get('travel_time') || '';
                    const fuel_qty = params.get('fuel_qty') || '';
                    const bill_amount = params.get('bill_amount') || '';

                    // Construct the request URL by including the additional parameters
                    const requestURL = `/${url}?tableData=${encodeURIComponent(tableData)}&dataType=${encodeURIComponent(dataType)}&type=${encodeURIComponent(type)}&ambulance_type=${encodeURIComponent(kmWise)}&department=${encodeURIComponent(department)}&year=${encodeURIComponent(year)}&employee_id=${encodeURIComponent(employeeId)}`;
                    const response = await fetch(requestURL);
                    // Check if the response is okay
                    if (!response.ok) {
                        throw new Error('Failed to fetch data from the server');
                    }

                    // Parse the JSON response
                    const data = await response.json();

                    // Check if there is an error (e.g., employee not found)
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    // Display the patient data in the modal
                    displayTableDataInModal(data);
                } catch (error) {
                    console.error('Error fetching data:', error);
                    alert('An error occurred while fetching data.');
                }
            }

            function displayTableDataInModal(data) {
                const tableBody = document.querySelector('#prodDataTable tbody');
                const tableHeader = document.querySelector('#prodDataTable thead');

                // Clear existing table content
                tableBody.innerHTML = '';
                tableHeader.innerHTML = '';

                // Check if there is any patient data
                if (data.tableData && data.tableData.length > 0) {
                    // Get the keys of the first patient object to dynamically generate table headers
                    const headers = Object.keys(data.tableData[0]);

                    // Create table headers dynamically based on the keys of the first patient object
                    const headerRow = document.createElement('tr');
                    headers.forEach(header => {
                        const th = document.createElement('th');
                        th.textContent = header.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
                        headerRow.appendChild(th);
                    });
                    tableHeader.appendChild(headerRow);

                    // Add each patient as a row in the table
                    data.tableData.forEach(patient => {
                        const row = document.createElement('tr');

                        // For each header (i.e., property of the patient), create a table cell
                        headers.forEach(header => {
                            const td = document.createElement('td');
                            td.textContent = patient[header] || ''; // Display the patient property value
                            row.appendChild(td);
                        });

                        tableBody.appendChild(row);
                    });

                    const modalTitle = document.getElementById('tableDataModalLabel');
                    modalTitle.textContent = `${data.title}`;

                } else {
                    // If no patients data, display a message
                    const row = document.createElement('tr');
                    const td = document.createElement('td');
                    td.colSpan = 6; // Adjust the colspan according to the number of columns
                    td.textContent = 'No patient data available.';
                    row.appendChild(td);
                    tableBody.appendChild(row);
                }

                // Show the modal using Bootstrap's Modal API
                const modal = new bootstrap.Modal(document.getElementById('tableDataModal'));
                modal.show();
            }

            // Equal height adjustment for cards
            function adjustCardHeights() {
                document.querySelectorAll('.row').forEach(row => {
                    const cards = row.querySelectorAll('.cust-card');
                    if (cards.length === 0) return;

                    const maxHeight = Math.max(...Array.from(cards).map(card => card.offsetHeight));
                    cards.forEach(card => card.style.height = `${maxHeight}px`);
                });
            }

            adjustCardHeights();
            window.addEventListener('resize', adjustCardHeights);
        });
    </script>

    @endsection