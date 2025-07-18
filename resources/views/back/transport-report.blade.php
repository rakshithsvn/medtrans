    @extends('layouts.back.index')

    @section('content')
    <style>
        td {
            font-weight: 900;
        }

        p {
            margin-bottom: 0
        }

        .space {
            margin-top: 28px;
        }
    </style>

    <section class="section">
        <div class="card shadow-sm border-0 global-font">
            <div class="card-body p-4">
                <!-- Filter Form and Action Buttons -->
                <form action="{{ route('transport.report') }}" method="POST">
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

                    </div>
                </form>
            </div>
        </div>

        @if(in_array($user->register_by, ['ADMIN', 'SUPERVISOR']))
        <div class="card shadow-sm p-4">
            <div class="row mt-3 d-flex justify-content-center">
                <div class="col-md-12 d-flex justify-content-center mb-4">
                    <button class="btn btn-primary">
                        <h4 class="text-white mb-0">Total Trip : {{@$transportDetails->count()}}</h4>
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Types of Trip</th>
                            <th>Department</th>
                            <th>Count</th>
                            <th>Vehicle</th>
                            <th>Total KM</th>
                            <th>Date</th>
                            <th>Destination</th>
                        </tr>
                        @php $totalKm = 0 @endphp
                        @forelse ($transportGrouped as $transportType => $requestData)
                            @foreach(@$requestData as $index => $data)
                            @php $totalKm += @$data->km_covered @endphp
                            <tr>
                                @if ($index === 0)
                                <td rowspan="{{@$requestData->count()}}"> <h5>{{@$transportType}}</h5></td>
                                @endif
                                <td>{{@$data->department}}</td>
                                <td>1</td>                                
                                <td>{{@$data->vehicle_no}}</td>
                                <td>{{@$data->km_covered}}</td>
                                <td>{{ \Carbon\Carbon::parse(@$data->booking_date)->format('d/m/Y') }}</td>
                                <td>{{@$data->destination}}</td>
                            </tr>
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">No data found.</td>
                        </tr>
                        @endforelse

                        <!-- <tr>
                            <td>
                                <h5>Daily Trip</h5>
                            </td>
                            <td class="bg-success text-white"></td>
                            <td>10</td>
                            <td>BUS 1 : 6 <br> BUS 2 : 4</td>
                            <td>
                                <p><b>Total Km:</b> <span class="text-danger">100</span></p>
                                <p><b>Total Fuel Consumed:</b> <span class="text-danger">20</span></p>
                            </td>
                        </tr> -->

                    </table>

                </div>

                <br>
                <h5><b>Total Km:</b> <span class="text-danger">{{@$totalKm}}</span></h5>
                <!-- <h5><b>Total Fuel Consumed:</b><span class="text-danger">120</span></h5> -->
            </div>
        </div>
        @endif

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
                                @forelse(@$transportRequests->where('allot_type', '!=', '1') as $data)
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
                                        data-reason="{{ $data->reason }}">Allot Vehicle</a>
                                    @else
                                        <a
                                        href="#"
                                        class="btn btn-success view-btn" data-bs-toggle="modal" data-bs-target="#requestModal"
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

                                <th>Driver Name</th>
                                <th>Vehicle No</th>
                                <th>Assigned By</th>
                                <th>Destination</th>
                                <th>Booking Date</th>
                                <th>Booking Time</th>
                                <th></th>
                            </tr>

                            @forelse(@$transportAllocations as $data)
                            @php $status = App\Models\VehicleMovement::where('type', 'transport')->where('allocation_id', $data->id)->first(); @endphp
                            <tr>
                                <td>{{@$data->driver->name}}</td>
                                <td>{{@$data->vehicle->reg_no}}</td>
                                <td>{{@$data->supervisor->name ?? 'Admin'}}</td>
                                <td>{{$data->transportRequest->destination}}</td>
                                <td>{{ \Carbon\Carbon::parse(@$data->transportRequest->booking_date)->format('d/m/Y') }}</td>
                                <td>{{ $data->transportRequest->booking_time ? \Carbon\Carbon::createFromFormat('H:i:s', @$data->transportRequest->booking_time)->format('g:i A') : '-' }}</td>
                                <td class="text-center">
                                    <a
                                        href="#"
                                        class="btn btn-success view-btn" data-bs-toggle="modal" data-bs-target="#requestModal"
                                        data-id="{{ $data->id }}"
                                        data-type="{{ $data->type }}"
                                        data-status="{{ @$status->status }}"
                                        data-date="{{ $data->transportRequest->date }}"
                                        data-employee_name="{{ $data->transportRequest->employee_name }}"
                                        data-booking_date="{{ $data->transportRequest->booking_date }}"
                                        data-booking_time="{{ $data->transportRequest->booking_time }}"
                                        data-employee_no="{{ $data->transportRequest->employee_no }}"
                                        data-designation="{{ $data->transportRequest->designation }}"
                                        data-department="{{ $data->transportRequest->department }}"
                                        data-destination="{{ $data->transportRequest->destination }}"
                                        data-request_type="{{ $data->transportRequest->request_type }}"
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
                </div>
            </div>

            <div class="row mt-4">
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
                       
                        @forelse(@$cancelledMovements as $move)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($move->date)->format('d/m/Y') }}</td>                            
                            <td>{{@$move->vehicle_name}}</td>
                            <td>{{@$move->driver_name}}</td>
                            <td>{{@$move->department}}</td>
                            <td>{{@$move->place}}</td>
                            <td>{{@$move->purpose}}</td>
                            <td>{{@$move->cancel_reason}}</td>
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
                                <label>Employee Name</label>
                                <input type="text" class="form-control" name="employee_name" id="employee_name" required/>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <h5 class="text-white p-2 bg-secondary mt-3">
                                    Booking Details
                                </h5>
                            </div>

                            <div class="col-md-4">
                                <label>Date</label>
                                <input type="date" class="form-control" name="booking_date" id="booking_date" required/>
                            </div>
                            <div class="col-md-4">
                                <label>Time</label>
                                <input type="time" class="form-control" name="booking_time" id="booking_time" required/>
                            </div>

                            <div class="col-md-4">
                                <label>Employee No</label>
                                <input type="text" class="form-control" name="employee_no" id="employee_no" required/>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>Designation</label>
                                <input type="text" class="form-control" name="designation" id="designation" required/>
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
                                <input type="text" class="form-control" name="destination" id="destination" required/>
                            </div>
                        </div>
                        <div class="row mt-3">                       
                            <div class="col-md-6">
                                <label>Request Type</label>
                                <select
                                        class="form-select"
                                        aria-label="Default select example" name="request_type" id="request_type" required>
                                        <option value="">Select</option>
                                        @foreach($requests as $request)
                                        <option value="{{@$request}}">{{@$request}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="col-md-6">
                                <label>Description</label>
                                <input type="text" class="form-control" name="reason" id="reason"/>
                            </div>
                        </div>

                        <div class="row mt-3" id="allot-view">
                            <h5 class="title">Allot Vehicle</h5>
                            <div class="col-md-6">
                                <label>Vehicle</label>
                                <input type="text" class="form-control" name="" id="v_vehicle" required readOnly/>
                            </div>
                            <div class="col-md-6">
                                <label>Driver Name</label>
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
                                    <label>Driver Name</label>
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
                    <form id="allocateForm" action="{{ route('transport.allocate') }}" method="POST" enctype="multipart/form-data">
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
                                    <input type="text" class="form-control" name="supervisor_name" />
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
                                        id="contact_no"
                                        value="" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label>Km</label>
                                    <input type="number" class="form-control" name="km" />
                                </div>
                                <div class="col-md-3">
                                    <label>Total Cost</label>
                                    <input type="number" class="form-control" name="total_cost" />
                                </div>
                                <div class="col-md-6">
                                    <label>Place</label>
                                    <input type="text" class="form-control" name="place" />
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

                            <div class="row mt-3 d-flex justify-content-center">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success px-5" id="submit-btn">
                                        Submit
                                    </button>
                                    {{-- <button type="button" id="#" class="btn btn-danger d-none" data-bs-toggle="modal" data-bs-target="#allocateModal">
                                        Allocate Ambulance
                                    </button> --}}
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

    <script>
        $(document).ready(function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById("booking_date").min = today;
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
                $('#v_vehicle').val($(this).data('vehicle') || '');
                $('#v_driver').val($(this).data('driver') || '');
                $('#allot-view').show();

                $('#raise-btn').hide();
                $('#allot-btn').hide();
                $('#requestForm').attr('action', '/trip-cancel/' + id);
                $('#requestForm').attr('onsubmit', "return confirm('Are you sure?\\nDo you want to cancel the trip?')");
                $('#raise-btn').text('Cancel Trip');
                $('#modalTitle').text('Transport Requisition');                
                $('#vehicle_id').attr('required', false);
                $('#driver_id').attr('required', false);
                $('#booking_date').removeAttr('min');
               
                if($(this).data('request')=="request") {                    
                    $('#raise-btn').hide();
                    $('#allot-view').hide();
                }
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
                $('#accept').val('');

                $('#raise-btn').show();
                $('#allot-btn').hide();
                $('#allot-view').hide();
                $('#vehicle_id').attr('required', false);
                $('#driver_id').attr('required', false);
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