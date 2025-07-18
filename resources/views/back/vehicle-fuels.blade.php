@extends('layouts.back.index')

@section('content')

<section class="section">
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <!-- Filter Form and Action Buttons -->
            <form action="{{ route('vehicle-fuels.index') }}" method="POST">
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
                            <option value="{{@$vehicle->id}}" data-driver_id="{{@$vehicle->employee_id}}" data-km_in="{{@$vehicle->km_in}}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>{{@$vehicle->reg_no}}</option>
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
                        <button type="submit" id="export" name="submit" value="export" class="btn btn-primary">
                            Download
                        </button>
                    </div>

                    <!-- Right Side Buttons (Conditional) -->
                    @if(in_array($user->register_by, ['ADMIN', 'SUPERVISOR', 'DRIVER']))
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

            <div class="row g-3">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Vehicle Wise Report</h5>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr align="center">
                                <th>Vehicle</th>
                                <th>Date</th>
                                <th>Total Km</th>
                                <th>Total Fuel Consumption / Ltr</th>
                                <th>Average @ Ltr (Mileage)</th>
                                <th>Driver</th>
                            </tr>

                            @foreach ($groupedFuels as $vehicleName => $movements)
                            @php
                            $totalKm = 0;
                            $totalFuels = 0;
                            $overall = 0;
                            @endphp

                            @foreach ($movements as $movement)
                            @php
                            $km = $movement->km_out - $movement->km_in;
                            $totalKm += $km;

                            $fuel = $movement->fuel_qty;
                            $totalFuels += $fuel;

                            $mileage = $movement->mileage;

                            @endphp

                            <tr align="center">
                                @if ($loop->first)
                                <td rowspan="{{ $movements->count() }}"><strong>{{ $vehicleName }}</strong></td>
                                @endif
                                <td class="ps-3">{{ \Carbon\Carbon::parse($movement->date)->format('d/m/Y') }}</td>
                                <td class="ps-3">{{ $km }}</td>
                                <td class="ps-3">{{ $movement->fuel_qty }}</td>
                                <td class="ps-3">{{ $movement->mileage }}</td>
                                <td class="ps-3">{{ $movement->driver_name }}</a>
                                </td>
                            </tr>
                            @endforeach
                            @php
                            $overallMileage = $totalFuels > 0 ? round($totalKm / $totalFuels, 2) : 0;
                            @endphp

                            <tr align="center" style="background-color: #eef; color: #dc3545; font-weight: bold;">
                                <td></td>
                                <td></td>
                                <td>{{ $totalKm }}</td>
                                <td>{{ $totalFuels }}</td>
                                <td>{{ $overallMileage }}</td>
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
                    <input type="text" id="searchInput" class="form-control border-primary" fuel_qtyholder="Search">
                </div> -->
                <!-- Vehicle Table -->
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr align="center">
                                <th rowspan="2">Date</th>
                                <th rowspan="2">Vehicle No</th>
                                <th colspan="2">Meter Reading in KMs</th>
                                <th rowspan="2">Total KMs</th>
                                <th rowspan="2">Diesel in Qty</th>
                                <th rowspan="2"></th>
                            </tr>
                            <tr align="center">
                                <th>Opening</th>
                                <th>Closing</th>
                            </tr>

                            @forelse($vehicleFuels as $dataRow)
                            <tr align="center" class="border-bottom align-middle">
                                <td class="ps-3">{{ \Carbon\Carbon::parse($dataRow->date)->format('d/m/Y') }}</td>
                                <td class="ps-3">{{ $dataRow->vehicle_name }}</td>
                                <td class="ps-3">{{ $dataRow->km_in }}</td>
                                <td class="ps-3">{{ $dataRow->km_out }}</td>
                                <td class="ps-3">{{ $dataRow->km_out - $dataRow->km_in }}</td>
                                <td class="ps-3">{{ $dataRow->fuel_qty }}</td>
                                <td class="ps-3">
                                    <button type="button" class="btn btn-sm btn-primary d-flex justify-content-center view-btn"
                                        data-id="{{ $dataRow->id }}"
                                        data-driver_id="{{ $dataRow->driver_id }}"
                                        data-vehicle_id="{{ $dataRow->vehicle_id }}"
                                        data-date="{{ $dataRow->date }}"
                                        data-km_in="{{ $dataRow->km_in }}"
                                        data-km_out="{{ $dataRow->km_out }}"
                                        data-km_covered="{{ $dataRow->km_covered }}"
                                        data-fuel_qty="{{ $dataRow->fuel_qty }}"
                                        data-mileage="{{ $dataRow->mileage }}"
                                        data-bs-toggle="modal" data-bs-target="#vehicleModal">
                                        View
                                    </button>
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
                <!-- Pagination -->
                <nav aria-label="Page navigation" class="my-0">
                    <ul class="pagination justify-content-center flex-wrap">
                        {{ $vehicleFuels->links() }}
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
                        <form action="{{ route('vehicle-fuels.import') }}" method="POST" enctype="multipart/form-data">
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
                            <i class="fas fa-user-md me-2"></i>Fuel Consumption
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-3">
                        <form id="vehicleForm" action="{{ route('vehicle-fuels.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="vehicleFuelId" name="id">

                            <div class="card shadow-sm p-4">
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label>Date</label>
                                        <input type="date" id="date" name="date" class="form-control" value="{{ now()->toDateString() }}" readOnly required />
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

                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label>Petrol / Diesel Qty In Ltr</label>
                                        <input type="number" id="fuel_qty" name="fuel_qty" class="form-control" required />
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <h5>Meter Reading In KMs</h5>
                                    <div class="col-md-6">
                                        <label>Opening</label>
                                        <input type="number" id="km_in" name="km_in" class="form-control" value="0" readOnly />
                                    </div>
                                    <div class="col-md-6">
                                        <label>Closing</label>
                                        <input type="number" id="km_out" name="km_out" class="form-control" required />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label>Total KMs</label>
                                        <input type="number" id="km_covered" name="km_covered" class="form-control" readOnly />
                                    </div>
                                    <div class="col-md-6">
                                        <label>Average @ Km/Ltr (Mileage)</label>
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
            $('#vehicleFuelId').val(id);
            $('#date').val($(this).data('date'));
            $('#driver_id').val($(this).data('driver_id'));
            $('#vehicle_id').val($(this).data('vehicle_id'));
            $('#km_out').val($(this).data('km_out'));
            $('#km_in').val($(this).data('km_in'));
            $('#km_covered').val($(this).data('km_covered'));
            $('#fuel_qty').val($(this).data('fuel_qty'));
            $('#mileage').val($(this).data('mileage'));
            $('#fuel_fill').val($(this).data('fuel_fill'));
            $('#department').val($(this).data('department'));

            // loadVehicleDocs(id);

            // Set form action for update
            $('#vehicleForm').attr('action', '/vehicle-fuels/' + id);
            $('#vehicleForm').append('<input type="hidden"  name="_method" value="POST">');
            $('#saveChanges').hide();
            $('#modalTitle').text('Fuel Consumption');

            // Disable inputs if not ADMIN/COORDINATOR
            @if(!in_array($user['register_by'], ['ADMIN', 'COORDINATOR']))
            $('#vehicleForm :input').prop('disabled', true);
            $('#footer-btn').hide();
            @else
            $('#vehicleForm :input').prop('disabled', false);
            $('#footer-btn').show();
            @endif
        });

        function loadVehicleFuelData(vehicleId) {
            fetch(`/vehicle-fuel-data/${vehicleId}`)
                .then(response => response.json())
                .then(data => {
                    $('#km_in').val(data.km ?? 0);
                })
                .catch(error => {
                    console.error('There was a problem fetching the vehicle docs:', error);
                    alert('Failed to load vehicle document data.');
                });
        }

        function calculateMileage() {
            let kmIn = parseInt($('#km_in').val(), 10) || 0;
            let kmOut = parseInt($('#km_out').val(), 10) || 0;
            let fuelUsed = parseFloat($('#fuel_qty').val()) || 0;

            let distance = kmOut - kmIn;
            if (distance < 0) distance = 0;

            $('#km_covered').val(distance);

            let mileage = fuelUsed > 0 ? (distance / fuelUsed).toFixed(2) : '';
            $('#mileage').val(mileage);
        }

        $('#vehicle_id').on('change', function() {
            let id = $(this).val();
            loadVehicleFuelData(id);
        });

        $('#km_in, #km_out, #fuel_qty').on('input', calculateMileage);

        $('#vehicleModal').on('hidden.bs.modal', function() {
            $('#vehicleForm').trigger('reset');
            // $('#vehicleForm').attr('action',
            //     '{{ route('vehicle-fuels.store') }}');
            $('#vehicleForm input[name="_method"]').remove(); // remove hidden method input
            $('#saveChanges').text('Submit');
            $('#modalTitle').text('Fuel Consumption');
            $('#vehicleForm :input').prop('disabled', false);
            $('#footer-btn').show();
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