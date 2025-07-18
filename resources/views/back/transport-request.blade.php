    @extends('layouts.back.index')

    @section('content')
    <section class="section">
        <div class="card shadow-sm border-0 global-font">
            <div class="card-body p-4">
                <!-- Filter Form and Action Buttons -->
                <form action="{{ route('transport.index') }}" method="POST">
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
                        <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                            <button type="button" id="new-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestModal">
                                New Request
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm p-4">
            <div class="row">
                <div class="col-md-12">
                    @if(in_array($user->register_by, ['ADMIN', 'SUPERVISOR']))
                    <h5 class="text-white p-2 bg-secondary">Received Requests</h5>
                    @else
                    <h5 class="text-white p-2 bg-secondary">Pending Requests</h5>
                    @endif
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Employee Name</th>
                                <th>Employee No.</th>
                                <th>Designation</th>
                                <th>Department</th>
                                <th>Destination</th>
                                <th>Reason for Request</th>
                                <th></th>
                            </tr>

                            <tr>
                                @forelse(@$transportRequests as $data)
                                <td>{{ \Carbon\Carbon::parse(@$data->booking_date)->format('d/m/Y') }}</td>
                                <td>{{ $data->booking_time ? \Carbon\Carbon::createFromFormat('H:i:s', @$data->booking_time)->format('g:i A') : '-' }}</td>
                                <td>{{@$data->employee_name}}</td>
                                <td>{{@$data->employee_no}}</td>
                                <td>{{@$data->designation}}</td>
                                <td>{{@$data->department}}</td>
                                <td>{{@$data->destination}}</td>
                                <td>{{@$data->request_type}}</td>
                                <td>
                                    @if(in_array($user->register_by, ['ADMIN', 'SUPERVISOR']))
                                    <a
                                        href="#"
                                        class="btn btn-success request-btn" data-bs-toggle="modal" data-bs-target="#requestModal"
                                        data-id="{{ $data->id }}"
                                        data-date="{{ $data->date }}"
                                        data-employee_name="{{ $data->employee_name }}"
                                        data-booking_date="{{ $data->booking_date }}"
                                        data-booking_time="{{ $data->booking_time }}"
                                        data-employee_no="{{ $data->employee_no }}"
                                        data-designation="{{ $data->designation }}"
                                        data-department="{{ $data->department }}"
                                        data-destination="{{ $data->destination }}"
                                        data-request_type="{{ $data->request_type }}"
                                        data-contact_no="{{ $data->contact_no }}"
                                        data-reason="{{ $data->reason }}"
                                        data-user_id="{{ $data->user_id }}"                                   
                                        >Allot Vehicle</a>
                                    @else
                                    <a
                                        href="#"
                                        class="btn btn-success view-btn" data-bs-toggle="modal" data-bs-target="#requestModal"
                                        data-id="{{ $data->id }}"
                                        data-update_flag="{{ $data->update_flag }}"
                                        data-date="{{ $data->date }}"
                                        data-employee_name="{{ $data->employee_name }}"
                                        data-booking_date="{{ $data->booking_date }}"
                                        data-booking_time="{{ $data->booking_time }}"
                                        data-employee_no="{{ $data->employee_no }}"
                                        data-designation="{{ $data->designation }}"
                                        data-department="{{ $data->department }}"
                                        data-destination="{{ $data->destination }}"
                                        data-request_type="{{ $data->request_type }}"
                                        data-contact_no="{{ $data->contact_no }}"
                                        data-reason="{{ $data->reason }}"
                                        data-request="request">View
                                    </a>
                                    @endif
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
                        {{ @$transportRequests->links() }}
                    </nav>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Approved Requests</h5>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Booking Date</th>
                                <th>Booking Time</th>
                                <th>Driver Name</th>
                                <th>Vehicle No</th>
                                <th>Assigned By</th>
                                <th>Destination</th>
                                <th></th>
                            </tr>

                            @forelse(@$transportAllocations as $data )
                            <tr>
                                <td>{{ \Carbon\Carbon::parse(@$data->transportRequest->booking_date)->format('d/m/Y') }}</td>
                                <td>{{ $data->transportRequest->booking_time ? \Carbon\Carbon::createFromFormat('H:i:s', @$data->transportRequest->booking_time)->format('g:i A') : '-' }}</td>
                                <td>{{@$data->driver->name}}</td>
                                <td>{{@$data->vehicle->reg_no}}</td>
                                <td>{{@$data->supervisor->name ?? 'Admin'}}</td>
                                <td>{{$data->transportRequest->destination}}</td>
                                <td class="text-center">
                                    <a
                                        href="#"
                                        class="btn btn-success view-btn" data-bs-toggle="modal" data-bs-target="#requestModal"
                                        data-id="{{ $data->id }}"
                                        data-type="{{ $data->type }}"
                                        data-status="{{ $data->vehicleMovement->status }}"
                                        data-date="{{ $data->transportRequest->date }}"
                                        data-employee_name="{{ $data->transportRequest->employee_name }}"
                                        data-booking_date="{{ $data->transportRequest->booking_date }}"
                                        data-booking_time="{{ $data->transportRequest->booking_time }}"
                                        data-employee_no="{{ $data->transportRequest->employee_no }}"
                                        data-designation="{{ $data->transportRequest->designation }}"
                                        data-department="{{ $data->transportRequest->department }}"
                                        data-destination="{{ $data->transportRequest->destination }}"
                                        data-request_type="{{ $data->transportRequest->request_type }}"
                                        data-contact_no="{{ $data->transportRequest->contact_no }}"
                                        data-reason="{{ $data->transportRequest->reason }}"
                                        data-vehicle="{{ $data->vehicle->reg_no }}"
                                        data-driver="{{ $data->driver->name }}"
                                        data-request="allocate">View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No data found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation" class="my-0">
                        {{ @$transportAllocations->links() }}
                    </nav>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Completed Requests</h5>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Booking Date</th>
                                <th>Booking Time</th>
                                <th>Driver Name</th>
                                <th>Vehicle No</th>
                                <th>Assigned By</th>
                                <th>Destination</th>
                                <th></th>
                            </tr>

                            @forelse(@$transportCompleted as $data )
                            <tr>
                                <td>{{ \Carbon\Carbon::parse(@$data->transportRequest->booking_date)->format('d/m/Y') }}</td>
                                <td>{{ $data->transportRequest->booking_time ? \Carbon\Carbon::createFromFormat('H:i:s', @$data->transportRequest->booking_time)->format('g:i A') : '-' }}</td>
                                <td>{{@$data->driver->name}}</td>
                                <td>{{@$data->vehicle->reg_no}}</td>
                                <td>{{@$data->supervisor->name ?? 'Admin'}}</td>
                                <td>{{$data->transportRequest->destination}}</td>
                                <td class="text-center">
                                    <a
                                        href="#"
                                        class="btn btn-success view-btn" data-bs-toggle="modal" data-bs-target="#requestModal"
                                        data-id="{{ $data->id }}"
                                        data-type="{{ $data->type }}"
                                        data-status="{{ $data->vehicleMovement->status }}"
                                        data-date="{{ $data->transportRequest->date }}"
                                        data-employee_name="{{ $data->transportRequest->employee_name }}"
                                        data-booking_date="{{ $data->transportRequest->booking_date }}"
                                        data-booking_time="{{ $data->transportRequest->booking_time }}"
                                        data-employee_no="{{ $data->transportRequest->employee_no }}"
                                        data-designation="{{ $data->transportRequest->designation }}"
                                        data-department="{{ $data->transportRequest->department }}"
                                        data-destination="{{ $data->transportRequest->destination }}"
                                        data-request_type="{{ $data->transportRequest->request_type }}"
                                        data-contact_no="{{ $data->transportRequest->contact_no }}"
                                        data-reason="{{ $data->transportRequest->reason }}"
                                        data-vehicle="{{ $data->vehicle->reg_no }}"
                                        data-driver="{{ $data->driver->name }}"
                                        data-request="allocate">View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No data found.</td>
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
                    <nav aria-label="Page navigation" class="my-0">
                    
                    </nav>
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
                        <i class="fas fa-user-md me-2"></i>Transport Requisition
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
                        action="{{ route('transport.store') }}"
                        method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="id" name="id" />
                        <input type="hidden" id="type" name="type" />
                        <input type="hidden" id="user_id" name="user_id" />

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label>Date</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    name=""
                                    id="h_form_date"
                                    value="{{ now()->toDateString() }}" readOnly required />
                            </div>

                            <div class="col-md-6">
                                <label>Employee name</label>
                                <input type="text" class="form-control" name="employee_name" id="employee_name" required />
                            </div>
                        </div>
                        <div class="row mt-3">
                            <h5 class="title">Booking Details</h5>

                            <div class="col-md-4">
                                <label>Date</label>
                                <input type="date" class="form-control" name="booking_date" id="booking_date" required />
                            </div>
                            <div class="col-md-4">
                                <label>Time</label>
                                <input type="time" class="form-control" name="booking_time" id="booking_time" required />
                            </div>

                            <div class="col-md-4">
                                <label>Employee No.</label>
                                <input type="text" class="form-control" name="employee_no" id="employee_no" required />
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>Designation</label>
                                <input type="text" class="form-control" name="designation" id="designation" required />
                            </div>
                            <div class="col-md-4">
                                <label>Department</label>
                                <select
                                    class="form-select"
                                    aria-label="Default select example" name="department" id="department" required>
                                    <option value="">Select</option>
                                    @foreach(@$departments as $dept)
                                    <option value="{{@$dept}}">{{@$dept}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Destination</label>
                                <input type="text" class="form-control" name="destination" id="destination" required />
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>Request type</label>
                                <select
                                    class="form-select"
                                    aria-label="Default select example" name="request_type" id="request_type" required>
                                    <option value="">Select</option>
                                    @foreach($requests as $request)
                                    <option value="{{@$request}}">{{@$request}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Description</label>
                                <input type="text" class="form-control" name="reason" id="reason" />
                            </div>
                            <div class="col-md-4">
                                <label>Contact No.</label>
                                <input type="number" class="form-control" name="contact_no" id="contact_no" required/>
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

                        <div class="col-md-12 text-center mt-3">
                            <button
                                class="btn btn-success"
                                id="raise-btn"
                                type="submit">
                                Raise Request
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
                                <div class="col-md-12 text-center mt-4">
                                    <button
                                        class="btn btn-success"
                                        type="submit">
                                        Confirm
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
                    <input type="hidden" name="type" value="transport" />
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
            $('#allot-view').hide();
            $('#allot-btn').hide();
            $('#cancel-btn').hide();

            $('.request-btn').on('click', function() {
                var id = $(this).data('id');
                $('#id').val($(this).data('id') || '');
                $('#date').val($(this).data('date') || '');
                $('#employee_name').val($(this).data('employee_name') || '');
                $('#booking_date').val($(this).data('booking_date') || '');
                $('#booking_time').val($(this).data('booking_time') || '');
                $('#employee_no').val($(this).data('employee_no') || '');
                $('#designation').val($(this).data('designation') || '');
                $('#department').val($(this).data('department') || '');
                $('#destination').val($(this).data('destination') || '');
                $('#reason').val($(this).data('reason') || '');
                $('#request_type').val($(this).data('request_type') || '');
                $('#contact_no').val($(this).data('contact_no') || '');
                $('#user_id').val($(this).data('user_id') || '');
                $('#accept').val('1');

                $('#raise-btn').hide();
                $('#allot-btn').show();
                $('#requestForm').attr('action', '/transport/allocate');
                // $('#vehicleForm').append('<input type="hidden"  name="_method" value="POST">');
                // $('#submit-btn').text('Accept');
                $('#modalTitle').text('Transport Requisition');
                $('#booking_date').removeAttr('min');
                $('#vehicle_id').attr('required', true);
                $('#driver_id').attr('required', true);
            });

            $('.view-btn').on('click', function() {
                var id = $(this).data('id');
                $('#id').val($(this).data('id') || '');
                $('#type').val($(this).data('type') || '');
                $('#date').val($(this).data('date') || '');
                $('#employee_name').val($(this).data('employee_name') || '');
                $('#booking_date').val($(this).data('booking_date') || '');
                $('#booking_time').val($(this).data('booking_time') || '');
                $('#employee_no').val($(this).data('employee_no') || '');
                $('#designation').val($(this).data('designation') || '');
                $('#department').val($(this).data('department') || '');
                $('#destination').val($(this).data('destination') || '');
                $('#reason').val($(this).data('reason') || '');
                $('#request_type').val($(this).data('request_type') || '');
                $('#contact_no').val($(this).data('contact_no') || '');
                $('#v_vehicle').val($(this).data('vehicle') || '');
                $('#v_driver').val($(this).data('driver') || '');

                $('#allot-view').show();
                $(this).data('status') == 'allot' ? $('#cancel-btn').show() : $('#cancel-btn').hide();
                // $('#raise-btn').hide();
                $('#allot-btn').hide();
                $('#cancelForm').attr('action', '/trip-cancel/' + id);
                // $('#requestForm').attr('onsubmit', "return confirm('Are you sure?\\nDo you want to cancel the trip?')");
                // $('#raise-btn').data('cancel-id', id);
                // $('#raise-btn').text('Cancel Trip');
                $(this).data('update_flag') ? $('#raise-btn').hide() : $('#raise-btn').show();
                $('#requestForm').attr('action', '/transport/update/' + id);
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
                $('#date').val('');
                $('#employee_name').val('');
                $('#booking_date').val('');
                $('#booking_time').val('');
                $('#employee_no').val('');
                $('#designation').val('');
                $('#department').val('');
                $('#destination').val('');
                $('#reason').val('');
                $('#request_type').val('');
                $('#contact_no').val('');
                $('#accept').val('');

                $('#raise-btn').show();
                $('#allot-btn').hide();
                $('#cancel-btn').hide();
                $('#allot-view').hide();
                $('#vehicle_id').attr('required', false);
                $('#driver_id').attr('required', false);
                $('#raise-btn').text('Submit');
                $('#requestForm').attr('action', '/transport/store');
                $('#requestForm').removeAttr('onsubmit');

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