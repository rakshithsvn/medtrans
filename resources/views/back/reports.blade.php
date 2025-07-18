@extends('layouts.back.index')

<style>
    h5.title {
        text-decoration: underline;
        color: #ac7070;

    }

    .box h5 {
        font-size: 20px;
        color: #2582a2
    }

    .box h4 {
        font-size: 25px
    }

    .metric-unit {
        font-size: 14px;
        color: #7f8c8d;
        margin-left: 5px;
    }

    .box.shadow {
        text-align: center;
        padding: 20px;
        border-radius: 10px;
    }
</style>

@section('content')

<section class="section">
    <!-- <div class="card shadow-sm p-4" style="border-top-right-radius: 0;border-top-left-radius: 0;">
		<form action="{{ route('dashboard') }}" method="POST">
            @csrf
            <div class="row g-4 align-items-end">
                <div class="col-md-3">
                    <label>Date</label>
                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{@$request->from_date}}" />
                </div>
                
                <div class="col-md-3">
                    <label>Vehicle</label>
                    <select id="vehicleSelect" name="vehicle_id" class="form-select">
                        <option value="">All</option>
                        @foreach(@$vehicles as $vehicle)
                        <option
                            value="{{@$vehicle->id}}"
                            data-driver_id="{{@$vehicle->employee_id}}"
                            {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                            {{@$vehicle->reg_no}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Driver</label>
                    <select id="driverSelect" name="driver_id" class="form-select">
                        <option value="">All</option>
                        @foreach(@$drivers as $driver)
                        <option value="{{@$driver->id}}" data-driver_id="{{@$driver->employee_id}}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>{{@$driver->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success">Search</button>
                </div>
            </div>
        </form>

        <div class="row mt-4">
            <div class="col-md-12 mb-3">
                <h5 class="text-white p-2 bg-secondary">Vehicle Wise Report</h5>
            </div>
            <div class="col-md-12">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-3 mb-3">
                        <div class="box shadow" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                            <h5 class="text-secondary">Total Trips</h5>
                            <p id="totalTrips" class="h3 fw-bold" style="color: #0082A3;">{{ $vehicleSummary['totalTrips'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="box shadow" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                            <h5 class="text-secondary">Total Kms</h5>
                            <p id="totalKms" class="h3 fw-bold" style="color: #0082A3;">{{ number_format($vehicleSummary['totalKms']) }} <span class="metric-unit">km</span></p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="box shadow" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                            <h5 class="text-secondary">Total Hours</h5>
                            <p id="totalHours" class="h3 fw-bold" style="color: #0082A3;">{{ $vehicleSummary['totalHours'] }} <span class="metric-unit">Hours</span></p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="box shadow" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                            <h5 class="text-secondary">Cancelled Trips</h5>
                            <p id="totalCancel" class="h3 fw-bold" style="color: #0082A3;">{{ @$vehicleSummary['totalCancel'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
  
    <div class="card-header py-3" style="background: #164966;">
        <h5 class="mb-0 text-white"><i class="fas fa-file-alt me-2"></i>Reports</h5>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="row g-4 d-flex justify-content-center">
            
            <div class="col-md-3 d-flex justify-content-center pt-4">
                <div class="card cust-card border-0 shadow-sm h-70" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <a href="{{ route('reports.ambulance') }}">
                                <i class="fas fa-ambulance" style="color: #0082A3; font-size: 25px"></i>
                            </a>
                        </div>
                        <a href="{{ route('reports.ambulance') }}" class="text-decoration-none">
                            <h5 class="text-secondary mb-3">Ambulance Report</h5>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 d-flex justify-content-center pt-4">
                <div class="card cust-card border-0 shadow-sm h-70" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <a href="{{ route('reports.vehicle') }}">
                                <i class="fas fa-car" style="color: #0082A3; font-size: 25px"></i>
                            </a>
                        </div>
                        <a href="{{ route('reports.vehicle') }}" class="text-decoration-none">
                            <h5 class="text-secondary mb-3">Vehicle Report</h5>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 d-flex justify-content-center pt-4">
                <div class="card cust-card border-0 shadow-sm h-70" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <a href="{{ route('reports.driver') }}">
                                <i class="fas fa-user" style="color: #0082A3; font-size: 25px"></i>
                            </a>
                        </div>
                        <a href="{{ route('reports.driver') }}" class="text-decoration-none">
                            <h5 class="text-secondary mb-3">Driver Report</h5>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 d-flex justify-content-center pt-4">
                <div class="card cust-card border-0 shadow-sm h-70" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <a href="{{ route('reports.department') }}">
                                <i class="fas fa-user" style="color: #0082A3; font-size: 25px"></i>
                            </a>
                        </div>
                        <a href="{{ route('reports.department') }}" class="text-decoration-none">
                            <h5 class="text-secondary mb-3">Department Report</h5>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@section('script')
@endsection