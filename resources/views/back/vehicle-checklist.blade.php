@extends('layouts.back.index')

@section('content')

<section class="section">
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <!-- Filter Form and Action Buttons -->
            <form action="{{ route('vehicle-checklist.index') }}" method="POST">
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
                        <select name="vehicle_id" class="form-select border-primary">
                            <option value="">All</option>
                            @foreach(@$vehicles as $vehicle)
                            <option value="{{@$vehicle->id}}" data-driver_id="{{@$vehicle->employee_id}}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>{{@$vehicle->reg_no}}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- <div class="col-md-6">
                        <label class="form-label text-secondary large-label">Driver Name</label>
                        <select name="driver_id" class="form-select border-primary">
                            <option value="">All</option>
                            @foreach($drivers as $name => $id)
                            <option value="{{ $id }}" {{ request('driver_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
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
                    @if(in_array($user->register_by, ['DRIVER']))
                    <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                        <button type="button" id="new-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checklistModal">
                            New
                        </button>
                        <!-- 
                         -->
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">

            <div class="row g-3">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Checklist Report</h5>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Date</th>
                                <th>Driver Name</th>
                                <th>Vehicle</th>
                                <th></th>
                            </tr>

                            @forelse($vehicleChecklist as $dataRow)
                            <tr class="border-bottom align-middle">
                                <td class="ps-3">{{ \Carbon\Carbon::parse($dataRow->date)->format('d/m/Y') }}</td>
                                <td class="ps-3">{{ $dataRow->driver_name }}</td>
                                <td class="ps-3">{{ $dataRow->vehicle_name }}</td>
                                <td class="ps-3">
                                    <button type="button" class="btn btn-sm btn-primary d-flex justify-content-center view-btn"
                                        data-id="{{ $dataRow->id }}"
                                        data-approve="{{ $dataRow->approve }}"
                                        data-driver_id="{{ $dataRow->driver_id }}"
                                        data-vehicle_id="{{ $dataRow->vehicle_id }}"
                                        data-date="{{ $dataRow->date }}"
                                        data-inspections="{{ @$dataRow->inspections }}"
                                        data-description="{{ @$dataRow->description }}"
                                        data-bs-toggle="modal" data-bs-target="#checklistModal">
                                        View
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No data found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation" class="my-0">
                        {{ @$vehicleChecklist->links() }}
                    </nav>
                </div>
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
                        <form action="{{ route('vehicle-checklist.import') }}" method="POST" enctype="multipart/form-data">
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
        <div class="modal fade" id="checklistModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="checklistModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content p-0">
                    <!-- Modal Header -->
                    <div class="modal-header py-3" style="background: #164966;">
                        <h5 class="modal-title text-white mb-0" id="modalTitle">
                            <i class="fas fa-user-md me-2"></i>{{ isset($vehicle) ? 'Update Vehicle Checklist' : 'Vehicle Checklist' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-3">
                        <form id="vehicleForm" action="{{ route('vehicle-checklist.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="checklistId" name="id">
                            <input type="hidden" id="approve" name="approve">

                            <div class="card shadow-sm p-4">
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

                                <div class="row mt-4">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input inspection-checkbox" type="checkbox" name="inspections[]" value="exterior_wash" id="check_exterior_wash" />
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Exterior Wash
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input inspection-checkbox" type="checkbox" name="inspections[]" value="interior_wash" id="check_interior_wash">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Interior Wash
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input inspection-checkbox" type="checkbox" name="inspections[]" value="water_level" id="check_water_level">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Water Level - Radiator
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input inspection-checkbox" type="checkbox" name="inspections[]" value="oil_level" id="check_oil_level">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Oil Level
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input inspection-checkbox" type="checkbox" name="inspections[]" value="air_check" id="check_air_check">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Air Check
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input inspection-checkbox" type="checkbox" name="inspections[]" value="stepney_check" id="check_stepney_check">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Stepney Check
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input inspection-checkbox" type="checkbox" name="inspections[]" value="head_light" id="check_head_light" />
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Head Light
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input inspection-checkbox" type="checkbox" name="inspections[]" value="break_light" id="check_break_light">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Break Light
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input inspection-checkbox" type="checkbox" name="inspections[]" value="indicator" id="check_indicator">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Indicator
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input inspection-checkbox" type="checkbox" name="inspections[]" value="reverse_light" id="check_reverse_light">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Reverse Light
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input inspection-checkbox" type="checkbox" name="inspections[]" value="viper" id="check_viper">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Viper
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input inspection-checkbox" type="checkbox" name="inspections[]" value="horn" id="check_horn">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Horn
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input inspection-checkbox" type="checkbox" name="inspections[]" value="siren" id="check_siren">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Siren
                                            </label>

                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <label>
                                            Repair and Management Details (If Any)
                                        </label>
                                        <input type="text" class="form-control" id="description" name="description" />
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
                                        data-bs-target="#checklistModal">
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
            const inspections = $(this).data('inspections');

            // Fill form fields dynamically
            $('#checklistId').val(id);
            $('#date').val($(this).data('date'));
            $('#approve').val($(this).data('approve'));
            $('#driver_id').val($(this).data('driver_id'));
            $('#vehicle_id').val($(this).data('vehicle_id'));
            $('#description').val($(this).data('description'));

            $('.inspection-checkbox').prop('checked', false);

            // Re-check based on the saved inspections
            if (Array.isArray(inspections)) {
                inspections.forEach(function(val) {
                    $('#check_' + val).prop('checked', true);
                });
            }

            // loadVehicleDocs(id);

            // Set form action for update
            $('#vehicleForm').attr('action', '/vehicle-checklist/' + id);
            $('#vehicleForm').append('<input type="hidden"  name="_method" value="POST">');
            $('#saveChanges').show();
            $('#modalTitle').text('Vehicle Checklist');

            @if(in_array($user['register_by'], ['ADMIN', 'SUPERVISOR']))
            // $('#vehicleForm :input').prop('disabled', true);
            $('#approve').val(1);
            $('#saveChanges').text('Approve');
            @else
            // $('#vehicleForm :input').prop('disabled', false);
            $('#saveChanges').text('Submit');
            @endif

            $(this).data('approve') ? $('#saveChanges').hide() : $('#saveChanges').show();
        });

        $('#checklistModal').on('hidden.bs.modal', function() {
            $('#vehicleForm').trigger('reset');
            $('#vehicleForm').attr('action', '/vehicle-checklist/store');
            $('#vehicleForm input[name="_method"]').remove();
            $('#saveChanges').text('Submit');
            $('#modalTitle').text('Vehicle Checklist');
            $('#vehicleForm :input').prop('disabled', false);
            $('#saveChanges').show();
        });

        $("#type").change(function() {
            var optionValue = $(this).val();
            $("#type_name").text(optionValue + " Name");
            $("#type_address").text(optionValue + " Address");
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