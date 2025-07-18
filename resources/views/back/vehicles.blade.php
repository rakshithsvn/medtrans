@extends('layouts.back.index')

@section('content')

<section class="section">
    @if(in_array($user->register_by, ['ADMIN', 'COORDINATOR']))
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <!-- Filter Form and Action Buttons -->
            <form action="{{ route('vehicles.index') }}" method="POST">
                @csrf
                <div class="row g-3 align-items-end">
                    <!-- Left Side: Dropdown, Search, and Download -->
                    <div class="col-12 col-md-8">
                    </div>
                    <!-- Right Side: New and Import Buttons -->
                    <div class="col-12 col-md-4">
                        <div class="row g-3 justify-content-end align-items-end">
                            <!-- New Button -->
                            <div class="col-12 col-md-4">
                                <button type="button" id="new-btn" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#vehicleModal">
                                    New
                                </button>
                            </div>

                            <!-- Import Button -->
                            <!-- <div class="col-12 col-md-4">
                                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#importModal">
                                    Import
                                    </button>
                                </div> -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center mb-4">
                <!-- Search Input -->
                <div class="col-12">
                    <input type="text" id="searchInput" class="form-control border-primary" placeholder="Search">
                </div>
                <!-- Vehicle Table -->
                <div class="table-responsive">
                    <table class="table table-borderless data-table">
                        <thead>
                            <tr style="color: white;">
                                <th class="ps-3 py-2">Register No</th>
                                <th class="ps-3 py-2">Model</th>
                                <th class="ps-3 py-2">Type</th>
                                <th class="ps-3 py-2">Driver</th>
                                <th class="ps-3 py-2">Fuel Type</th>
                                <th class="ps-3 py-2">Km In</th>
                                <th class="ps-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody id="vehicleTableBody">
                            @forelse($vehicles as $dataRow)
                            <tr class="border-bottom align-middle">
                                <td class="ps-3">{{ $dataRow->reg_no }}</td>
                                <td class="ps-3">{{ $dataRow->model }}</td>
                                <td class="ps-3">{{ $dataRow->type }}</td>
                                <td class="ps-3">{{ $dataRow->employee->name }}</td>
                                <td class="ps-3">{{ $dataRow->fuel_type }}</td>
                                <td class="ps-3">{{ $dataRow->km_in }}</td>
                                <td class="ps-3">
                                    <button type="button" class="btn btn-sm btn-primary d-flex justify-content-center view-btn"
                                        data-id="{{ $dataRow->id }}"
                                        data-model="{{ $dataRow->model }}"
                                        data-type="{{ $dataRow->type }}"
                                        data-reg_no="{{ $dataRow->reg_no }}"
                                        data-employee_id="{{ $dataRow->employee_id }}"
                                        data-fuel_type="{{ $dataRow->fuel_type }}"
                                        data-km_in="{{ $dataRow->km_in }}"
                                        data-bs-toggle="modal" data-bs-target="#vehicleModal">
                                        View
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No vehicles found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <nav aria-label="Page navigation" class="my-0">
                    <ul class="pagination justify-content-center flex-wrap">
                        {{ $vehicles->links() }}
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
                        <form action="{{ route('vehicles.import') }}" method="POST" enctype="multipart/form-data">
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
                            <i class="fas fa-user-md me-2"></i>{{ isset($vehicle) ? 'Vehicle' : 'Add Vehicle' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-3">
                        <form id="vehicleForm" action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="vehicleId" name="id">

                            <div class="card shadow-sm p-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Vehicle Registration No.</label>
                                        <input type="text" id="reg_no" name="reg_no" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Model</label>
                                        <input type="text" id="model" name="model" class="form-control" required>
                                    </div>
                                </div>

                                <!-- Reg No, Driver, Fuel -->
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label>Vehicle Type</label>
                                        <select id="type" name="type" class="form-select" required>
                                            <option value="">Select</option>
                                            <option value="Car">Car</option>
                                            <option value="Ambulance">Ambulance</option>
                                            <option value="Bus">Bus</option>
                                            <option value="2 Wheeler">2 Wheeler</option>
                                            <option value="Tanker">Tanker</option>
                                            <option value="Tempo Traveller">Tempo Traveller</option>
                                            <option value="Goods Tempo">Goods Tempo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Responsible Driver</label>
                                        <select class="form-select" aria-label="Default select example" name="employee_id" id="employee_id">
                                            <option value="">Select</option>
                                            @foreach(@$drivers as $driver=>$id)
                                            <option value="{{@$id}}">{{@$driver}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <label>Fuel Type</label>
                                        <select id="fuel_type" name="fuel_type" class="form-select" required>
                                            <option value="">Select</option>
                                            <option value="Petrol">Petrol</option>
                                            <option value="Diesel">Diesel</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <label>Km In</label>
                                        <input type="number" id="km_in" name="km_in" class="form-control" required>
                                    </div>
                                </div>

                                <!-- RC Fitness -->
                                <div class="row mt-4">
                                    <h5 class="title">RC Fitness Information</h5>
                                    <div class="col-md-6 mt-2">
                                        <label>Fitness Valid Till</label>
                                        <input type="date" id="fitness_expiry" name="fitness_expiry" class="form-control">
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label>Upload Fitness Certificate</label>
                                        <input type="file" id="fitness_file" name="fitness_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.svg" />
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6 col-sm-12">
                                        <a
                                            id="fitness_link"
                                            href="#"
                                            type="button"
                                            target="_blank"
                                            class="btn btn-success col-md-4 col-lg-3">
                                            View Doc </a><a
                                            href="#"
                                            type="button"
                                            class="btn btn-danger col-md-4 col-lg-3 ms-lg-3">
                                            Set Reminder
                                        </a>
                                    </div>
                                </div>

                                <!-- Insurance -->
                                <div class="row mt-4">
                                    <h5 class="title">Vehicle Insurance Policy</h5>
                                    <div class="col-md-6">
                                        <label>From Date</label>
                                        <input type="date" id="insurance_from" name="insurance_from" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Expiry Date</label>
                                        <input type="date" id="insurance_expiry" name="insurance_expiry" class="form-control">
                                    </div>

                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6 col-sm-12">
                                        <a
                                            id="insurance_link"
                                            href="#"
                                            type="button"
                                            target="_blank"
                                            class="btn btn-success col-md-4 col-lg-3">
                                            View Doc </a><a
                                            href="#"
                                            type="button"
                                            class="btn btn-danger col-md-4 col-lg-3 ms-lg-3">
                                            Set Reminder
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="file" id="insurance_file" name="insurance_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.svg" />
                                    </div>
                                </div>

                                <!-- Emission Certificate -->
                                <div class="row mt-4">
                                    <h5 class="title">Emission Test Certificate</h5>
                                    <div class="col-md-6">
                                        <label>From Date</label>
                                        <input type="date" id="emission_from" name="emission_from" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Expiry Date</label>
                                        <input type="date" id="emission_expiry" name="emission_expiry" class="form-control">
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6 col-sm-12">
                                        <a
                                            id="emission_link"
                                            href="#"
                                            type="button"
                                            target="_blank"
                                            class="btn btn-success col-md-4 col-lg-3">
                                            View Doc </a><a
                                            href="#"
                                            type="button"
                                            class="btn btn-danger col-md-4 col-lg-3 ms-lg-3">
                                            Set Reminder
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="file" id="emission_file" name="emission_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.svg" />
                                    </div>
                                </div>

                                <!-- Road Permit -->
                                <div class="row mt-4">
                                    <h5 class="title">Road Permit Details</h5>
                                    <div class="col-md-6">
                                        <label>From Date</label>
                                        <input type="date" id="permit_from" name="permit_from" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Expiry Date</label>
                                        <input type="date" id="permit_expiry" name="permit_expiry" class="form-control">
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6 col-sm-12">
                                        <a
                                            id="permit_link"
                                            href="#"
                                            type="button"
                                            target="_blank"
                                            class="btn btn-success col-md-4 col-lg-3">
                                            View Doc </a><a
                                            href="#"
                                            type="button"
                                            class="btn btn-danger col-md-4 col-lg-3 ms-lg-3">
                                            Set Reminder
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="file" id="permit_file" name="permit_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.svg" />
                                    </div>
                                </div>

                                <!-- Motor Vehicle Tax -->
                                <div class="row mt-4">
                                    <h5 class="title">Motor Vehicle Tax</h5>
                                    <div class="col-md-6">
                                        <label>From Date</label>
                                        <input type="date" id="tax_from" name="tax_from" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Expiry Date</label>
                                        <input type="date" id="tax_expiry" name="tax_expiry" class="form-control">
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6 col-sm-12">
                                        <a
                                            id="tax_link"
                                            href="#"
                                            type="button"
                                            target="_blank"
                                            class="btn btn-success col-md-4 col-lg-3">
                                            View Doc </a><a
                                            href="#"
                                            type="button"
                                            class="btn btn-danger col-md-4 col-lg-3 ms-lg-3">
                                            Set Reminder
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="file" id="tax_file" name="tax_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.svg" />
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex justify-content-center mt-4">
                                    <button type="submit" id="saveChanges" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Changes
                                    </button>
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
                                <td class="ps-3 pe-3">${vehicle.model}</td>
                                <td class="ps-3 pe-3">${vehicle.type}</td>
                                <td class="ps-3 pe-3">${vehicle.reg_no}</td>
                                <td class="ps-3 pe-3"></td>
                                <td class="ps-3 pe-3">${vehicle.fuel_type}</td>
                                <td class="ps-3 pe-3">${vehicle.km_in}</td>
                                <td class="ps-3 pe-3">
                                    <button type="button" class="btn btn-sm btn-primary d-flex justify-content-center view-btn" 
                                        data-id="${vehicle.id}" 
                                        data-model="${vehicle.model}"
                                        data-type="${vehicle.type}"
                                        data-reg_no="${vehicle.reg_no}"
                                        data-employee_id="${vehicle.employee_id}"
                                        data-fuel_type="${vehicle.fuel_type}"
                                        data-km_in="${vehicle.km_in}"
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
            $('#vehicleId').val(id);
            $('#model').val($(this).data('model'));
            $('#type').val($(this).data('type'));
            $('#reg_no').val($(this).data('reg_no'));
            $('#employee_id').val($(this).data('employee_id'));
            $('#fuel_type').val($(this).data('fuel_type'));
            $('#km_in').val($(this).data('km_in'));

            loadVehicleDocs(id);

            // Set form action for update
            $('#vehicleForm').attr('action', '/vehicles/' + id);
            $('#vehicleForm').append('<input type="hidden"  name="_method" value="POST">');
            $('#saveChanges').text('Update Changes');
            $('#modalTitle').text('Vehicle Information');

            // Disable inputs if not ADMIN/COORDINATOR
            @if(!in_array($user['register_by'], ['ADMIN', 'COORDINATOR']))
            $('#vehicleForm :input').prop('disabled', true);
            $('#footer-btn').hide();
            @else
            $('#vehicleForm :input').prop('disabled', false);
            $('#footer-btn').show();
            @endif
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
            //     '{{ route('vehicles.store') }}');
            $('#vehicleForm input[name="_method"]').remove(); // remove hidden method input
            $('#saveChanges').text('Save Changes');
            $('#modalTitle').text('Add Vehicle');
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