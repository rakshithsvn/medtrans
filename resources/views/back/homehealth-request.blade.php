@extends('layouts.back.index')

@section('content')

<section class="section">
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <!-- Filter Form and Action Buttons -->
            <form action="{{ route('home-health.index') }}" method="POST">
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
                    @if(in_array($user->register_by, ['ADMIN', 'SUPERVISOR', 'DEPARTMENT']))
                    <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                        <button type="button" id="new-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestModal">
                            New Request
                        </button>
                    </div>
                    @endif
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
                            <th>Appointment Time</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Contact No.</th>
                            <th>Type of Service</th>
                            <th></th>
                        </tr>

                        <tr>
                            @forelse(@$homehealthRequests as $data)
                            <td>{{ \Carbon\Carbon::parse(@$data->booking_date)->format('d/m/Y') }}</td>
                            <td>{{ $data->booking_time ? \Carbon\Carbon::createFromFormat('H:i:s', @$data->booking_time)->format('g:i A') : '-' }}</td>
                            <td>{{@$data->name}}</td>
                            <td>{{@$data->age}}</td>
                            <td>{{@$data->sex}}</td>
                            <td>{{@$data->contact_no}}</td>
                            <td>{{@$data->service_type}}</td>
                            <td>
                                @if(in_array($user->register_by, ['ADMIN', 'SUPERVISOR']))
                                <button
                                    class="btn btn-success request-btn" data-bs-toggle="modal" data-bs-target="#requestModal"
                                    data-id="{{ $data->id }}"
                                    data-request_date="{{ $data->request_date }}"
                                    data-request_time="{{ $data->request_time }}"
                                    data-booking_date="{{ $data->booking_date }}"
                                    data-booking_time="{{ $data->booking_time }}"
                                    data-name="{{ $data->name }}"
                                    data-age="{{ $data->age }}"
                                    data-sex="{{ $data->sex }}"
                                    data-aj_patient="{{ $data->aj_patient }}"
                                    data-address="{{ $data->address }}"
                                    data-landmark="{{ $data->landmark }}"
                                    data-appoint_by="{{ $data->appoint_by }}"
                                    data-contact_no="{{ $data->contact_no }}"
                                    data-service_type="{{ $data->service_type }}"
                                    data-status="{{ $data->status }}"
                                    data-user_id="{{ $data->user_id }}">
                                    Allot Vehicle
                                </button>
                                    @else
                                        <button
                                        class="btn btn-success view-btn" data-bs-toggle="modal" data-bs-target="#requestModal"
                                        data-id="{{ $data->id }}"
                                        data-update_flag="{{ $data->update_flag }}"
                                        data-request_date="{{ $data->request_date }}"
                                        data-request_time="{{ $data->request_time }}"
                                        data-booking_date="{{ $data->booking_date }}"
                                        data-booking_time="{{ $data->booking_time }}"
                                        data-name="{{ $data->name }}"
                                        data-age="{{ $data->age }}"
                                        data-sex="{{ $data->sex }}"
                                        data-aj_patient="{{ $data->aj_patient }}"
                                        data-address="{{ $data->address }}"
                                        data-landmark="{{ $data->landmark }}"
                                        data-appoint_by="{{ $data->appoint_by }}"
                                        data-contact_no="{{ $data->contact_no }}"
                                        data-service_type="{{ $data->service_type }}"
                                        data-status="{{ $data->status }}"
                                        data-request="request">View</button>
                                    @endif
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
                    {{ @$homehealthRequests->links() }}
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">Approved Requests</h5>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>Date</th>
                            <th>Appointment Time</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Contact No.</th>
                            <th>Type of Service</th>
                            <th></th>
                        </tr>

                        <tr>
                            @forelse(@$transportAllocations as $data )
                            <td>{{ \Carbon\Carbon::parse(@$data->homeHealthRequest->booking_date)->format('d/m/Y') }}</td>
                            <td>{{ @$data->homeHealthRequest->booking_time ? \Carbon\Carbon::createFromFormat('H:i:s', @$data->homeHealthRequest->booking_time)->format('g:i A') : '-' }}</td>
                            <td>{{@$data->homeHealthRequest->name}}</td>
                            <td>{{@$data->homeHealthRequest->age}}</td>
                            <td>{{@$data->homeHealthRequest->sex}}</td>
                            <td>{{@$data->homeHealthRequest->contact_no}}</td>
                            <td>{{@$data->homeHealthRequest->service_type}}</td>
                            <td> <button
                                    class="btn btn-success view-btn" data-bs-toggle="modal" data-bs-target="#requestModal"
                                    data-id="{{ $data->id }}"
                                    data-status="{{ $data->vehicleMovement->status }}"
                                    data-type="{{ $data->homeHealthRequest->type }}"
                                    data-request_date="{{ $data->homeHealthRequest->request_date }}"
                                    data-request_time="{{ $data->homeHealthRequest->request_time }}"
                                    data-booking_date="{{ $data->homeHealthRequest->booking_date }}"
                                    data-booking_time="{{ $data->homeHealthRequest->booking_time }}"
                                    data-name="{{ $data->homeHealthRequest->name }}"
                                    data-age="{{ $data->homeHealthRequest->age }}"
                                    data-sex="{{ $data->homeHealthRequest->sex }}"
                                    data-aj_patient="{{ $data->homeHealthRequest->aj_patient }}"
                                    data-address="{{ $data->homeHealthRequest->address }}"
                                    data-landmark="{{ $data->homeHealthRequest->landmark }}"
                                    data-appoint_by="{{ $data->homeHealthRequest->appoint_by }}"
                                    data-contact_no="{{ $data->homeHealthRequest->contact_no }}"
                                    data-service_type="{{ $data->homeHealthRequest->service_type }}"
                                    data-status="{{ $data->homeHealthRequest->status }}"
                                    data-vehicle="{{ $data->vehicle->reg_no }}"
                                    data-driver="{{ $data->driver->name }}"
                                    data-request="allocate">View</button></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">No data found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <nav aria-label="Page navigation" class="my-0">
                    {{ @$transportAllocations->links() }}
                </nav>
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
                            <th>Appointment Time</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Contact No.</th>
                            <th>Type of Service</th>
                            <th></th>
                        </tr>

                        <tr>
                            @forelse(@$transportCompleted as $data )
                            <td>{{ \Carbon\Carbon::parse(@$data->homeHealthRequest->booking_date)->format('d/m/Y') }}</td>
                            <td>{{ $data->homeHealthRequest->booking_time ? \Carbon\Carbon::createFromFormat('H:i:s', @$data->homeHealthRequest->booking_time)->format('g:i A') : '-' }}</td>
                            <td>{{@$data->homeHealthRequest->name}}</td>
                            <td>{{@$data->homeHealthRequest->age}}</td>
                            <td>{{@$data->homeHealthRequest->sex}}</td>
                            <td>{{@$data->homeHealthRequest->contact_no}}</td>
                            <td>{{@$data->homeHealthRequest->service_type}}</td>
                            <td> <button
                                    class="btn btn-success view-btn" data-bs-toggle="modal" data-bs-target="#requestModal"
                                    data-id="{{ $data->id }}"
                                    data-status="{{ $data->vehicleMovement->status }}"
                                    data-type="{{ $data->homeHealthRequest->type }}"
                                    data-request_date="{{ $data->homeHealthRequest->request_date }}"
                                    data-request_time="{{ $data->homeHealthRequest->request_time }}"
                                    data-booking_date="{{ $data->homeHealthRequest->booking_date }}"
                                    data-booking_time="{{ $data->homeHealthRequest->booking_time }}"
                                    data-name="{{ $data->homeHealthRequest->name }}"
                                    data-age="{{ $data->homeHealthRequest->age }}"
                                    data-sex="{{ $data->homeHealthRequest->sex }}"
                                    data-aj_patient="{{ $data->homeHealthRequest->aj_patient }}"
                                    data-address="{{ $data->homeHealthRequest->address }}"
                                    data-landmark="{{ $data->homeHealthRequest->landmark }}"
                                    data-appoint_by="{{ $data->homeHealthRequest->appoint_by }}"
                                    data-contact_no="{{ $data->homeHealthRequest->contact_no }}"
                                    data-service_type="{{ $data->homeHealthRequest->service_type }}"
                                    data-status="{{ $data->homeHealthRequest->status }}"
                                    data-vehicle="{{ $data->vehicle->reg_no }}"
                                    data-driver="{{ $data->driver->name }}"
                                    data-request="allocate">View</button></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">No data found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <nav aria-label="Page navigation" class="my-0">
                    {{ @$transportCompleted->links() }}
                </nav>
            </div>
        </div>

        <div class="row">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Cancelled Requests</h5>
                </div>
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
                                <td colspan="7" class="text-center text-muted py-3">No data found.</td>
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
                    <i class="fas fa-user-md me-2"></i>Home Health Appointment
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
                    action="{{ route('home-health.store') }}"
                    method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="id" name="id" />
                    <input type="hidden" id="type" name="type" />
                    <input type="hidden" id="status" name="status" value="Pending" />
                    <input type="hidden" id="user_id" name="user_id" value="" />

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Request date</label>
                            <input
                                type="date"
                                class="form-control"
                                name="request_date"
                                id="request_date"
                                value="{{ now()->toDateString() }}" readOnly required />
                        </div>
                        <div class="col-md-6">
                            <label>Request time</label>
                            <input type="time" class="form-control" name="request_time" id="request_time" value="{{ now()->format('H:i') }}" required disabled/>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Appointment date</label>
                            <input
                                type="date"
                                class="form-control"
                                name="booking_date"
                                id="booking_date"
                                value="" required />
                        </div>
                        <div class="col-md-6">
                            <label>Appointment time</label>
                            <input type="time" class="form-control" name="booking_time" id="booking_time" required/>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="name" required/>
                        </div>
                        <div class="col-md-3">
                            <label>Age</label>
                            <input type="number" class="form-control" name="age" id="age" required/>
                        </div>
                        <div class="col-md-3">
                            <label>Sex</label>
                            <select
                                class="form-select"
                                aria-label="Default select example" name="sex" id="sex" required>
                                <option value="">Select</option>
                                @foreach(['Male', 'Female'] as $data)
                                <option value="{{@$data}}">{{@$data}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>AJ patient</label>
                            <select
                                class="form-select"
                                aria-label="Default select example" name="aj_patient" id="aj_patient" required>
                                <option value="">Select</option>
                                @foreach(['Yes', 'No'] as $data)
                                <option value="{{@$data}}">{{@$data}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Complete address</label>
                            <input type="text" class="form-control" name="address" id="address" required/>
                        </div>
                        <div class="col-md-6">
                            <label>Landmark</label>
                            <input type="text" class="form-control" name="landmark" id="landmark" required/>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label>Appointment taken by</label>
                            <input type="text" class="form-control" name="appoint_by" id="appoint_by" required/>
                        </div>
                        <div class="col-md-4">
                            <label>Contact No.</label>
                            <input type="number" class="form-control" name="contact_no" id="contact_no" required/>
                        </div>
                        <div class="col-md-4">
                            <label>Type of service</label>
                            <select
                                class="form-select"
                                aria-label="Default select example" name="service_type" id="service_type" required>
                                <option value="">Select</option>
                                @foreach(@$services as $service)
                                <option value="{{@$service}}">{{@$service}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3" id="allot-view">
                        <h5 class="title">Allot Vehicle</h5>
                        <div class="col-md-6">
                            <label>Vehicle</label>
                            <input type="text" class="form-control" name="" id="v_vehicle" required readOnly/>
                        </div>
                        <div class="col-md-6">
                            <label>Driver name</label>
                            <input type="text" class="form-control" name="" id="v_driver" required readOnly/>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center mt-3">
                            <button
                                class="btn btn-success"
                                id="raise-btn"
                                type="submit">
                                Raise a Request
                            </button>
                            <button
                                class="btn btn-success"
                                id="cancel-btn"
                                type="submit">
                                Cancel Trip
                            </button>
                            <a class="btn btn-secondary" id="allot-btn" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="true" aria-controls="collapseExample">
                                Allot Vehicle
                            </a>
                        </div>
                    </div>

                    <div class="collapse" id="collapseExample" style="">
                        <div class="row mt-3">
                            <h5 class="title">Allot Vehicle</h5>
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
                                <label>Driver name</label>
                                <select class="form-select" aria-label="Default select example" name="driver_id" id="driver_id">
                                    <option value="">Select</option>
                                    @foreach(@$drivers as $driver=>$id)
                                    <option value="{{@$id}}">{{@$driver}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center mt-3">
                                <button
                                    class="btn btn-success"
                                    id="raise-btn"
                                    type="submit">
                                    Submit
                                </button>
                            </div>
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
                    <input type="hidden" name="type" value="home-health" />
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
        $('#cancel-btn').hide();
        $('#allot-view').hide();

        $('.request-btn').on('click', function() {
            var id = $(this).data('id');
            $('#id').val($(this).data('id') || '');
            $('#request_date').val($(this).data('request_date') || '');
            $('#request_time').val($(this).data('request_time') || '');
            $('#booking_date').val($(this).data('booking_date') || '');
            $('#booking_time').val($(this).data('booking_time') || '');
            $('#name').val($(this).data('name') || '');
            $('#age').val($(this).data('age') || '');
            $('#sex').val($(this).data('sex') || '');
            $('#aj_patient').val($(this).data('aj_patient') || '');
            $('#address').val($(this).data('address') || '');
            $('#landmark').val($(this).data('landmark') || '');
            $('#appoint_by').val($(this).data('appoint_by') || '');
            $('#contact_no').val($(this).data('contact_no') || '');
            $('#service_type').val($(this).data('service_type') || '');
            $('#user_id').val($(this).data('user_id') || '');
            $('#status').val('Completed');
            $('#vehicle_id').attr('required', true);
            $('#driver_id').attr('required', true);

            $('#raise-btn').hide();
            $('#cancel-btn').hide();
            $('#allot-view').hide();
            $('#allot-btn').show();
            $('#booking_date').removeAttr('min');
            $('#requestForm').attr('action', '/home-health/allocate/');
            // $('#vehicleForm').append('<input type="hidden"  name="_method" value="POST">');
            // $('#raise-btn').text('Complete Request');
            $('#modalTitle').text('Home Health Appointment');
        });

        $('.view-btn').on('click', function() {
                var id = $(this).data('id');
                $('#id').val($(this).data('id') || '');
                $('#type').val($(this).data('type') || '');
                $('#request_date').val($(this).data('request_date') || '');
                $('#request_time').val($(this).data('request_time') || '');
                $('#booking_date').val($(this).data('booking_date') || '');
                $('#booking_time').val($(this).data('booking_time') || '');
                $('#name').val($(this).data('name') || '');
                $('#age').val($(this).data('age') || '');
                $('#sex').val($(this).data('sex') || '');
                $('#aj_patient').val($(this).data('aj_patient') || '');
                $('#address').val($(this).data('address') || '');
                $('#landmark').val($(this).data('landmark') || '');
                $('#appoint_by').val($(this).data('appoint_by') || '');
                $('#contact_no').val($(this).data('contact_no') || '');
                $('#service_type').val($(this).data('service_type') || '');
                $('#v_vehicle').val($(this).data('vehicle') || '');
                $('#v_driver').val($(this).data('driver') || '');

                $(this).data('status') == 'allot' ? $('#cancel-btn').show() : $('#cancel-btn').hide();
                $('#allot-view').show();
                // $('#raise-btn').hide();
                $('#allot-btn').hide();
                $('#cancelForm').attr('action', '/trip-cancel/' + id);
                // $('#requestForm').attr('onsubmit', "return confirm('Are you sure?\\nDo you want to cancel the trip?')");
                // $('#raise-btn').data('cancel-id', id);
                // $('#raise-btn').text('Cancel Trip');
                $(this).data('update_flag') ? $('#raise-btn').hide() : $('#raise-btn').show();
                $('#requestForm').attr('action', '/home-health/update/' + id);
                $('#raise-btn').text('Update');
                $('#modalTitle').text('Transport Requisition');
                $('#vehicle_id').attr('required', false);
                $('#driver_id').attr('required', false);
                $('#booking_date').removeAttr('min');
                if ($(this).data('request') == "request") {
                    $('#cancel-btn').show();
                    $('#cancelForm').attr('action', '/trip-cancel/' + id + '/request');
                    $('#allot-view').hide();
                }
                if ($(this).data('request') == "allocate") {
                    $('#raise-btn').hide();
                }
            });

            $('#cancel-btn').on('click', function(e) {
                e.preventDefault();
                $('#cancel_reason').val('');
                $('#cancelReasonModal').modal('show');
            });

        $('#requestModal').on('hidden.bs.modal', function() {
            $('#id').val('');
            // $('#request_date').val('');
            // $('#request_time').val('');
            $('#booking_date').val('');
            $('#booking_time').val('');
            $('#name').val('');
            $('#age').val('');
            $('#sex').val('');
            $('#aj_patient').val('');
            $('#address').val('');
            $('#landmark').val('');
            $('#appoint_by').val('');
            $('#contact_no').val('');
            $('#service_type').val('');
            $('#status').val('Pending');

            $('#raise-btn').show();
            $('#allot-btn').hide();
            $('#cancel-btn').hide();
            $('#allot-view').hide();
            $('#raise-btn').text('Raise a Request');
            $('#requestForm').attr('action', '/home-health/store');
            $('#requestForm').removeAttr('onsubmit');
            $('#vehicle_id').attr('required', false);
            $('#driver_id').attr('required', false);

            const today = new Date().toISOString().split('T')[0];
            document.getElementById("booking_date").min = today;

            const collapseElement = document.getElementById('collapseExample');
            const bsCollapse = new bootstrap.Collapse(collapseElement, {
                toggle: false
            });
            bsCollapse.hide();
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#allocate-btn').on('click', function() {
            $('#ambulance_request_id').val($(this).data('id') || '');
            $('#a_patient_name').val($(this).data('patient_name') || '');
            $('#a_mr_no').val($(this).data('mr_no') || '');
            $('#a_ambulance_type').val($(this).data('a_ambulance_type') || '');
            $('#h_requester_name').val($(this).data('requester_name') || '');
            $('#h_requester_relation').val($(this).data('requester_relation') || '');
            $('#h_form_date').val($(this).data('form_date') || '');
            $('#h_patient_name').val($(this).data('patient_name') || '');
            $('#h_patient_age').val($(this).data('patient_age') || '');
            $('#h_patient_sex').val($(this).data('patient_sex') || '');
            $('#h_from_location').val($(this).data('from_location') || '');
            $('#h_patient_location').val($(this).data('patient_location') || '');
            $('#h_reason').val($(this).data('reason') || '');
            $('#h_technician_name').val($(this).data('technician_name') || '');
            $('#h_technician_phone').val($(this).data('technician_phone') || '');
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