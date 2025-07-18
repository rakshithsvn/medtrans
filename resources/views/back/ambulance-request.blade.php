@extends('layouts.back.index')

@section('content')

<section class="section">
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <!-- Filter Form and Action Buttons -->
            <form action="{{ route('ambulance.index', [@$type]) }}" method="POST">
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

                    <!-- Right Side Buttons (Conditional) -->
                    @if(in_array($user->register_by, ['ADMIN', 'SUPERVISOR', 'DEPARTMENT']))
                    <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                        <button type="button" id="new-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ambulanceModal">
                            New Request
                        </button>
                        <!-- 
                         -->
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm p-4">
        <!-- @if(in_array($user->register_by, ['ADMIN', 'SUPERVISOR']))
        <h5 class="text-white p-2 bg-secondary">Received Request</h5>
        <div class="row">
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped">
                    <tbody>
                        @if(@$type == 'ward')
                        <tr>
                            <th width="20%">Patient Name</th>
                            <th width="20%">MR No.</th>
                            <th width="20%">Date</th>
                            <th width="20%">Ambulance Type</th>
                            <th></th>
                        </tr>
                        @else
                        <tr>
                            <th width="20%">Patient Name</th>
                            <th width="20%">Location</th>
                            <th width="20%">Date</th>
                            <th width="20%">Request Reason</th>
                            <th></th>
                        </tr>
                        @endif
                        @forelse(@$ambulanceRequests->where('accept', '!=', '1') as $data)
                        <tr>
                            @if(@$type == 'ward')
                            <td>{{$data->patient_name}}</td>
                            <td>{{@$data->mr_no}}</td>
                            <td>{{ \Carbon\Carbon::parse(@$data->booking_date)->format('d/m/Y') }}</td>
                            <td>{{@$data->ambulance_type}}</td>
                            @else
                            <td>{{$data->patient_name}}</td>
                            <td>{{@$data->patient_location}}</td>
                            <td>{{ \Carbon\Carbon::parse(@$data->booking_date)->format('d/m/Y') }}</td>
                            <td>{{@$data->reason}}</td>
                            @endif
                            <td>
                                @if(in_array($user->register_by, ['ADMIN', 'SUPERVISOR']))
                                <a
                                    href="#"
                                    class="btn btn-success request-btn" data-bs-toggle="modal" data-bs-target="#ambulanceModal"
                                    data-id="{{ $data->id }}"
                                    data-patient_name="{{ $data->patient_name }}"
                                    data-mr_no="{{ $data->mr_no }}"
                                    data-contact_no="{{ $data->contact_no }}"
                                    data-form_date="{{ $data->form_date }}"
                                    data-booking_date="{{ $data->booking_date }}"
                                    data-booking_time="{{ $data->booking_time }}"
                                    data-consultant="{{ $data->consultant }}"
                                    data-ward="{{ $data->ward }}"
                                    data-destination="{{ $data->destination }}"
                                    data-patient_type="{{ $data->patient_type }}"
                                    data-patient_age="{{ $data->patient_age }}"
                                    data-patient_sex="{{ $data->patient_sex }}"
                                    data-requester_name="{{ $data->requester_name }}"
                                    data-requester_relation="{{ $data->requester_relation }}"
                                    data-from_location="{{ $data->from_location }}"
                                    data-patient_location="{{ $data->patient_location }}"
                                    data-reason="{{ $data->reason }}"
                                    data-staff_name="{{ $data->staff_name }}"
                                    data-ambulance_type="{{ $data->ambulance_type }}">Accept Request</a>
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
            </div>
        </div>
        @endif -->

        <!-- <div class="row mt-3">
            <h5>Date</h5>
            <div class="col-md-6">
                <label>From</label>
                <input type="date" class="form-control" />
            </div>
            <div class="col-md-6">
                <label>To</label>
                <input type="date" class="form-control" />
            </div>

            <div class="col-md-6 mt-3">
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
            </div>

            <div class="col-md-12 mt-3">
                <a href="" class="btn btn-success">Search</a>
            </div>
        </div> -->

        <div class="row mt-3">
            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">
                    Pending Requests
                </h5>
                <div class="row mt-2">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                @if(@$type == 'ward')
                                <tr>
                                    <th width="15%">Date</th>
                                    <th width="15%">Patient Name</th>
                                    <th width="15%">MR No.</th>
                                    <th width="30%">Ambulance Type</th>
                                    <th></th>
                                </tr>
                                @else
                                <tr>
                                    <th width="15%">Date</th>
                                    <th width="15%">Patient Name</th>
                                    <th width="15%">Location</th>
                                    <th width="30%">Request Reason</th>
                                    <th></th>
                                </tr>
                                @endif

                                @forelse(@$ambulanceRequests as $data)
                                <tr>
                                    @if(@$type == 'ward')
                                    <td>{{ \Carbon\Carbon::parse(@$data->booking_date)->format('d/m/Y') }}</td>
                                    <td>{{$data->patient_name}}</td>
                                    <td>{{@$data->mr_no}}</td>
                                    <td>{{@$data->ambulance_type}}</td>
                                    @else
                                    <td>{{ \Carbon\Carbon::parse(@$data->booking_date)->format('d/m/Y') }}</td>
                                    <td>{{$data->patient_name}}</td>
                                    <td>{{@$data->patient_location}}</td>
                                    <td>{{@$data->reason}}</td>
                                    @endif
                                    <td align="center">
                                        <div class="d-flex gap-2 h-10">
                                        @php
                                            $user_role = DB::table('user_roles')->where('user_id', @$user->id)->first();
                                            $role = DB::table('roles')->where('id', @$user_role->role_id)->first();
                                        @endphp
                                        <button type="button" class="btn btn-danger view-btn" data-bs-toggle="modal" data-bs-target="#ambulanceModal"
                                            data-id="{{ $data->id }}"
                                            data-update_flag="{{ $data->update_flag }}"
                                            data-type="{{ $data->type }}"
                                            data-patient_name="{{ $data->patient_name }}"
                                            data-mr_no="{{ $data->mr_no }}"
                                            data-contact_no="{{ $data->contact_no }}"
                                            data-form_date="{{ $data->form_date }}"
                                            data-booking_date="{{ $data->booking_date }}"
                                            data-booking_time="{{ $data->booking_time }}"
                                            data-consultant="{{ $data->consultant }}"
                                            data-ward="{{ $data->ward }}"
                                            data-destination="{{ $data->destination }}"
                                            data-patient_type="{{ $data->patient_type }}"
                                            data-patient_age="{{ $data->patient_age }}"
                                            data-patient_sex="{{ $data->patient_sex }}"
                                            data-requester_name="{{ $data->requester_name }}"
                                            data-requester_relation="{{ $data->requester_relation }}"
                                            data-from_location="{{ $data->from_location }}"
                                            data-patient_location="{{ $data->patient_location }}"
                                            data-reason="{{ $data->reason }}"
                                            data-staff_name="{{ $data->staff_name }}"
                                            data-ambulance_type="{{ $data->ambulance_type }}"
                                            data-request="request">
                                            View
                                        </button>
                                        @if(in_array(@$role->slug, ['admin', 'supervisor', 'helpdesk']))
                                        <button type="button" class="btn btn-danger allocate-btn" data-bs-toggle="modal" data-bs-target="#allocateModal"
                                            data-id="{{ $data->id }}"
                                            data-patient_name="{{ $data->patient_name }}"
                                            data-mr_no="{{ $data->mr_no }}"
                                            data-form_date="{{ $data->form_date }}"
                                            data-booking_date="{{ $data->booking_date }}"
                                            data-booking_time="{{ $data->booking_time }}"
                                            data-consultant="{{ $data->consultant }}"
                                            data-ward="{{ $data->ward }}"
                                            data-destination="{{ $data->destination }}"
                                            data-patient_type="{{ $data->patient_type }}"
                                            data-requester_name="{{ $data->requester_name }}"
                                            data-requester_relation="{{ $data->requester_relation }}"
                                            data-staff_name="{{ $data->staff_name }}"
                                            data-ambulance_type="{{ $data->ambulance_type }}"
                                            data-patient_age="{{ $data->patient_age }}"
                                            data-patient_sex="{{ $data->patient_sex }}"
                                            data-contact_no="{{ $data->contact_no }}"
                                            data-from_location="{{ $data->from_location }}"
                                            data-patient_location="{{ $data->patient_location }}"
                                            data-reason="{{ $data->reason }}"
                                            data-technician_name="{{ $data->technician_name }}"
                                            data-technician_phone="{{ $data->technician_phone }}"                                            
                                            data-user_id="{{ $data->user_id }}">                                           
                                            Allocate Ambulance
                                        </button>
                                        @endif   
                                        </div>                                     
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No data found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <nav aria-label="Page navigation" class="my-0">
                            {{ @$ambulanceRequests->links() }}
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">
                    Approved Requests
                </h5>

                <!-- <div class="col-md-6 mt-3">
                    <label>Ambulance</label>
                    <select
                        class="form-select"
                        aria-label="Default select example">
                        <option value="">All</option>
                        @foreach(@$ambulanceAllocations as $amb)
                        <option value="{{@$amb->vehicle->reg_no}}">{{@$amb->vehicle->reg_no}}</option>
                        @endforeach
                    </select>
                </div> -->

                <div class="row mt-3">
                    <div class="table-responsive mt-2">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                @if(@$type == 'ward')
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Patient Name</th>
                                    <th>Location</th>
                                    <th>Reason</th>
                                    <th>Contact No.</th>
                                    <th>Ambulance</th>
                                    <th>Ambulance Type</th>
                                    <th></th>
                                </tr>
                                @else
                                <tr>
                                    <th>Date</th>
                                    <th>Patient Name</th>
                                    <th>Location</th>
                                    <th>Reason</th>
                                    <th>Contact No.</th>
                                    <th>Ambulance</th>
                                    <th>Ambulance Type</th>
                                    <th></th>
                                </tr>
                                @endif

                                @forelse(@$ambulanceAllocations as $data )
                                <tr>
                                    @if(@$type == 'ward')
                                    <td>{{ \Carbon\Carbon::parse(@$data->ambulanceRequest->booking_date)->format('d/m/Y') }}</td>
                                    <td>{{ $data->ambulanceRequest->booking_time ? \Carbon\Carbon::createFromFormat('H:i:s', @$data->ambulanceRequest->booking_time)->format('g:i A') : '-' }}</td>
                                    <td>{{@$data->ambulanceRequest->patient_name}}</td>
                                    <td>{{@$data->vehicleMovement->place}}</td>
                                    <td>{{@$data->ambulanceRequest->reason}}</td>
                                    <td>{{@$data->ambulanceRequest->contact_no}}</td>
                                    <td>{{@$data->vehicle->reg_no ?? @$data->tied_up_ambulance}}</td>
                                    <td>{{@$data->ambulanceRequest->ambulance_type}}</td>
                                    @else
                                    <td>{{ \Carbon\Carbon::parse(@$data->ambulanceRequest->booking_date)->format('d/m/Y') }}</td>
                                    <td>{{@$data->ambulanceRequest->patient_name}}</td>
                                    <td>{{@$data->vehicleMovement->place}}</td>
                                    <td>{{@$data->ambulanceRequest->reason}}</td>
                                    <td>{{@$data->ambulanceRequest->contact_no}}</td>
                                    <td>{{@$data->vehicle->reg_no ?? @$data->tied_up_ambulance}}</td>
                                    <td>{{@$data->ambulance_arranged}}</td>
                                    @endif
                                    <td>
                                        <button type="button" class="btn btn-danger view-allocate-btn" data-bs-toggle="modal" data-bs-target="#allocateModal"
                                            data-id="{{ $data->id }}"
                                            data-status="{{ $data->vehicleMovement->status }}"
                                            data-patient_name="{{ $data->ambulanceRequest->patient_name }}"
                                            data-mr_no="{{ $data->ambulanceRequest->mr_no }}"
                                            data-contact_no="{{ $data->ambulanceRequest->contact_no }}"
                                            data-form_date="{{ $data->ambulanceRequest->form_date }}"
                                            data-booking_date="{{ $data->ambulanceRequest->booking_date }}"
                                            data-booking_time="{{ $data->ambulanceRequest->booking_time }}"
                                            data-consultant="{{ $data->ambulanceRequest->consultant }}"
                                            data-ward="{{ $data->ambulanceRequest->ward }}"
                                            data-destination="{{ $data->ambulanceRequest->destination }}"
                                            data-patient_type="{{ $data->ambulanceRequest->patient_type }}"
                                            data-patient_age="{{ $data->ambulanceRequest->patient_age }}"
                                            data-patient_sex="{{ $data->ambulanceRequest->patient_sex }}"
                                            data-requester_name="{{ $data->ambulanceRequest->requester_name }}"
                                            data-requester_relation="{{ $data->ambulanceRequest->requester_relation }}"
                                            data-from_location="{{ $data->ambulanceRequest->from_location }}"
                                            data-patient_location="{{ $data->ambulanceRequest->patient_location }}"
                                            data-reason="{{ $data->ambulanceRequest->reason }}"
                                            data-staff_name="{{ $data->ambulanceRequest->staff_name }}"
                                            data-ambulance_type="{{ $data->ambulanceRequest->ambulance_type }}"
                                            data-received_by="{{ $data->received_by }}"
                                            data-received_time="{{ $data->received_time }}"
                                            data-supervisor_id="{{ $data->supervisor_id }}"
                                            data-ambulance_arranged="{{ $data->ambulance_arranged }}"
                                            data-ambulance_type="{{ $data->ambulance_type }}"
                                            data-vehicle_id="{{ $data->vehicle_id }}"
                                            data-driver_id="{{ $data->driver_id }}"
                                            data-technician="{{ $data->technician_allotted }}"
                                            data-technician_id="{{ $data->technician_id }}"
                                            data-technician_phone="{{ $data->technician_phone }}"
                                            data-km="{{ $data->km }}"
                                            data-total_cost="{{ $data->total_cost }}"
                                            data-confirmed="{{ $data->confirmed }}"
                                            data-no_reason="{{ $data->no_reason }}"
                                            data-tied_up_ambulance="{{ $data->tied_up_ambulance }}"
                                            data-poc_name="{{ $data->poc_name }}"
                                            data-poc_phone="{{ $data->poc_phone }}"
                                            data-receiving_person="{{ $data->receiving_person }}"
                                            data-nature_of_illness="{{ $data->nature_of_illness }}"
                                            data-type_of_care="{{ $data->type_of_care }}"
                                            data-time_receive_call="{{ $data->time_receive_call }}"
                                            data-time_departure="{{ $data->time_departure }}"
                                            data-time_reaching="{{ $data->time_reaching }}"
                                            data-time_reaching_back="{{ $data->time_reaching_back }}"
                                            data-request="allocate">
                                            View
                                        </button>
                                    </td>
                                </tr>                             
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-3">No data found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <nav aria-label="Page navigation" class="m-0">
                            {{ @$ambulanceAllocations->links() }}
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">
                    Completed Requests
                </h5>

                <div class="row mt-3">
                    <div class="table-responsive mt-2">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                @if(@$type == 'ward')
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Patient Name</th>
                                    <th>Location</th>
                                    <th>Reason</th>
                                    <th>Contact No.</th>
                                    <th>Ambulance</th>
                                    <th>Ambulance Type</th>
                                    <th></th>
                                </tr>
                                @else
                                <tr>
                                    <th>Date</th>
                                    <th>Patient Name</th>
                                    <th>Location</th>
                                    <th>Reason</th>
                                    <th>Contact No.</th>
                                    <th>Ambulance</th>
                                    <th>Ambulance Type</th>
                                    <th></th>
                                </tr>
                                @endif

                                @forelse(@$ambulanceCompleted as $data)
                                <tr>
                                    @if(@$type == 'ward')
                                    <td>{{ \Carbon\Carbon::parse(@$data->ambulanceRequest->booking_date)->format('d/m/Y') }}</td>
                                    <td>{{ $data->ambulanceRequest->booking_time ? \Carbon\Carbon::createFromFormat('H:i:s', @$data->ambulanceRequest->booking_time)->format('g:i A')  : '-' }}</td>
                                    <td>{{@$data->ambulanceRequest->patient_name}}</td>
                                    <td>{{@$data->vehicleMovement->place}}</td>
                                    <td>{{@$data->ambulanceRequest->reason}}</td>
                                    <td>{{@$data->ambulanceRequest->contact_no}}</td>
                                    <td>{{@$data->vehicle->reg_no ?? @$data->tied_up_ambulance}}</td>
                                    <td>{{@$data->ambulanceRequest->ambulance_type}}</td>
                                    @else
                                    <td>{{ \Carbon\Carbon::parse(@$data->ambulanceRequest->booking_date)->format('d/m/Y') }}</td>
                                    <td>{{@$data->ambulanceRequest->patient_name}}</td>
                                    <td>{{@$data->vehicleMovement->place}}</td>
                                    <td>{{@$data->ambulanceRequest->reason}}</td>
                                    <td>{{@$data->ambulanceRequest->contact_no}}</td>
                                    <td>{{@$data->vehicle->reg_no ?? @$data->tied_up_ambulance}}</td>
                                    <td>{{@$data->ambulance_arranged}}</td>
                                    @endif
                                    <td>
                                        <button type="button" class="btn btn-danger view-allocate-btn" data-bs-toggle="modal" data-bs-target="#allocateModal"
                                            data-id="{{ $data->id }}"
                                            data-status="{{ $data->vehicleMovement->status }}"
                                            data-patient_name="{{ $data->ambulanceRequest->patient_name }}"
                                            data-mr_no="{{ $data->ambulanceRequest->mr_no }}"
                                            data-contact_no="{{ $data->ambulanceRequest->contact_no }}"
                                            data-form_date="{{ $data->ambulanceRequest->form_date }}"
                                            data-booking_date="{{ $data->ambulanceRequest->booking_date }}"
                                            data-booking_time="{{ $data->ambulanceRequest->booking_time }}"
                                            data-consultant="{{ $data->ambulanceRequest->consultant }}"
                                            data-ward="{{ $data->ambulanceRequest->ward }}"
                                            data-destination="{{ $data->ambulanceRequest->destination }}"
                                            data-patient_type="{{ $data->ambulanceRequest->patient_type }}"
                                            data-patient_age="{{ $data->ambulanceRequest->patient_age }}"
                                            data-patient_sex="{{ $data->ambulanceRequest->patient_sex }}"
                                            data-requester_name="{{ $data->ambulanceRequest->requester_name }}"
                                            data-requester_relation="{{ $data->ambulanceRequest->requester_relation }}"
                                            data-from_location="{{ $data->ambulanceRequest->from_location }}"
                                            data-patient_location="{{ $data->ambulanceRequest->patient_location }}"
                                            data-reason="{{ $data->ambulanceRequest->reason }}"
                                            data-staff_name="{{ $data->ambulanceRequest->staff_name }}"
                                            data-ambulance_type="{{ $data->ambulanceRequest->ambulance_type }}"
                                            data-received_by="{{ $data->received_by }}"
                                            data-received_time="{{ $data->received_time }}"
                                            data-supervisor_id="{{ $data->supervisor_id }}"
                                            data-ambulance_arranged="{{ $data->ambulance_arranged }}"
                                            data-ambulance_type="{{ $data->ambulance_type }}"
                                            data-vehicle_id="{{ $data->vehicle_id }}"
                                            data-driver_id="{{ $data->driver_id }}"
                                            data-technician="{{ $data->technician_allotted }}"
                                            data-technician_id="{{ $data->technician_id }}"
                                            data-technician_phone="{{ $data->technician_phone }}"
                                            data-km="{{ $data->km }}"
                                            data-total_cost="{{ $data->total_cost }}"
                                            data-confirmed="{{ $data->confirmed }}"
                                            data-no_reason="{{ $data->no_reason }}"
                                            data-tied_up_ambulance="{{ $data->tied_up_ambulance }}"
                                            data-poc_name="{{ $data->poc_name }}"
                                            data-poc_phone="{{ $data->poc_phone }}"
                                            data-receiving_person="{{ $data->receiving_person }}"
                                            data-nature_of_illness="{{ $data->nature_of_illness }}"
                                            data-type_of_care="{{ $data->type_of_care }}"
                                            data-time_receive_call="{{ $data->time_receive_call }}"
                                            data-time_departure="{{ $data->time_departure }}"
                                            data-time_reaching="{{ $data->time_reaching }}"
                                            data-time_reaching_back="{{ $data->time_reaching_back }}"
                                            data-request="complete">
                                            View
                                        </button>
                                    </td>
                                </tr>                             
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-3">No data found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <nav aria-label="Page navigation" class="my-0">
                            {{ @$ambulanceCompleted->links() }}
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">Cancelled Requests</h5>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Request Date</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Department </th>
                                <th>Destination</th>
                                <th>Reason for Request</th>
                                <th>Reason for Cancellation</th>
                            </tr>
                            @forelse($combinedCancelled as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->booking_date ?? $item->date)->format('d/m/Y') }}</td>
                                    <td>{{ $item->vehicle_name ?? 'N/A' }}</td>
                                    <td>{{ $item->driver_name ?? 'N/A' }}</td>
                                    <td>{{ $item->department ?? 'N/A' }}</td>
                                    <td>{{ $item->destination ?? $item->place ?? 'N/A' }}</td>
                                    <td>{{ $item->reason ?? $item->purpose ?? 'N/A' }}</td>
                                    <td>{{ $item->cancel_reason ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">No cancelled requests or movements found.</td>
                                </tr>
                            @endforelse
          
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div
    class="modal fade"
    id="ambulanceModal"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    tabindex="-1"
    aria-labelledby="ambulanceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content p-0">
            <!-- Modal Header -->
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

            <!-- Modal Body -->
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
                    <input type="hidden" id="accept" name="accept" value="1" />
                    <div class="card shadow-sm p-4">

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="patientName">Patient name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="patient_name"
                                    id="patient_name"
                                    value="" required />
                            </div>
                            <div class="col-md-4">
                                <label for="patientName">Age</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    name="patient_age"
                                    id="patient_age"
                                    value="" required />
                            </div>
                            <div class="col-md-4">
                                <label>Sex</label>
                                <select
                                    class="form-select"
                                    aria-label="Default select example" name="patient_sex" id="patient_sex" required>
                                    <option value="">Select</option>
                                    @foreach(['Male', 'Female'] as $data)
                                    <option value="{{@$data}}">{{@$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="mrNo">MR No.</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="mr_no"
                                    id="mr_no"
                                    value="" required />
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
                            <div class="col-md-4">
                                <label for="formDate">Reason</label>
                                <select class="form-select" id="reason" name="reason" required>
                                    <option value="">Select</option>
                                    @foreach ([
                                        'DAMA',
                                        'LAMA',
                                        'Discharge',
                                        'Death'
                                    ] as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <h5>Ambulance transport required on</h5>
                            <div class="col-md-4">
                                <label for="requiredDate">Date</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    name="booking_date"
                                    id="booking_date"
                                    value="" required />
                            </div>
                            <div class="col-md-4">
                                <label for="requiredTime">Time</label>
                                <input
                                    type="time"
                                    class="form-control"
                                    name="booking_time"
                                    id="booking_time"
                                    value="" required />
                            </div>
                            <div class="col-md-4">
                                <label for="consultant">Treating consultant</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="consultant"
                                    id="consultant"
                                    value="" required />
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                            <label for="ward">Ward/ICU</label>
                            <select name="ward" id="ward-select" class="form-select" required>
                                <option value="">Select</option>
                                @foreach(['Ward', 'ICU'] as $typeData)
                                    <option value="{{ $typeData }}" {{ request('ward') == $typeData ? 'selected' : '' }}>{{ $typeData }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4" id="ward-group">
                            <label for="department-ward">Ward</label>
                            <select name="department" id="department-ward" class="form-select" disabled required>
                                <option value="">Select</option>
                                @foreach($ward_list as $ward)
                                    <option value="{{ $ward }}" {{ request('department') == $ward ? 'selected' : '' }}>{{ $ward }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4" id="icu-group" style="display: none;">
                            <label for="department-icu">ICU</label>
                            <select name="department" id="department-icu" class="form-select" disabled required>
                                <option value="">Select</option>
                                @foreach($icu_list as $icu)
                                    <option value="{{ $icu }}" {{ request('department') == $icu ? 'selected' : '' }}>{{ $icu }}</option>
                                @endforeach
                            </select>
                        </div>

                            <div class="col-md-4">
                                <label for="dropAddress">Drop destination</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="destination"
                                    id="destination"
                                    value="" required />
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="patient_type">Patient type</label>
                                <select name="patient_type" id="patient_type" class="form-select " required>
                                    <option value="">Select</option>
                                    @foreach(['Stable', 'Unstable', 'Deadbody'] as $typeData)
                                    <option value="{{ $typeData }}" {{ request('patient_type') == $typeData ? 'selected' : '' }}>{{ $typeData }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="requester_name">Person requesting for ambulance</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="requester_name"
                                    id="requester_name"
                                    value="" required />
                            </div>
                            <div class="col-md-4">
                                <label for="requester_relation">Relation to patient</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="requester_relation"
                                    id="requester_relation"
                                    value="" required />
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
                                    value="" required />
                            </div>
                            <div class="col-md-6">
                                <label for="ambulanceType">Ambulance type</label>
                                <select name="ambulance_type" id="ambulance_type" class="form-select " required>
                                    <option value="">Select</option>
                                    @foreach(['Normal Ambulance', 'ICU Ambulance'] as $typeData)
                                    <option value="{{ $typeData }}">{{ $typeData }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3 d-flex justify-content-center">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success mt-3 px-5" id="submit-btn">
                                    Submit
                                </button>
                                <button type="submit" class="btn btn-success mt-3 px-5 cancel-btn">
                                    Cancel Trip
                                </button>
                                <!-- <button type="button" id="#" class="btn btn-danger d-none" data-bs-toggle="modal" data-bs-target="#allocateModal">
                                    Allocate Ambulance
                                </button> -->
                            </div>
                        </div>
                    </div>
                    @else
                    <input type="hidden" id="accept" name="accept" value="1" />
                    <input type="hidden" id="form_date" name="form_date" value="{{ now()->toDateString() }}" readOnly required />
                    <div class="card shadow-sm p-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="patientName">Name of person called</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="requester_name"
                                    id="requester_name"
                                    value="" required />
                            </div>
                            <div class="col-md-4">
                                <label for="mrNo">Relationship with patient</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="requester_relation"
                                    id="requester_relation"
                                    value="" required />
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
                                <label for="patientName">Patient name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="patient_name"
                                    id="patient_name"
                                    value="" required />
                            </div>
                            <div class="col-md-4">
                                <label for="patientName">Age</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    name="patient_age"
                                    id="patient_age"
                                    value="" required />
                            </div>
                            <div class="col-md-4">
                                <label>Sex</label>
                                <select
                                    class="form-select"
                                    aria-label="Default select example" name="patient_sex" id="patient_sex" required>
                                    <option value="">Select</option>
                                    @foreach(['Male', 'Female'] as $data)
                                    <option value="{{@$data}}">{{@$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- <div class="col-md-4">
                                <label for="consultant">Request From</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="from_location"
                                    id="from_location"
                                    value="" required />
                            </div> -->
                            <div class="col-md-4">
                                <label for="patient_location">Location of patient</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="patient_location"
                                    id="patient_location"
                                    value="" required />
                            </div>
                            <div class="col-md-4">
                                <label for="reason">Reason for request</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="reason"
                                    id="reason"
                                    value="" required />
                            </div>
                            <div class="col-md-4">
                                <label for="reason">Contact No.</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    name="contact_no"
                                    id="contact_no"
                                    value="" required />
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="ambulanceType">Ambulance type</label>
                                <select name="ambulance_type" id="ambulance_type" class="form-select " required>
                                    <option value="">Select</option>
                                    @foreach(['Normal Ambulance', 'ICU Ambulance'] as $typeData)
                                    <option value="{{ $typeData }}">{{ $typeData }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3 d-flex justify-content-center">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success mt-3 px-5" id="submit-btn">
                                    Submit
                                </button>
                                <button type="submit" class="btn btn-success mt-3 px-5 cancel-btn">
                                    Cancel Trip
                                </button>
                                <!-- <button type="button" id="#" class="btn btn-danger d-none" data-bs-toggle="modal" data-bs-target="#allocateModal">
                                    Allocate Ambulance
                                </button> -->
                            </div>
                        </div>
                    </div>
                    @endif                    
                </form>
            </div>
        </div>
    </div>
</div>

<div
    class="modal fade"
    id="allocateModal"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    tabindex="-1"
    aria-labelledby="allocateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content p-0">
            <!-- Modal Header -->
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

            <!-- Modal Body -->
            <div class="modal-body p-3">
                <form id="allocateForm" action="{{ route('ambulance.allocate') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="ambulance_request_id" name="ambulance_request_id" value="{{ $requestId ?? '' }}" />
                    <input type="hidden" id="ambulance_booking_date" name="booking_date" value="" />
                    <input type="hidden" id="ambulance_booking_time" name="booking_time" value="" />
                    <input type="hidden" id="ambulance_reason" name="reason" value="" />
                    <input type="hidden" id="ambulance_type" name="type" value="{{@$type}}" />
                    <input type="hidden" id="ambulance_arranged" name="ambulance_arranged" value="" />
                    <input type="hidden" id="user_id" name="user_id" value="" />
                      
                    <div class="card shadow-sm p-4">
                        
                        <h5 class="title mt-3">Patient Details</h5>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="patientName">Patient name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name=""
                                    id="a_patient_name"
                                    value="" readOnly />
                            </div>
                            <div class="col-md-4">
                                <label for="patientName">Age</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    name=""
                                    id="a_patient_age"
                                    value="" readOnly />
                            </div>
                            <div class="col-md-4">
                                <label for="patientName">Sex</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name=""
                                    id="a_patient_sex"
                                    value="" readOnly />
                            </div>
                        </div>

                        <div class="row mt-3">
                            @if(@$type == 'ward')
                            <div class="col-md-4">
                                <label for="mrNo">MR No.</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="a_mr_no"
                                    value="" readOnly />
                            </div> 
                            @else
                             <div class="col-md-4">
                                <label for="requiredDate">Date</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="a_booking_date"
                                    value="" readOnly />
                            </div>
                            @endif                       
                            <div class="col-md-4">
                                <label>Contact No.</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="a_contact_no"
                                    value="" readOnly />
                            </div>
                             <div class="col-md-4">
                                <label>Reason</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="a_reason"
                                    value="" readOnly />
                            </div>
                        </div>

                        @if(@$type == 'ward')
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="requiredDate">Date</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="a_booking_date"
                                    value="" readOnly />
                            </div>
                            <div class="col-md-4">
                                <label for="requiredTime">Time</label>
                                <input
                                    type="time"
                                    class="form-control"
                                    id="a_booking_time"
                                    value="" readOnly />
                            </div>
                            <div class="col-md-4">
                                <label>Ambulance type</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="a_ambulance_type"
                                    value="" readOnly />
                            </div>                            
                        </div>
                        @else
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="requiredDate">Location</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="place"
                                    value="" readOnly />
                            </div>
                            <div class="col-md-4">
                                <label>Ambulance type</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="a_ambulance_type"
                                    value="" readOnly />
                            </div>                            
                        </div>
                        @endif

                        @if(@$type == 'ward')

                        <h5 class="title mt-3">Allocate Inhouse Ambulance</h5>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label>Received person ED/ front desk</label>
                                <input type="text" class="form-control"  id="received_by" name="received_by" required />
                            </div>
                            <div class="col-md-6">
                                <label>Received time</label>
                                <input type="time" class="form-control" id="received_time" name="received_time" required />
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label>Hospital supervisor name</label>
                                <select id="supervisor_id" name="supervisor_id" class="form-select " required>
                                    <option value="">Select</option>
                                    @foreach($supervisors as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- <div class="col-md-6">
                                <label>Ambulance Arranged</label>
                                <select class="form-select" id="ambulance_arranged" name="ambulance_arranged" required>
                                    <option value="">Select</option>
                                    @foreach ([
                                        'Hospital ICU Ambulance',
                                        'Hospital Normal Ambulance',
                                    ] as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div> -->
                            <div class="col-md-6">
                                <label>Drop destination</label>
                                <input type="text" class="form-control" id="place" name="place" readonly />
                            </div>
                        </div>                       

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>Km</label>
                                <input type="number" class="form-control" id="km" name="km" required />
                            </div>
                            <div class="col-md-4">
                                <label>Total cost</label>
                                <input type="number" class="form-control" id="total_cost" name="total_cost" required />
                            </div>
                            <div class="col-md-4">
                                <label for="confirmed">Confirm inhouse ambulance</label>
                                <select class="form-select" id="confirmed" name="confirmed" required>
                                    <option value="">-- Select --</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3" id="confirmedYes">
                            <!-- <h5 class="title">Allot Ambulance</h5> -->
                            <div class="col-md-6">
                                <label>Ambulance</label>
                                <select class="form-select" aria-label="Default select example" name="vehicle_id" id="vehicle_id">
                                    <option value="">Select</option>
                                    @foreach(@$vehicles as $vehicle)
                                    <option value="{{@$vehicle->id}}" data-driver_id="{{@$vehicle->employee_id}}">{{@$vehicle->reg_no}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Driver name</label>
                                <select class="form-select" aria-label="Default select example" name="driver_id" id="driver_id">
                                    <option value="">Select</option>
                                    @foreach(@$drivers as $driver=>$id)
                                    <option value="{{@$id}}">{{@$driver}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3" id="confirmedNo">
                            <div class="col-md-6">
                                <label>Reason</label>
                                <input class="form-control" type="text" id="no_reason" name="no_reason" />
                            </div>
                            <div class="col-md-6">
                                <label>Allot tied-up ambulance</label>
                                <select class="form-select" id="tied_up_ambulance" name="tied_up_ambulance">
                                    <option value="">-- Select --</option>
                                    @foreach(@$externalAmbulance as $ambulance)
                                    <option value="{{@$ambulance}}">{{@$ambulance}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row mt-3">
                                <h6>Point of contact</h6>
                                <div class="col-md-6">
                                    <label>Name</label>
                                    <input class="form-control" type="text" id="poc_name" name="poc_name" />
                                </div>
                                <div class="col-md-6">
                                    <label>Phone</label>
                                    <input class="form-control" type="text" id="poc_phone" name="poc_phone" />
                                </div>

                                <!-- <div class="col-md-6">
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Reason</label>
                                                <input class="form-control" type="text" name="technician_reason" />
                                            </div>
                                        </div>
                                    </div>
                                </div> -->

                            </div>
                        </div>

                        <div class="row mt-3" id="allot-footer">
                            <div class="col-md-6">
                                <label>Technician allotted</label>
                                <select class="form-select" name="technician_allotted" id="technician" required>
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
                                            <select id="technician_id" name="technician_id" class="form-select  tech">
                                                <option value="">Select</option>
                                                @foreach($technicians as $tech)
                                                <option value="{{ $tech->id }}" data-tech_phone="{{@$tech->mobile}}">{{ $tech->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                        <div class="col-md-6">
                                            <label>Phone</label>
                                            <input class="form-control tech-phone" type="text" name="technician_phone" id="technician_phone" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-center mt-3">
                                <button type="submit" id="allot-btn" class="btn btn-md btn-primary mt-3">Submit</button>
                            </div>
                        </div>
                        
                        @else
                        
                        <input type="hidden" id="ambulance_destination" name="place" value="" />
                        <div class="row mt-3">
                            <h5 class="title">Information desk / emergency department</h5>
                            <div class="col-md-6">
                                <label>Call received by</label>
                                <input class="form-control" type="text" name="receiving_person" id="receiving_person" required />
                            </div>
                            <div class="col-md-6">
                                <label>Nature of illness</label>
                                <input type="text" class="form-control" name="nature_of_illness" id="nature_of_illness" required />
                            </div>
                            <!-- <div class="col-md-4">
                                <label>Type of care given</label>
                                <input type="text" class="form-control" name="type_of_care" id="type_of_care" required />
                            </div> -->
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label>Time of receiving call</label>
                                <input class="form-control" type="time" name="time_receive_call" id="time_receive_call" required />
                            </div>
                            <div class="col-md-6">
                                <label>Time of departure to site</label>
                                <input class="form-control" type="time" name="time_departure" id="time_departure" />
                            </div>
                            <!-- <div class="col-md-3">
                                <label>Time of reaching the location</label>
                                <input class="form-control" type="time" name="time_reaching" id="time_reaching" />
                            </div>
                            <div class="col-md-3">
                                <label>Time of reaching back casualty</label>
                                <input class="form-control" type="time" name="time_reaching_back" id="time_reaching_back" />
                            </div> -->
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label>Hospital supervisor name</label>
                                <select id="supervisor_id" name="supervisor_id" class="form-select " required>
                                    <option value="">Select</option>
                                    @foreach($supervisors as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- <div class="col-md-6">
                                <label>Ambulance Arranged</label>
                                <select class="form-select" id="ambulance_arranged" name="ambulance_arranged" required>
                                    <option value="">Select</option>
                                    @foreach ([
                                        'Hospital ICU Ambulance',
                                        'Hospital Normal Ambulance',
                                    ] as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div> -->
                            <div class="col-md-6">
                                <label for="confirmed">Confirm inhouse ambulance</label>
                                <select class="form-select" id="confirmed" name="confirmed" required>
                                    <option value="">-- Select --</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3" id="confirmedYes">
                            <!-- <h5 class="title">Allot Ambulance</h5> -->
                            <div class="col-md-6">
                                <label>Ambulance</label>
                                <select class="form-select" aria-label="Default select example" name="vehicle_id" id="vehicle_id" required>
                                    <option value="">Select</option>
                                    @foreach(@$vehicles as $vehicle)
                                    <option value="{{@$vehicle->id}}" data-driver_id="{{@$vehicle->employee_id}}">{{@$vehicle->reg_no}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Driver name</label>
                                <select class="form-select" aria-label="Default select example" name="driver_id" id="driver_id" required>
                                    <option value="">Select</option>
                                    @foreach(@$drivers as $driver=>$id)
                                    <option value="{{@$id}}">{{@$driver}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3" id="confirmedNo">
                            <div class="col-md-6">
                                <label>Reason</label>
                                <input class="form-control" type="text" id="no_reason" name="no_reason" />
                            </div>
                            <div class="col-md-6">
                                <label>Allot tied-up ambulance</label>
                                <select class="form-select" id="tied_up_ambulance" name="tied_up_ambulance">
                                    <option value="">-- Select --</option>
                                    @foreach(@$externalAmbulance as $ambulance)
                                    <option value="{{@$ambulance}}">{{@$ambulance}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row mt-3">
                                <h6>Point of contact</h6>
                                <div class="col-md-6">
                                    <label>Name</label>
                                    <input class="form-control" type="text" id="poc_name" name="poc_name" />
                                </div>
                                <div class="col-md-6">
                                    <label>Phone</label>
                                    <input class="form-control" type="text" id="poc_phone" name="poc_phone" />
                                </div>

                                <!-- <div class="col-md-6">
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Reason</label>
                                                <input class="form-control" type="text" name="technician_reason" />
                                            </div>
                                        </div>
                                    </div>
                                </div> -->

                            </div>
                        </div>

                        <div class="row mt-3" id="allot-footer">
                            <div class="col-md-6">
                                <label>Technician allotted</label>
                                <select class="form-select" name="technician_allotted" id="technician" required>
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
                                            <select id="technician_id" name="technician_id" class="form-select  tech">
                                                <option value="">Select</option>
                                                @foreach($technicians as $tech)
                                                <option value="{{ $tech->id }}" data-tech_phone="{{@$tech->mobile}}">{{ $tech->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Phone</label>
                                            <input class="form-control tech-phone" type="text" name="technician_phone" id="technician_phone" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-center mt-3">
                                <button type="submit" id="allot-btn" class="btn btn-md btn-primary mt-3">Submit</button>
                            </div>
                        </div>

                        <!-- <div class="row mt-3 d-flex justify-content-center">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success mt-3 px-5" id="allot-submit-btn">
                                    Submit
                                </button>
                            </div>
                        </div> -->
                    @endif
                    <div class="col-md-12 text-center cancel-btn">
                        <button type="submit" class="btn btn-success mt-3">
                            Cancel Trip
                        </button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Reason Modal -->
<div class="modal fade" id="cancelReasonModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelReasonLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content p-0">
            <form id="cancelForm" method="POST">
                @csrf
                <input type="hidden" name="type" value="{{@$type}}" />
                <div class="modal-header py-3" style="background: #164966">
                    <h5 class="modal-title text-white mb-0" id="modalTitle">
                        <i class="fas fa-user-md me-2"></i>Cancel Trip
                    </h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <p>Are you sure you want to cancel this trip?</p>
                    <div class="mb-3">
                        <label for="cancel_reason" class="form-label">Cancel Reason</label>
                        <input type="text" class="form-control" id="cancel_reason" name="cancel_reason" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-danger">Yes, Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById("booking_date").min = today;
    });

    $('#requestForm').on('submit', function(e) {
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

    $('#allocateForm').on('submit', function(e) {
        const mobile = $('#technician_phone').val();
        const isValid = /^[6-9]\d{9}$/.test(mobile);
        var $btn = $(this).find('button[type="submit"]');

        if (!isValid && $('#technicianYes').is(':visible')) {
            e.preventDefault();
            alert('Invalid mobile number');

            setTimeout(function() {
                $btn.prop('disabled', false).text('Submit');
            }, 20);
        }
    });

    // function toggleRequired() {
    //     console.log('sub');
    //     if (!$('#confirmedYes').is(':visible')) {
    //         console.log('ddd');
    //         $('#confirmedYes').find('select, input').attr('required', true);
    //         $('#confirmedNo').find('select, input').removeAttr('required');
    //     } else if (!$('#confirmedNo').is(':visible')) {
    //         console.log('sss');
    //         $('#confirmedNo').find('select, input').attr('required', true);
    //         $('#confirmedYes').find('select, input').removeAttr('required');
    //     }
    //     if ($('#technicianYes').is(':visible')) {
    //         console.log('hhh');
    //         $('#technicianYes').find('select, input').attr('required', true);
    //     } else {
    //         console.log('kkk');
    //         $('#technicianYes').find('select, input').removeAttr('required');
    //     }
    // } 
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
                                        data-bs-target="#ambulanceModal">
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
        $('.cancel-btn').hide();
        $('.request-btn').on('click', function() {
            var id = $(this).data('id');
            $('#id').val($(this).data('id') || '');
            $('#patient_name').val($(this).data('patient_name') || '');
            $('#mr_no').val($(this).data('mr_no') || '');
            $('#contact_no').val($(this).data('contact_no') || '');
            $('#form_date').val($(this).data('form_date') || '');
            $('#booking_date').val($(this).data('booking_date') || '');
            $('#booking_time').val($(this).data('booking_time') || '');
            $('#consultant').val($(this).data('consultant') || '');
            $('#ward').val($(this).data('ward') || '');
            $('#destination').val($(this).data('destination') || '');
            $('#patient_type').val($(this).data('patient_type') || '');
            $('#requester_name').val($(this).data('requester_name') || '');
            $('#requester_relation').val($(this).data('requester_relation') || '');
            $('#patient_name').val($(this).data('patient_name') || '');
            $('#patient_age').val($(this).data('patient_age') || '');
            $('#patient_sex').val($(this).data('patient_sex') || '');
            $('#from_location').val($(this).data('from_location') || '');
            $('#patient_location').val($(this).data('patient_location') || '');
            $('#reason').val($(this).data('reason') || '');
            $('#staff_name').val($(this).data('staff_name') || '');
            $('#ambulance_type').val($(this).data('ambulance_type') || '');
            $('#accept').val('1');

            $('#requestForm').attr('action', '/ambulance/update/' + id);
            // $('#vehicleForm').append('<input type="hidden"  name="_method" value="POST">');
            $('#submit-btn').text('Accept');
            $('#modalTitle').text('Ambulance Request');
        });

        $('.view-btn').on('click', function() {
            var id = $(this).data('id');
            $('#id').val($(this).data('id') || '');
            $('#type').val($(this).data('type') || '');
            $('#patient_name').val($(this).data('patient_name') || '');
            $('#mr_no').val($(this).data('mr_no') || '');
            $('#contact_no').val($(this).data('contact_no') || '');
            $('#form_date').val($(this).data('form_date') || '');
            $('#booking_date').val($(this).data('booking_date') || '');
            $('#booking_time').val($(this).data('booking_time') || '');
            $('#consultant').val($(this).data('consultant') || '');
            $('#ward').val($(this).data('ward') || '');
            $('#destination').val($(this).data('destination') || '');
            $('#patient_type').val($(this).data('patient_type') || '');
            $('#requester_name').val($(this).data('requester_name') || '');
            $('#requester_relation').val($(this).data('requester_relation') || '');
            $('#patient_name').val($(this).data('patient_name') || '');
            $('#patient_age').val($(this).data('patient_age') || '');
            $('#patient_sex').val($(this).data('patient_sex') || '');
            $('#from_location').val($(this).data('from_location') || '');
            $('#patient_location').val($(this).data('patient_location') || '');
            $('#reason').val($(this).data('reason') || '');
            $('#staff_name').val($(this).data('staff_name') || '');
            $('#ambulance_type').val($(this).data('ambulance_type') || '');
            // $('#submit-btn').hide();
            $('#allot-btn').hide();
            $('.cancel-btn').show();
            $('#cancelForm').attr('action', '/trip-cancel/' + id + '/request');
            // $('#requestForm').attr('onsubmit', "return confirm('Are you sure?\\nDo you want to cancel the trip?')");
            // $('#submit-btn').data('cancel-id', id);
            $(this).data('update_flag') ? $('#submit-btn').hide() : $('#submit-btn').show();
            $('#requestForm').attr('action', '/ambulance/update/' + id);
            $('#submit-btn').text('Update');
            $('#booking_date').removeAttr('min');
            // if($(this).data('request')=="request")
            //     $('.cancel-btn').hide();
        });

        $('.cancel-btn').on('click', function(e) {
            e.preventDefault();
            $('#cancel_reason').val('');
            $('#cancelReasonModal').modal('show');
        });

        $('#ambulanceModal').on('hidden.bs.modal', function() {
            $('#id').val('');
            $('#patient_name').val('');
            $('#mr_no').val('');
            $('#contact_no').val('');
            // $('#form_date').val('');
            $('#booking_date').val('');
            $('#booking_time').val('');
            $('#consultant').val('');
            $('#ward').val('');
            $('#destination').val('');
            $('#patient_type').val('');
            $('#requester_name').val('');
            $('#requester_relation').val('');
            $('#patient_name').val('');
            $('#patient_age').val('');
            $('#patient_sex').val('');
            $('#from_location').val('');
            $('#patient_location').val('');
            $('#reason').val('');
            $('#staff_name').val('');
            $('#ambulance_type').val('');

            $('#accept').val('');
            $('.cancel-btn').hide();
            $('#submit-btn').show();
            
            $('#submit-btn').text('Submit');
            $('#requestForm').attr('action', '/ambulance/store');
            $('#requestForm').removeAttr('onsubmit');

            const today = new Date().toISOString().split('T')[0];
            document.getElementById("booking_date").min = today;
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.allocate-btn').on('click', function() {
            $('.cancel-btn').hide();
            $('#allot-btn').show();
            $('#ambulance_request_id').val($(this).data('id') || '');
            $('#ambulance_booking_date').val($(this).data('booking_date') || '');
            $('#ambulance_booking_time').val($(this).data('booking_time') || '');
            $('#ambulance_destination').val($(this).data('destination') || '');
            $('#ambulance_reason').val($(this).data('reason') || '');
            $('#ambulance_arranged').val($(this).data('ambulance_type') || '');
            $('#user_id').val($(this).data('user_id') || '');
            $('#a_patient_name').val($(this).data('patient_name') || '');
            $('#a_patient_age').val($(this).data('patient_age') || '');
            $('#a_patient_sex').val($(this).data('patient_sex') || '');
            $('#a_mr_no').val($(this).data('mr_no') || '');
            $('#a_ambulance_type').val($(this).data('ambulance_type') || '');
            $('#a_contact_no').val($(this).data('contact_no') || '');
            $('#a_reason').val($(this).data('reason') || '');
            $('#a_booking_date').val($(this).data('booking_date') || '');
            $('#a_booking_time').val($(this).data('booking_time') || '');
            $('#place').val($(this).data('destination') || '');
            // $('#h_requester_name').val($(this).data('requester_name') || '');
            // $('#h_requester_relation').val($(this).data('requester_relation') || '');
            // $('#h_form_date').val($(this).data('form_date') || '');
            // $('#h_patient_name').val($(this).data('patient_name') || '');
            // $('#h_patient_age').val($(this).data('patient_age') || '');
            // $('#h_patient_sex').val($(this).data('patient_sex') || '');
            // $('#h_from_location').val($(this).data('from_location') || '');
            // $('#h_patient_location').val($(this).data('patient_location') || '');
            // $('#h_reason').val($(this).data('reason') || '');
            // $('#h_contact_no').val($(this).data('contact_no') || '');
            // $('#h_technician_name').val($(this).data('technician_name') || '');
            // $('#h_technician_phone').val($(this).data('technician_phone') || '');
        });

        $('.view-allocate-btn').on('click', function() {
            
            $('#ambulance_request_id').val($(this).data('id') || '');
            $('#ambulance_booking_date').val($(this).data('booking_date') || '');
            $('#ambulance_booking_time').val($(this).data('booking_time') || '');
            $('#ambulance_destination').val($(this).data('destination') || '');
            $('#ambulance_reason').val($(this).data('reason') || '');
            $('#user_id').val($(this).data('user_id') || '');
            $('#a_patient_name').val($(this).data('patient_name') || '');
            $('#a_patient_age').val($(this).data('patient_age') || '');
            $('#a_patient_sex').val($(this).data('patient_sex') || '');
            $('#a_mr_no').val($(this).data('mr_no') || '');
            $('#a_ambulance_type').val($(this).data('ambulance_type') || '');
            $('#a_contact_no').val($(this).data('contact_no') || '');
            $('#a_reason').val($(this).data('reason') || '');
            $('#a_booking_date').val($(this).data('booking_date') || '');
            $('#a_booking_time').val($(this).data('booking_time') || '');
            $('#place').val($(this).data('destination') || '');

            $('#received_by').val($(this).data('received_by') || '');
            $('#received_time').val($(this).data('received_time') || '');
            $('#supervisor_id').val($(this).data('supervisor_id') || '');
            $('#ambulance_arranged').val($(this).data('ambulance_arranged') || '');
            $('#ambulance_type').val($(this).data('ambulance_type') || '');
            $('#vehicle_id').val($(this).data('vehicle_id') || '');
            $('#driver_id').val($(this).data('driver_id') || '');
            $('#technician').val($(this).data('technician') || '');
            $('#technician_id').val($(this).data('technician_id') || '');
            $('#technician_phone').val($(this).data('technician_phone') || '');
            $('#km').val($(this).data('km') || '');
            $('#total_cost').val($(this).data('total_cost') || '');
            $('#confirmed').val($(this).data('confirmed') || '');

            $('#no_reason').val($(this).data('no_reason') || '');
            $('#tied_up_ambulance').val($(this).data('tied_up_ambulance') || '');
            $('#poc_name').val($(this).data('poc_name') || '');
            $('#poc_phone').val($(this).data('poc_phone') || '');
            $('#receiving_person').val($(this).data('receiving_person') || '');
            $('#nature_of_illness').val($(this).data('nature_of_illness') || '');
            $('#type_of_care').val($(this).data('type_of_care') || '');
            $('#time_receive_call').val($(this).data('time_receive_call') || '');
            $('#time_departure').val($(this).data('time_departure') || '');
            $('#time_reaching').val($(this).data('time_reaching') || '');
            $('#time_reaching_back').val($(this).data('time_reaching_back') || '');
            $('#supervisor_id').val($(this).data('supervisor_id') || '');
            $('#ambulance_arranged').val($(this).data('ambulance_arranged') || '');   
            $('#technician').trigger('change');
            $('#confirmed').trigger('change');
            // $('#allot-submit-btn').hide();
            $('#allot-btn').hide();
            $(this).data('status') == 'allot' ? $('.cancel-btn').show() : $('.cancel-btn').hide();
            var id = $(this).data('id');
            $('#cancelForm').attr('action', '/trip-cancel/' + id);
            // if($(this).data('request')=="request")
            //     $('.cancel-btn').hide();
        });

        $('#allocateModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset();
            $('.cancel-btn').hide();
            // $('#allot-submit-btn').show();
            $('#allot-btn').show();
            $('#allot-footer').hide();
            $('#technicianYes, #technicianNo').hide().find('input, select, textarea').prop('required', false);
            $('#confirmedYes, #confirmedNo').hide().find('input, select, textarea').prop('required', false);
        });

    });
</script>

<script>
    $(document).ready(function() {
        $('#allot-footer').hide();
        $('#confirmedYes, #confirmedNo').hide();
        $('#technicianYes, #technicianNo').hide();

        $('#confirmed').on('change', function() {
            $('#allot-footer').show();
            const value = $(this).val();

            if (value === 'yes') {
                $('#confirmedYes').show().find('input, select, textarea').prop('required', true);
                $('#confirmedNo').hide().find('input, select, textarea').prop('required', false);
                $('#allot-btn').text('Allot Ambulance');
            } else if (value === 'no') {
                $('#confirmedYes').hide().find('input, select, textarea').prop('required', false);
                $('#confirmedNo').show().find('input, select, textarea').prop('required', true);
                $('#allot-btn').text('Allot Ambulance');
            } else {
                $('#confirmedYes, #confirmedNo').hide().find('input, select, textarea').prop('required', false);
            }
        });

        $('#technician').on('change', function() {
            const val = $(this).val();

            if (val === 'yes') {
                $('#technicianYes').show().find('input, select, textarea').prop('required', true);
                $('#technicianNo').hide().find('input, select, textarea').prop('required', false);
            } else if (val === 'no') {
                $('#technicianYes').hide().find('input, select, textarea').prop('required', false);
                $('#technicianNo').show().find('input, select, textarea').prop('required', true);
            } else {
                $('#technicianYes, #technicianNo').hide().find('input, select, textarea').prop('required', false);
            }
        });
    });
</script>

<script>
    $('#vehicle_id').on('change', function() {
        var driverId = $(this).find(':selected').data('driver_id');
        $('#driver_id').val(driverId || '');
    });
    $('.tech').on('change', function() {
        var techPhone = $(this).find(':selected').data('tech_phone');
        $('.tech-phone').val(techPhone || '');
    });
    
    function toggleDepartmentSelect() {
        var selected = $('#ward-select').val();

        $('#department-ward').prop('disabled', true);
        $('#department-icu').prop('disabled', true);
        $('#ward-group, #icu-group').hide();

        if (selected === 'Ward') {
            $('#ward-group').show();
            $('#department-ward').prop('disabled', false);
        } else if (selected === 'ICU') {
            $('#icu-group').show();
            $('#department-icu').prop('disabled', false);
        }
    }

    $(document).ready(function() {
        toggleDepartmentSelect();

        $('#ward-select').on('change', function() {
            toggleDepartmentSelect();
        });
    });

</script>

@endsection