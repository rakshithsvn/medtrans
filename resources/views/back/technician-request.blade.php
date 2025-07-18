@extends('layouts.back.index')

@section('content')

<section class="section">
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <!-- Filter Form and Action Buttons -->
            <form action="{{ route('technician.index',[@$type]) }}" method="POST">
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
                        <label>Status</label>
                        <select
                            class="form-select"
                            aria-label="Default select example">
                            <option value="">Select</option>
                            <option value="Completed">Completed</option>
                            <option value="Pending">Pending</option>
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
                    <!-- @if(in_array($user->register_by, ['ADMIN', 'SUPERVISOR']))
                    <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                        <button type="button" id="new-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestModal">
                            New
                        </button>
                    </div>
                    @endif -->
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm p-4">
        <div class="row">
            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">Pending Requests</h5>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient Name</th>
                            <th>Contact No.</th>
                            <th>Ambulance</th>
                            <th>Ambulance Type</th>
                            <th></th>
                        </tr>

                        <tr>
                            @forelse(@$allocationRequests as $data)
                            <td>{{ \Carbon\Carbon::parse(@$data->ambulanceRequest->booking_date)->format('d/m/Y') }}</td>
                             @php
                                $time = @$data->ambulanceRequest->booking_time ?? @$data->time_departure;
                            @endphp   
                            <td> {{ $time && \Carbon\Carbon::hasFormat($time, 'H:i:s') 
                                ? \Carbon\Carbon::createFromFormat('H:i:s', $time)->format('g:i A') 
                                : '-' 
                            }}</td>
                            <td>{{@$data->ambulanceRequest->patient_name}}</td>
                            <td>{{@$data->ambulanceRequest->contact_no}}</td>
                            <td>{{@$data->Vehicle->reg_no}}</td>
                            <td>{{@$data->ambulanceRequest->ambulance_type ?? @$data->ambulance_arranged}}</td>

                            <td> <button
                                    class="btn btn-success create-btn" data-bs-toggle="modal" data-bs-target="#requestModal"
                                    data-allocation_id="{{ $data->id }}"
                                    data-technician_id="{{ $data->technician_id }}"
                                    data-booking_date="{{ $data->ambulanceRequest->booking_date }}"
                                    data-booking_time="{{@$data->ambulanceRequest->booking_time ?? @$data->time_departure}}"
                                    data-name="{{ $data->ambulanceRequest->patient_name }}"
                                    data-age="{{ $data->ambulanceRequest->patient_age }}"
                                    data-sex="{{ $data->ambulanceRequest->patient_sex }}"
                                    data-location="{{ $data->ambulanceRequest->patient_location }}"
                                    data-contact_no="{{ $data->ambulanceRequest->contact_no }}"
                                    data-vehicle_name="{{ @$data->Vehicle->reg_no }}"
                                    data-driver_name="{{ @$data->Driver->name }}"
                                    data-destination="{{ $data->ambulanceRequest->destination }}">Update</button></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">No data found.</td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">Completed Requests</h5>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>Date</th>
                            <th>Departure Time</th>
                            <th>Patient Name</th>
                            <th>Present Diagnosis</th>
                            <th>Request Reason</th>
                            <th></th>
                        </tr>

                        <tr>
                            @forelse(@$techRequests as $data)
                            <td>{{ \Carbon\Carbon::parse(@$data->booking_date)->format('d-m-Y') }}</td>
                            <td>{{ $data->time_departure ? \Carbon\Carbon::createFromFormat('H:i:s', @$data->time_departure)->format('g:i A') : '-' }}</td>                            
                            <td>{{@$data->patient_name}}</td>
                            <td>{{@$data->present_diagnosis}}</td>
                            <td>{{@$data->request_reason}}</td>

                            <td> <button
                                    class="btn btn-success view-btn" data-bs-toggle="modal" data-bs-target="#requestModal"
                                    data-id="{{ $data->id }}"
                                    data-allocation_id="{{ $data->id }}"
                                    data-technician_id="{{ $data->technician_id }}"
                                    data-patient_name="{{ $data->patient_name }}"
                                    data-patient_age="{{ $data->patient_age }}"
                                    data-patient_sex="{{ $data->patient_sex }}"
                                    data-booking_date="{{ $data->booking_date }}"
                                    data-booking_time="{{ @$data->booking_time }}"
                                    data-contact_no="{{ @$data->contact_no }}"
                                    data-vehicle_name="{{ @$data->vehicle_name }}"
                                    data-driver_name="{{ @$data->driver_name }}"
                                    data-hospital_id="{{ $data->hospital_id }}"
                                    data-transfering_from="{{ $data->transfering_from }}"
                                    data-present_diagnosis="{{ $data->present_diagnosis }}"
                                    data-request_reason="{{ $data->request_reason }}"
                                    data-doctor="{{ $data->doctor }}"
                                    data-destination="{{ $data->destination }}"
                                    data-consent_taken="{{ $data->consent_taken }}"
                                    data-technician_name="{{ $data->technician_name }}"
                                    data-time_departure="{{ $data->time_departure }}"
                                    data-time_reached_destination="{{ $data->time_reached_destination }}"
                                    data-time_reached_back="{{ $data->time_reached_back }}"
                                    data-temp_before="{{ $data->temp_before }}"
                                    data-time_current="{{ $data->time_current }}"
                                    data-temp_current="{{ $data->temp_current }}"
                                    data-bp_current="{{ $data->bp_current }}"
                                    data-hr_current="{{ $data->hr_current }}"
                                    data-rep_current="{{ $data->rep_current }}"
                                    data-spo2_current="{{ $data->spo2_current }}"
                                    data-bp_before="{{ $data->bp_before }}"
                                    data-hr_before="{{ $data->hr_before }}"
                                    data-rep_before="{{ $data->rep_before }}"
                                    data-spo2_before="{{ $data->spo2_before }}"
                                    data-temp_after="{{ $data->temp_after }}"
                                    data-bp_after="{{ $data->bp_after }}"
                                    data-hr_after="{{ $data->hr_after }}"
                                    data-rep_after="{{ $data->rep_after }}"
                                    data-spo2_after="{{ $data->spo2_after }}"
                                    data-medication="{{ $data->medication }}"
                                    data-order_time="{{ $data->order_time }}"
                                    data-setting_time="{{ $data->setting_time }}"
                                    data-remarks="{{ $data->remarks }}">View</button></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">No data found.</td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<div
    class="modal fade"
    id="requestModal"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    tabindex="-1"
    aria-labelledby="requestModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content p-0">
            <!-- Modal Header -->
            <div class="modal-header py-3" style="background: #164966">
                <h5 class="modal-title text-white mb-0" id="modalTitle">
                    <i class="fas fa-user-md me-2"></i>Technician Request
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
                    action="{{ route('technician.store') }}"
                    method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="allocation_id" name="allocation_id" value="" />
                    <input type="hidden" id="technician_id" name="technician_id" value="" />

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label>Patient Name</label>
                            <input type="text" class="form-control" name="patient_name" id="patient_name" required/>
                        </div>
                        <div class="col-md-3">
                            <label>Age</label>
                            <input type="number" class="form-control" name="patient_age" id="patient_age" required/>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <label>Contact No</label>
                            <input type="number" class="form-control" name="contact_no" id="contact_no" required/>
                        </div>
                    </div>

                    <div class="row mt-3">                        
                        <div class="col-md-3">
                            <label>Appointment Date</label>
                            <input
                                type="date"
                                class="form-control"
                                name="booking_date"
                                id="booking_date"
                                value="" required />
                        </div>
                        <div class="col-md-3">
                            <label>Appointment Time</label>
                            <input
                                type="time"
                                class="form-control"
                                name="booking_time"
                                id="booking_time"
                                value="" required />
                        </div>
                        <div class="col-md-3">
                            <label>Ambulance</label>
                            <input
                                type="text"
                                class="form-control"
                                name="vehicle_name"
                                id="vehicle_name"
                                value="" required />
                        </div>
                        <div class="col-md-3">
                            <label>Driver</label>
                            <input
                                type="text"
                                class="form-control"
                                name="driver_name"
                                id="driver_name"
                                value="" required />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label>Hospital ID</label>
                            <input type="text" class="form-control" name="hospital_id" id="hospital_id" required/>
                        </div>
                        <div class="col-md-3">
                            <label>Transferring From</label>
                            <input type="text" class="form-control" name="transfering_from" id="transfering_from" required/>
                        </div>
                        <div class="col-md-3">
                            <label>Present Diagnosis</label>
                            <input type="text" class="form-control" name="present_diagnosis" id="present_diagnosis" required/>
                        </div>
                        <div class="col-md-3">
                            <label>Type of Discharge</label>
                            <select
                                class="form-select"
                                aria-label="Default select example" name="request_reason" id="request_reason" required>
                                <option value="">Select</option>
                                @foreach(['DAMA', 'LAMA', 'Discharge', 'Death'] as $data)
                                <option value="{{@$data}}">{{@$data}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label>Doctor Accompanying</label>
                            <input type="text" class="form-control" name="doctor" id="doctor" required/>
                        </div>
                        <div class="col-md-3">
                            <label>Place of Transfer</label>
                            <input type="text" class="form-control" name="destination" id="destination" required/>
                        </div>
                        <div class="col-md-3">
                            <label>Consent Taken (LAMA/DAMA)</label>
                            <select
                                class="form-select"
                                aria-label="Default select example" name="consent_taken" id="consent_taken" required>
                                <option value="">Select</option>
                                @foreach(['Yes', 'No'] as $data)
                                <option value="{{@$data}}">{{@$data}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <h6>Ambulance Time</h6>
                        <!-- <div class="col-md-3">
                            <label>Name of Technician</label>
                            <input type="text" class="form-control" name="technician_name" id="technician_name">
                        </div> -->
                        <div class="col-md-4">
                            <label>Time of Departure of Ambulance from hospital</label>
                            <input type="time" class="form-control" name="time_departure" id="time_departure">
                        </div>
                        <div class="col-md-4">
                            <label>Time of Reaching to the Destination</label>
                            <input type="time" class="form-control" name="time_reached_destination" id="time_reached_destination">
                        </div>
                        <div class="col-md-4">
                            <label>Time of Reaching back to ED</label>
                            <input type="time" class="form-control" name="time_reached_back" id="time_reached_back">
                        </div>
                    </div>

                    <div class="row mt-3">

                        <h6>Vital Signs</h6>
                        <hr>
                        <h6 class="mt-2">Before Recieving in the Ambulance</h6>
                        <div class="col-md-2">
                            <label>Temp</label>
                            <input type="text" class="form-control" name="temp_before" id="temp_before">
                        </div>
                        <div class="col-md-2">
                            <label>BP</label>
                            <input type="text" class="form-control" name="bp_before" id="bp_before">
                        </div>
                        <div class="col-md-2">
                            <label>HR</label>
                            <input type="text" class="form-control" name="hr_before" id="hr_before">
                        </div>
                        <div class="col-md-2">
                            <label>Rep</label>
                            <input type="text" class="form-control" name="rep_before" id="rep_before">
                        </div>
                        <div class="col-md-2">
                            <label>SPO2</label>
                            <input type="text" class="form-control" name="spo2_before" id="spo2_before">
                        </div>

                        <h6 class="mt-2">During Transportation</h6>
                        <div id="transportation-container">
                            <div class="row mb-2 transportation-row align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label">Time</label>
                                    <input type="time" class="form-control" name="time_current[]" />
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Temp</label>
                                    <input type="text" class="form-control" name="temp_current[]" />
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">BP</label>
                                    <input type="text" class="form-control" name="bp_current[]" />
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">HR</label>
                                    <input type="text" class="form-control" name="hr_current[]" />
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Rep</label>
                                    <input type="text" class="form-control" name="rep_current[]" />
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">SPO2</label>
                                    <input type="text" class="form-control" name="spo2_current[]" />
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-success btn-sm mt-4 add-transportation-row">+</button>
                                </div>
                            </div>
                        </div>

                        <h6 class="mt-2">After Reaching While Handovering</h6>
                        <div class="col-md-2">
                            <label>Temp</label>
                            <input type="text" class="form-control" name="temp_after" id="temp_after">
                        </div>
                        <div class="col-md-2">
                            <label>BP</label>
                            <input type="text" class="form-control" name="bp_after" id="bp_after">
                        </div>
                        <div class="col-md-2">
                            <label>HR</label>
                            <input type="text" class="form-control" name="hr_after" id="hr_after">
                        </div>
                        <div class="col-md-2">
                            <label>Rep</label>
                            <input type="text" class="form-control" name="rep_after" id="rep_after">
                        </div>
                        <div class="col-md-2">
                            <label>SPO2</label>
                            <input type="text" class="form-control" name="spo2_after" id="spo2_after">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Ordered By</label>
                            <input type="text" class="form-control" name="order_time" id="order_time">
                        </div>
                        <div class="col-md-6">
                            <label>Ventilator Mode / Settings</label>
                            <input type="text" class="form-control" name="setting_time" id="setting_time">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Medication / Consumables Used</label>
                            <textarea class="form-control" name="medication" id="medication" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label>Open Remarks</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="3" required></textarea>
                        </div>
                    </div>

                    <div class="col-md-12 text-center mt-3">
                        <button
                            class="btn btn-success"
                            id="raise-btn"
                            type="submit">
                            Submit
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    // $(document).ready(function() {
    //     const today = new Date().toISOString().split('T')[0];
    //     document.getElementById("booking_date").min = today;
    // });
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
                                        data-bs-target="#requestModal">
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
        $('#allot-btn').hide();
        $('.view-btn').on('click', function() {
            var id = $(this).data('id');
            $('#allocation_id').val($(this).data('allocation_id') || '');
            $('#technician_id').val($(this).data('technician_id') || '');
            $('#booking_date').val($(this).data('booking_date') || '');
            $('#booking_time').val($(this).data('booking_time') || '');
            $('#patient_name').val($(this).data('patient_name') || '');
            $('#patient_age').val($(this).data('patient_age') || '');
            $('#patient_sex').val($(this).data('patient_sex') || '');
            $('#contact_no').val($(this).data('contact_no') || '');
            $('#vehicle_name').val($(this).data('vehicle_name') || '');
            $('#driver_name').val($(this).data('driver_name') || '');
            $('#hospital_id').val($(this).data('hospital_id') || '');
            $('#transfering_from').val($(this).data('transfering_from') || '');
            $('#present_diagnosis').val($(this).data('present_diagnosis') || '');
            $('#request_reason').val($(this).data('request_reason') || '');
            $('#doctor').val($(this).data('doctor') || '');
            $('#destination').val($(this).data('destination') || '');
            $('#consent_taken').val($(this).data('consent_taken') || '');
            $('#time_departure').val($(this).data('time_departure') || '');
            $('#time_reached_destination').val($(this).data('time_reached_destination') || '');
            $('#time_reached_back').val($(this).data('time_reached_back') || '');
            $('#temp_before').val($(this).data('temp_before') || '');
            $('#bp_before').val($(this).data('bp_before') || '');
            $('#hr_before').val($(this).data('hr_before') || '');
            $('#rep_before').val($(this).data('rep_before') || '');
            $('#spo2_before').val($(this).data('spo2_before') || '');
            $('#temp_after').val($(this).data('temp_after') || '');
            $('#bp_after').val($(this).data('bp_after') || '');
            $('#hr_after').val($(this).data('hr_after') || '');
            $('#rep_after').val($(this).data('rep_after') || '');
            $('#spo2_after').val($(this).data('spo2_after') || '');
            $('#medication').val($(this).data('medication') || '');
            $('#order_time').val($(this).data('order_time') || '');
            $('#setting_time').val($(this).data('setting_time') || '');
            $('#remarks').val($(this).data('remarks') || '');

            // $('#raise-btn').hide();
            // $('#allot-btn').show();
            $('#requestForm').attr('action', '/technician/update/' + id);
            // $('#vehicleForm').append('<input type="hidden"  name="_method" value="POST">');
            $('#raise-btn').text('Update');
            $('#modalTitle').text('Technician Request');

            // Populate the transportation data
            const time_current  = String($(this).data('time_current') || '').split(',');
            const temp_current  = String($(this).data('temp_current') || '').split(',');
            const bp_current    = String($(this).data('bp_current') || '').split(',');
            const hr_current    = String($(this).data('hr_current') || '').split(',');
            const rep_current   = String($(this).data('rep_current') || '').split(',');
            const spo2_current  = String($(this).data('spo2_current') || '').split(',');

            // Clear existing rows
            $('#transportation-container').empty();
            // Append new rows based on the data
            for (let i = 0; i < time_current.length; i++) {
                const isLast = i === time_current.length - 1;
                const plusButton = isLast
                    ? `<div class="col-md-2 d-flex align-items-end">
                        <!-- <button type="button" class="btn btn-success btn-sm ms-2 mt-auto add-transportation-row">+</button> -->
                    </div>`
                    : '';

                const row = `
                    <div class="row mb-2 transportation-row">
                        <div class="col-md-2">
                            <label>Time</label>
                            <input type="time" class="form-control" name="time_current[]" value="${time_current[i] ?? ''}" />
                        </div>
                        <div class="col-md-2">
                            <label>Temp</label>
                            <input type="text" class="form-control" name="temp_current[]" value="${temp_current[i] ?? ''}" />
                        </div>
                        <div class="col-md-2">
                            <label>BP</label>
                            <input type="text" class="form-control" name="bp_current[]" value="${bp_current[i] ?? ''}" />
                        </div>
                        <div class="col-md-2">
                            <label>HR</label>
                            <input type="text" class="form-control" name="hr_current[]" value="${hr_current[i] ?? ''}" />
                        </div>
                        <div class="col-md-2">
                            <label>REP</label>
                            <input type="text" class="form-control" name="rep_current[]" value="${rep_current[i] ?? ''}" />
                        </div>
                        <div class="col-md-2">
                            <label>SPO2</label>
                            <input type="text" class="form-control" name="spo2_current[]" value="${spo2_current[i] ?? ''}" />
                        </div>
                        ${plusButton}
                    </div>
                `;

                $('#transportation-container').append(row);
            }
        });

        let originalTransportationRow;

        $(function () {
            originalTransportationRow = $('#transportation-container').html();
        });

        $('#requestModal').on('hidden.bs.modal', function() {
            $('#allocation_id').val('');
            $('#technician_id').val('');
            $('#booking_date').val('');
            $('#patient_name').val('');
            $('#patient_age').val('');
            $('#patient_sex').val('');
            $('#hospital_id').val('');
            $('#transfering_from').val('');
            $('#present_diagnosis').val('');
            $('#request_reason').val('');
            $('#doctor').val('');
            $('#destination').val('');
            $('#consent_taken').val('');
            $('#time_current').val('');
            $('#temp_current').val('');
            $('#bp_current').val('');
            $('#hr_current').val('');
            $('#rep_current').val('');
            $('#spo2_current').val('');
            $('#time_departure').val('');
            $('#time_reached_destination').val('');
            $('#time_reached_back').val('');
            $('#temp_before').val('');
            $('#bp_before').val('');
            $('#hr_before').val('');
            $('#rep_before').val('');
            $('#spo2_before').val('');
            $('#temp_after').val('');
            $('#bp_after').val('');
            $('#hr_after').val('');
            $('#rep_after').val('');
            $('#spo2_after').val('');
            $('#medication').val('');
            $('#order_time').val('');
            $('#setting_time').val('');
            $('#remarks').val('');

            $('#raise-btn').text('Submit');
            $('form').attr('action', '/technician/store');
            $('form').attr('method', 'POST');

            $('#transportation-container').html(originalTransportationRow);

        });
    });
</script>

<script>
    $(document).on('click', '.add-transportation-row', function () {
        var $originalRow = $(this).closest('.transportation-row');
        var $clonedRow = $originalRow.clone();

        $clonedRow.find('input').val('');

        $('#transportation-container').append($clonedRow);
    });
</script>

<script>
    $(document).ready(function() {
        $('.create-btn').on('click', function() {
            $('#allocation_id').val($(this).data('allocation_id') || '');
            $('#technician_id').val($(this).data('technician_id') || '');
            $('#booking_date').val($(this).data('booking_date') || '');
            $('#booking_time').val($(this).data('booking_time') || '');
            $('#patient_name').val($(this).data('name') || '');
            $('#patient_age').val($(this).data('age') || '');
            $('#patient_sex').val($(this).data('sex') || '');
            $('#transfereing_from').val($(this).data('patient_location') || '');
            $('#destination').val($(this).data('destination') || '');
            $('#contact_no').val($(this).data('contact_no') || '');
            $('#vehicle_name').val($(this).data('vehicle_name') || '');
            $('#driver_name').val($(this).data('driver_name') || '');

            $('#requestForm').attr('action', '/technician/store');
            // $('#vehicleForm').append('<input type="hidden"  name="_method" value="POST">');
            $('#raise-btn').text('Submit');
            $('#modalTitle').text('Technician Request');
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
                $('#confirmedYes').show();
                $('#confirmedNo').hide();
                $('#allot-btn').text('Book Ambulance');
            } else if (value === 'no') {
                $('#confirmedYes').hide();
                $('#confirmedNo').show();
                $('#allot-btn').text('Allot Ambulance');
            } else {
                $('#confirmedYes, #confirmedNo').hide();
            }
        });
        $('#technician').on('change', function() {
            const val = $(this).val();
            if (val === 'yes') {
                $('#technicianYes').show();
                $('#technicianNo').hide();
            } else if (val === 'no') {
                $('#technicianYes').hide();
                $('#technicianNo').show();
            } else {
                $('#technicianYes, #technicianNo').hide();
            }
        });
    });
</script>

<script>
    $('#vehicle_id').on('change', function() {
        var driverId = $(this).find(':selected').data('driver_id');
        $('#driver_id').val(driverId || '');
    });
</script>

@endsection