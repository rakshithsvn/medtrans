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

<style>
    .cust-card {
        height: 500px;
        overflow-y: scroll;
        display: flex;
        flex-direction: column;
        transition: none !important;
    }

    .cust-card:hover {
        transform: none !important;
    }

    .cust-card::-webkit-scrollbar {
        width: 3px;
        height: 5px;
    }

    .cust-card::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .cust-card::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .chart-container canvas {
        max-height: 300px;
        min-height: 300px;
    }

    .compact-legend .chart-legend {
        max-height: 120px;
        overflow-y: auto;
    }

    /* Chart container styling */
    .chart-container {
        flex-grow: 1;
        width: 100%;
        position: relative;
    }

    /* Ensure charts are visible */
    canvas {
        display: block;
        background-color: transparent !important;
    }

    /* Legend styling for both display and download */
    .chart-legend {
        border: 1px solid #eee;
        border-radius: 4px;
        padding: 10px;
        margin-top: 15px;
        font-size: 12px;
        max-height: none;
        overflow: visible;
    }

    .legend-color-box {
        width: 15px;
        height: 15px;
        margin-right: 8px;
        display: inline-block;
        vertical-align: middle;
        border: 1px solid #333;
    }

    .chart-legend div {
        margin-bottom: 5px;
        white-space: normal;
        line-height: 1.4;
    }

    /* Color boxes for legend */
    .legend-color-box {
        width: 15px;
        height: 15px;
        margin-right: 8px;
        border: 1px solid #333;
        display: inline-block;
    }

    .chart-legend {
        max-height: 150px;
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 4px;
        margin-top: 10px;
        font-size: 12px;
        width: 100%;
        overflow: visible;
    }

    .legend-color-box {
        width: 15px;
        height: 15px;
        margin-right: 8px;
        border: 1px solid #333;
        display: inline-block;
        flex-shrink: 0;
    }
</style>

<style>
    h5.title {
        text-decoration: underline;
        color: #ac7070;

    }

    .riding {
        background-color: #b5f2b5;
    }

    .not-riding {
        background-color: #ff9ca6;
    }

    .complete-riding {
        background-color: #32cd32;
    }

    .progress-container {
        position: relative;
        width: 300px;
        height: 26px;
        border-radius: 10px;
        overflow: hidden;
        margin: 0 auto;
    }

    .arrow-track {
        position: absolute;
        height: 50px;
        width: 0%;
        top: -17px;
        left: 0;
        white-space: nowrap;
        overflow: hidden;
        font-size: 55px;
        /* Increased font size */
        color: limegreen;
        animation: growArrow 5s linear infinite;
        display: flex;
        align-items: center;
        padding-left: 5px;
        font-weight: bold;
    }

    /* Arrow symbols */
    .arrow-track::after {
        content: "»»»»»»»»»»»»»»»»»»»»»»";
        letter-spacing: 2px;
        /* Reduced spacing */
    }

    .car-icon {
        position: absolute;
        top: -6px;
        left: 0;
        font-size: 30px;
        /* Slightly bigger car */
        animation: moveCar 5s linear infinite;
        background: transparent;
        padding: 2px;
        border-radius: 50%;
    }

    /* Progress Bar animation (for arrows) */
    @keyframes growArrow {
        0% {
            width: 0%;
        }

        100% {
            width: 100%;
        }
    }

    /* Car movement animation */
    @keyframes moveCar {
        0% {
            left: 0%;
        }

        100% {
            left: 100%;
            transform: translateX(-100%);
        }
    }

    /* Static (Not riding) */
    .not-riding .arrow-track,
    .not-riding .car-icon, .complete-riding .arrow-track,
    .complete-riding .car-icon {
        animation: none;
        width: 0;
        left: 0;
    }

    .flipped {
        transform: scaleX(-1);
        /* Flip horizontally */
    }
</style>

@section('content')
<section class="section">
	<div class="card-header py-3" style="background: #164966;">
        <h5 class="mb-0 text-white"><i class="fas fa-file-alt me-2"></i>Daily Report</h5>
    </div>
    <div class="card shadow-sm p-4" style="border-top-right-radius: 0;border-top-left-radius: 0;">
		<form action="{{ route('dashboard') }}" method="POST">
        @csrf
        <div class="row g-4 align-items-end">
            <div class="col-md-3">
                <label>Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ @$request->from_date ?? now()->format('Y-m-d') }}" />
            </div>
            <!-- <div class="col-md-3">
				<div class="form-group mb-0">
                <label>Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control" />
            </div> -->
        <!-- </div> -->
			<!-- <div class="col-md-3">
				<div class="form-group mb-0">
                <label>Select</label>
                <select id="typeSelect" name="type" class="form-select">
                    <option value="">All</option>
					<option value="vehicle">Vehicle</option>             
                    <option value="driver">Driver</option>
                </select></div>
            </div> -->
			
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
            <!-- <div class="col-md-12 text-center d-flex justify-content-center">
            <div class="col-md-4 d-flex justify-content-center mb-4">
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
            </div> -->
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
                            <p id="totalKms" class="h3 fw-bold" style="color: #0082A3;">{{ number_format($vehicleSummary['totalKms']) }} <span class="metric-unit">Kms</span></p>
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
                    <!-- <div class="col-md-3 mb-3">
                        <div class="box shadow" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                            <h5 class="text-secondary">Fuel Consumption</h5>
                            <p id="totalFuel" class="h3 fw-bold" style="color: #0082A3;">{{ @$vehicleSummary['totalFuel'] ?? 0 }}</p>
                        </div>
                    </div> -->
                    
                    <!--<div class="col-md-4 mb-3">
                        <div class="box shadow">
                            <h5 class="text-secondary">Total Service Cost</h5>
                            <p id="totalService" class="h3 fw-bold" style="color: #0082A3;">{{ @$vehicleSummary['totalService'] ?? 0 }} <span class="metric-unit">INR</span></p>
                        </div>
                    </div>-->
                </div>
            </div>
           <!-- <div class="col-md-6  mt-5">
               
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Km Wise Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="kmWise" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="kmWise" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="kmWise">Download</button>
                        </div>
                    </div>
					<div class="d-flex justify-content-between align-items-center">
                    <div class="chart-container" id="kmWiseGraph">
                        <canvas id="kmWiseChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="kmWiseLegend"></div>
                    <div class="table-container d-none mt-2" id="kmWiseTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Vehicle No</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Kms</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
					</div>
                </div>
            </div>

            <div class="col-md-6  mt-5">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Hour Wise Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="travelWise" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="travelWise" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="travelWise">Download</button>
                        </div>
                    </div>
                    <div class="chart-container" id="travelWiseGraph">
                        <canvas id="travelWiseChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="travelWiseLegend"></div>
                    <div class="table-container d-none mt-2" id="travelWiseTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Vehicle No</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Hours</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6  mt-5">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Trip Wise Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="tripWise" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="tripWise" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="tripWise">Download</button>
                        </div>
                    </div>
                    <div class="chart-container" id="tripWiseGraph">
                        <canvas id="tripWiseChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="tripWiseLegend"></div>
                    <div class="table-container d-none mt-2" id="tripWiseTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Vehicle No</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Trip</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6  mt-5">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Fuel Consumption Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="fuelWise" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="fuelWise" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="fuelWise">Download</button>
                        </div>
                    </div>
                    <div class="chart-container" id="fuelWiseGraph">
                        <canvas id="fuelWiseChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="fuelWiseLegend"></div>
                    <div class="table-container d-none mt-2" id="fuelWiseTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Vehicle No</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Fuel</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6  mt-5">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Job Card Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="jobWise" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="jobWise" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="jobWise">Download</button>
                        </div>
                    </div>
                    <div class="chart-container" id="jobWiseGraph">
                        <canvas id="jobWiseChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="jobWiseLegend"></div>
                    <div class="table-container d-none mt-2" id="jobWiseTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Vehicle No</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Bill Amount</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 mb-3">
                <h5 class="text-white p-2 bg-secondary">Driver Wise Report</h5>
            </div>

            <div class="row mt-3">
                 <div class="col-md-3 mb-3">
                <label>From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control" />
            </div>
            <div class="col-md-3 mb-3">
                <label>To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control" />
            </div>
                <div class="col-md-4 mb-4">
                    <label>Driver</label>
                    <select id="driverSelect" name="driver_id" class="form-select">
                        <option value="">All</option>
                        @foreach(@$drivers as $driver)
                        <option value="{{@$driver->id}}" data-driver_id="{{@$driver->employee_id}}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>{{@$driver->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mt-4">
                    <button id="driverSearch" class="btn btn-success">Search</button>
                </div>

            </div>
            <div class="col-md-12 text-center d-flex justify-content-center">

            </div>
            <div class="col-md-12">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-4">
                        <div class="box shadow">
                            <h5>Total Trips</h5>
                            <p id="totalTripsD">{{ $vehicleSummary['totalTrips'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box shadow">
                            <h5>Total Kms</h5>
                            <p id="totalKmsD">{{ number_format($vehicleSummary['totalKms']) }} <span class="metric-unit">km</span></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box shadow">
                            <h5>Total Hours</h5>
                            <p id="totalHoursD">{{ $vehicleSummary['totalHours'] }} <span class="metric-unit">Hours</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6  mt-5">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Trip Wise Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="driverTripWise" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="driverTripWise" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="driverTripWise">Download</button>
                        </div>
                    </div>
                    <div class="chart-container" id="driverTripWiseGraph">
                        <canvas id="driverTripWiseChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="driverTripWiseLegend"></div>
                    <div class="table-container d-none mt-2" id="driverTripWiseTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Vehicle No</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Trip</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6  mt-5">
                
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Km Wise Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="driverKmWise" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="driverKmWise" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="driverKmWise">Download</button>
                        </div>
                    </div>
                    <div class="chart-container" id="driverKmWiseGraph">
                        <canvas id="driverKmWiseChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="driverKmWiseLegend"></div>
                    <div class="table-container d-none mt-2" id="driverKmWiseTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Driver Name</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Kms</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6  mt-5">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Hour Wise Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="driverTravelWise" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="driverTravelWise" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="driverTravelWise">Download</button>
                        </div>
                    </div>
                    <div class="chart-container" id="driverTravelWiseGraph">
                        <canvas id="driverTravelWiseChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="driverTravelWiseLegend"></div>
                    <div class="table-container d-none mt-2" id="driverTravelWiseTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Driver Name</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Hours</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    
    <div class="card shadow-sm p-4">
        <div class="row">
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr align="center">
                            <th>Trip Status</th>
                            <th>On Road Vehicle</th>
                            <th>On Trip Driver</th>
                        </tr>
                    </thead>
                    <tbody>
						@php
                        use Carbon\Carbon;
                        $movements = App\Models\VehicleMovement::query()
                            ->leftJoin('vehicles', 'vehicle_movements.vehicle_id', '=', 'vehicles.id')
                            ->leftJoin('employees', 'vehicle_movements.driver_id', '=', 'employees.id')
                            ->select(
                                'vehicle_movements.*',
                                'vehicles.reg_no as vehicle_name',
                                'employees.name as driver_name'
                            )
                            ->whereDate('vehicle_movements.date', Carbon::today())
                            ->whereNull('vehicle_movements.deleted_at')
                            ->get();
                        @endphp
                        @forelse(@$movements as $data)
                        @if(@$data->status == 'start')
                        <tr>
                            <td>
                                <div class="progress-container riding">
                                    <div class="arrow-track"></div>
                                    <div class="car-icon"><img src="assets/images/ambulance.svg" style="height:30px"></div>
                                </div>
                            </td>
                            <td align="center">{{@$data->vehicle_name}}</td>
                            <td align="center">{{@$data->driver_name}}</td>
                        </tr>
                        @endif
                        @if(@$data->status == 'allot')
                        <tr>
                            <td>
                                <div class="progress-container not-riding">
                                    <div class="arrow-track"></div>
                                    <div class="car-icon"><img src="assets/images/ambulance.svg" style="height:30px"></div>
                                </div>
                            </td>
                            <td align="center">{{@$data->vehicle_name}}</td>
                            <td align="center">{{@$data->driver_name}}</td>
                        </tr>
                        @endif

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
            <div class="col-md-12 mb-3">
                <h5 class="text-white p-2 bg-secondary">Driver Wise Trip Report</h5>
            </div>
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="text-dark mb-2" style="font-size: 1rem;"> </h5>
                    <div class="btn-group">
                        <button class="btn btn-xs btn-primary toggle-btn active" data-target="tripWiseDriverBar" name="graph">Graph</button>
                        <button class="btn btn-xs btn-secondary toggle-btn" data-target="tripWiseDriverBar" name="table">Table</button>
                        <button class="btn btn-xs btn-success download-btn" data-target="tripWiseDriverBar">Download</button>
                    </div>
                </div>
                <div class="chart-container" id="tripWiseDriverBarGraph">
                    <canvas id="tripWiseDriverBarChart"></canvas>
                </div>
                <div class="chart-legend mt-2" id="tripWiseDriverBarLegend"></div>
                <div class="table-container d-none mt-2" id="tripWiseDriverBarTable">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless report-table">
                            <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                <tr>
                                    <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Driver Name</th>
                                    <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Trip</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.75rem;"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-4">
                <div class="table-responsive scroll">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Name</th>
                            <th>Total Trips</th>
                            <th>Place</th>
                            <th>Total Hrs</th>
                        </tr>

                        @if(@$$driverTripWiseReport)
                        @forelse($driverTripWiseReport as $name => $count)
                        @php
                        $driver = DB::table('employees')->where('name', $name)->first();
                        $data = $driver ? App\Models\VehicleMovement::where('driver_id', $driver->id)->whereNull('vehicle_movements.deleted_at')->where('status', 'end')->get() : collect();

                        $totalHours = 0;
                        foreach ($data as $row) {
                            if ($row->time_in && $row->time_out) {
                                $start = \Carbon\Carbon::createFromFormat('H:i', substr($row->time_out, 0, 5));
                                $end = \Carbon\Carbon::createFromFormat('H:i', substr($row->time_in, 0, 5));
                                
                                $diffInMinutes = $end->diffInMinutes($start);
                                $totalHours += $diffInMinutes;

                                $duration = \Carbon\CarbonInterval::minutes($totalHours)->cascade()->format('%H:%I');
                            }
                        }
                        @endphp
                        @foreach($data as $index => $row)
                            <tr>
                                @if($index === 0)
                                    <td rowspan="{{ count($data) }}">{{ $name }}</td>
                                    <td rowspan="{{ count($data) }}">{{ $count }}</td>
                                @endif

                                <td>{{ @$row->place }}</td>

                                @if($index === 0)
                                    <td rowspan="{{ count($data) }}">{{ $duration }}</td>
                                @endif
                            </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No data found.</td>
                        </tr>
                        @endforelse
                        @endif

                    </table>
                </div>
                <div class="text-end mt-3">
                   
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 mb-3">
                <h5 class="text-white p-2 bg-secondary">Vehicle Wise Trip Report</h5>
            </div>
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="text-dark mb-2" style="font-size: 1rem;"> </h5>
                    <div class="btn-group">
                        <button class="btn btn-xs btn-primary toggle-btn active" data-target="tripWiseBar" name="graph">Graph</button>
                        <button class="btn btn-xs btn-secondary toggle-btn" data-target="tripWiseBar" name="table">Table</button>
                        <button class="btn btn-xs btn-success download-btn" data-target="tripWiseBar">Download</button>
                    </div>
                </div>
                <div class="chart-container" id="tripWiseBarGraph">
                    <canvas id="tripWiseBarChart"></canvas>
                </div>
                <div class="chart-legend mt-2" id="tripWiseBarLegend"></div>
                <div class="table-container d-none mt-2" id="tripWiseBarTable">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless report-table">
                            <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                <tr>
                                    <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Driver Name</th>
                                    <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Trip</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.75rem;"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-4">
                <div class="table-responsive scroll">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Name</th>
                            <th>Total Trips</th>
                            <th>Place</th>
                            <th>Total Hrs</th>
                        </tr>
                        @if(@$tripWiseReport)
                        @forelse($tripWiseReport as $name => $count)
                        @php
                        $vehicle = DB::table('vehicles')->where('reg_no', $name)->first();
                        $data = $vehicle ? App\Models\VehicleMovement::where('vehicle_id', $vehicle->id)->whereNull('vehicle_movements.deleted_at')->where('status', 'end')->get() : collect();

                        $totalHours = 0;
                        foreach ($data as $row) {
                            if ($row->time_in && $row->time_out) {
                                $start = \Carbon\Carbon::createFromFormat('H:i', substr($row->time_out, 0, 5));
                                $end = \Carbon\Carbon::createFromFormat('H:i', substr($row->time_in, 0, 5));
                                
                                $diffInMinutes = $end->diffInMinutes($start);
                                $totalHours += $diffInMinutes;

                                $duration = \Carbon\CarbonInterval::minutes($totalHours)->cascade()->format('%H:%I');
                            }
                        }
                        @endphp
                        @foreach($data as $index => $row)
                            <tr>
                                @if($index === 0)
                                    <td rowspan="{{ count($data) }}">{{ $name }}</td>
                                    <td rowspan="{{ count($data) }}">{{ $count }}</td>
                                @endif

                                <td>{{ @$row->place }}</td>

                                @if($index === 0)
                                    <td rowspan="{{ count($data) }}">{{ $duration }}</td>
                                @endif
                            </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No data found.</td>
                        </tr>
                        @endforelse
                        @endif
                    </table>
                </div>
                <div class="text-end mt-3">
                  
                </div>
            </div>
        </div>
			
        <div class="row mt-4">
            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">Pending Requests</h5>
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
                            <th>Delayed by (Days)</th>
                        </tr>
                       
                        @forelse(@$pendingMovements as $move)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($move->date)->format('d/m/Y') }}</td>                            
                            <td>{{@$move->vehicle_name}}</td>
                            <td>{{@$move->driver_name}}</td>
                            <td>{{@$move->department}}</td>
                            <td>{{@$move->place}}</td>
                            <td>{{@$move->purpose}}</td>
                            <td>{{@$move->delay_days ?? 0}}</td>
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

        <div class="row mt-4">
            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">Job Card - Pending</h5>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped">
                        <tbody>
                            <tr align="center">
                                <th>Date</th>
                                <th>Job Card No</th>
                                <th>Vehicle No</th>
                                <th>Service Type</th>
                                <th>Bill Amount</th>
                                <th>Checkout Date</th>
                            </tr>
                            @if(@$pendingJobs)
                            @forelse($pendingJobs as $job)
                            <tr class="border-bottom align-middle">
                                <td class="ps-3">{{ \Carbon\Carbon::parse($job->date)->format('d/m/Y') }}</td>
                                <td class="ps-3">{{ $job->card_no }}</td>
                                <td class="ps-3">{{ $job->vehicle_name }}</td>
                                <td class="ps-3">{{ $job->service_type }}</td>
                                <td class="ps-3">{{ $job->bill_amount }}</td>
                                <td class="ps-3">{{ \Carbon\Carbon::parse($job->checkout_date)->format('d/m/Y') }}</td>
                                 <td class="ps-3">
                                    <button type="button" class="btn btn-sm btn-primary d-flex justify-content-center view-btn"
                                        data-id="{{ $job->id }}"
                                        data-vehicle_id="{{ $job->vehicle_id }}"
                                        data-card_no="{{ $job->card_no }}"
                                        data-date="{{ $job->date }}"
                                        data-warranty="{{ $job->warranty }}"
                                        data-insurance="{{ $job->insurance }}"
                                        data-service_type="{{ $job->service_type }}"
                                        data-service_desc="{{ $job->service_desc }}"
                                        data-km_recorded="{{ $job->km_recorded }}"
                                        data-checkout_driver="{{ $job->checkout_driver }}"
                                        data-checkout_supervisor="{{ $job->checkout_supervisor }}"
                                        data-checkout_date="{{ $job->checkout_date }}"
                                        data-checkout_time="{{ $job->checkout_time }}"
                                        data-gatepass_no="{{ $job->gatepass_no }}"
                                        data-service_center="{{ $job->service_center }}"
                                        data-contact_no="{{ $job->contact_no }}"
                                        data-service_date="{{ $job->service_date }}"
                                        data-estimation="{{ $job->estimation }}"
                                        data-estimation_cost="{{ $job->estimation_cost }}"
                                        data-estimation_doc="{{ $job->estimation_doc }}"
                                        data-est_repair_time="{{ $job->est_repair_time }}"
                                        data-substitute_vehicle="{{ $job->substitute_vehicle }}"
                                        data-checkin_driver="{{ $job->checkin_driver }}"
                                        data-checkin_supervisor="{{ $job->checkin_supervisor }}"
                                        data-checkin_date="{{ $job->checkin_date }}"
                                        data-checkin_time="{{ $job->checkin_time }}"
                                        data-insurance_desc="{{ $job->insurance_desc }}"
                                        data-insurance_amount="{{ $job->insurance_amount }}"
                                        data-bill_desc="{{ $job->bill_desc }}"
                                        data-bill_amount="{{ $job->bill_amount }}"
                                        data-approve="{{ in_array($user->register_by, ['ADMIN']) ? 1 : 0 }}"
                                        data-bs-toggle="modal" data-bs-target="#vehicleModal">
                                        @if(in_array($user->register_by, ['DRIVER','SUPERVISOR']))
                                        Update
                                        @else
                                        Verify
                                        @endif
                                    </button>
                                </td> 
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No job card found.</td>
                            </tr>
                            @endforelse
                            @endif
                        </tbody>
                    </table>
            </div>
        </div>-->
        <div class="row mt-4">
            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">Trip Requests</h5>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>Request Date</th>
                            <!-- <th>Request Time</th> -->
                            <th>Location</th>
                            <th>Reason</th>
                            <th>Department </th>
                            <th></th>
                        </tr>
                       
                        @forelse(@$filteredRequests as $move)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($move->booking_date ?? $move->request_date)->format('d/m/Y') }}</td>
                            <!-- <td>{{ $move->booking_time ? \Carbon\Carbon::createFromFormat('H:i:s', @$move->booking_time)->format('g:i A') : '-' }}</td> -->
                            <td>{{@$move->patient_location ?? @$move->destination ?? @$move->address}}</td>
                            <td>{{@$move->reason ?? @$move->service_type}}</td>
                                 @if(@$move->request_type == 'Transport')
                                <td>{{@$move->department}}</td>
                                @elseif(@$move->request_type == 'HomeHealth')
                               <td>Home Health</td>
                                @elseif(@$move->type == 'ward')
                                <td>Ward</td>
                                @elseif(@$move->type == 'help-desk')
                                <td>Help Desk</td>
                                @endif
                            <td>
                                @if(@$move->request_type == 'Transport')
                                <a href="{{route('transport.index')}}" class="btn btn-sm btn-primary d-flex justify-content-center view-btn">View</a>
                                @elseif(@$move->request_type == 'HomeHealth')
                                <a href="{{route('home-health.index')}}" class="btn btn-sm btn-primary d-flex justify-content-center view-btn">View</a>
                                @elseif(@$move->type == 'ward')
                                <a href="{{route('ambulance.index', ['ward'])}}" class="btn btn-sm btn-primary d-flex justify-content-center view-btn">View</a>
                                @elseif(@$move->type == 'help-desk')
                                <a href="{{route('ambulance.index', ['help-desk'])}}" class="btn btn-sm btn-primary d-flex justify-content-center view-btn">View</a>
                                @endif
                            </td>
                        </tr>
                        @empty
                         <tr>
                            <td colspan="8" class="text-center text-muted py-3">No data found.</td>
                         </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- <nav aria-label="Page navigation" class="my-0">
                </nav> -->
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">Trips Allotted</h5>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>Request Date</th>
                            <!-- <th>Request Time</th> -->
                            <th>Location</th>
                            <th>Reason</th>
                            <th>Vehicle</th>
                            <th>Driver</th>
                            <th>Department </th>
                            <th></th>
                        </tr>
                       
                        @forelse(@$requestMovements as $move)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($move->date)->locale('en_US')->isoFormat('L') }}</td>
                            <!-- <td>{{ @$dataRow->time_in && \Carbon\Carbon::createFromFormat('H:i:s', @$dataRow->time_in)->format('g:i A') }}</td> -->
                            <td>{{@$move->place}}</td>
                            <td>{{@$move->purpose}}</td>
                            <td>{{@$move->vehicle_name}}</td>
                            <td>{{@$move->driver_name}}</td>
                            <td>{{@$move->department}}</td>
                            <td>
                                @if(@$move->type == 'transport')
                                <a href="{{route('transport.index')}}" class="btn btn-sm btn-primary d-flex justify-content-center view-btn">View</a>
                                @elseif(@$move->type == 'home-health')
                                <a href="{{route('home-health.index')}}" class="btn btn-sm btn-primary d-flex justify-content-center view-btn">View</a>
                                @elseif(@$move->type == 'ward')
                                <a href="{{route('ambulance.index', ['ward'])}}" class="btn btn-sm btn-primary d-flex justify-content-center view-btn">View</a>
                                @elseif(@$move->type == 'help-desk')
                                <a href="{{route('ambulance.index', ['helpdesk'])}}" class="btn btn-sm btn-primary d-flex justify-content-center view-btn">View</a>
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
                <nav aria-label="Page navigation" class="my-0">
                    {{ @$requestMovements->links() }}
                </nav>
            </div>
        </div>

		<div class="row mt-4">
            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">Manual Trips</h5>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-borderless data-table">
                    <tbody>
                        <tr style="color: white;">
                            <th>Date</th>
                            <th>Driver</th>
                            <th>Vehicle No</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Km Covered</th>
                            <th>Location</th>
                            <th>Purpose</th>
                        </tr>

                        @forelse($manualMovements as $dataRow)
                        <tr class="border-bottom align-middle">
                            <td class="ps-3">{{ \Carbon\Carbon::parse($dataRow->date)->format('d/m/Y') }}</td>
                            <td class="ps-3">{{ $dataRow->driver_name }}</td>
                            <td class="ps-3">{{ $dataRow->vehicle_name }}</td>
                            <td>{{ $dataRow->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', @$dataRow->time_in)->format('g:i A') : '-' }}</td>
                            <td>{{ $dataRow->time_out ? \Carbon\Carbon::createFromFormat('H:i:s', @$dataRow->time_out)->format('g:i A') : '-' }}</td>
                            <td class="ps-3">{{ $dataRow->km_out - $dataRow->km_in }}</td>
                            <td class="ps-3"><strong><a href="{{route('vehicle-movements.index')}}">{{ $dataRow->place }}</a></strong></td>
                            <td class="ps-3">{{ $dataRow->purpose }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">No vehicles found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <nav aria-label="Page navigation" class="my-0">
                    <ul class="pagination justify-content-center flex-wrap">
                        {{ $manualMovements->links() }}
                    </ul>
                </nav>
            </div>
        </div>
		
        <div class="row">
            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">Trip Status</h5>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Vehicle</th>
                            <th style="text-align: center;">Driver</th>
                            <th style="text-align: center;">Location</th>
                        </tr>
						
                        @forelse(@$todaysMovements as $data)
                        @if(@$data->status == 'start')
                        <tr>
                            <td>
                                <div class="progress-container riding">
                                    <div class="arrow-track"></div>
                                    <div class="car-icon"><img src="assets/images/ambulance.svg" style="height:30px"></div>
                                </div>
                            </td>
                            <td align="center">{{@$data->vehicle_name}}</td>
                            <td align="center">{{@$data->driver_name}}</td>
                            <td align="center">{{@$data->place}}</td>
                        </tr>
                        @endif
                        @if(@$data->status == 'allot')
                        <tr>
                            <td>
                                <div class="progress-container not-riding">
                                    <div class="arrow-track"></div>
                                    <div class="car-icon"><img src="assets/images/ambulance.svg" style="height:30px"></div>
                                </div>
                            </td>
                            <td align="center">{{@$data->vehicle_name}}</td>
                            <td align="center">{{@$data->driver_name}}</td>
                            <td align="center">{{@$data->place}}</td>
                        </tr>
                        @endif
                        @if(@$data->status == 'end')
                        <tr>
                            <td>
                                <div class="progress-container complete-riding">
                                    <div class="arrow-track"></div>
                                    <div class="car-icon" style="left: 85%"><img src="assets/images/ambulance.svg" style="height:30px"></div>
                                </div>
                            </td>
                            <td align="center">{{@$data->vehicle_name}}</td>
                            <td align="center">{{@$data->driver_name}}</td>
                            <td align="center">{{@$data->place}}</td>
                        </tr>
                        @endif

                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No data found.</td>
                        </tr>
                        @endforelse                        
                    </tbody>
                </table>
                <nav aria-label="Page navigation" class="my-0">
                    {{ @$todaysMovements->links() }}
                </nav>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">Cancelled Trips</h5>
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

		<div class="row mt-4">
            <div class="col-md-12">
                <h5 class="text-white p-2 bg-secondary">Pending Requests</h5>
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
                            <th>Delayed by (Days)</th>
                        </tr>
                       
                        @forelse(@$pendingMovements as $move)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($move->date)->format('d/m/Y') }}</td>                            
                            <td>{{@$move->vehicle_name}}</td>
                            <td>{{@$move->driver_name}}</td>
                            <td>{{@$move->department}}</td>
                            <td>{{@$move->place}}</td>
                            <td>{{@$move->purpose}}</td>
                            <td>{{@$move->delay_days ?? 0}}</td>
                        </tr>
                        @empty
                         <tr>
                            <td colspan="7" class="text-center text-muted py-3">No data found.</td>
                         </tr>
                        @endforelse
                    </tbody>
                </table>
                <nav aria-label="Page navigation" class="my-0">
                    {{ @$pendingMovements->links() }}
                </nav>
            </div>
        </div>
        
</section>
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

        const kmCoveredReport = @json(@$kmCoveredReport);
        const tripWiseReport = @json(@$tripWiseReport);
        const travelTimeReport = @json(@$travelTimeReport);
        const fuelWiseReport = @json(@$fuelWiseReport);
        const jobWiseReport = @json(@$jobWiseReport);
        const driverTripWiseReport = @json(@$driverTripWiseReport);
        const driverKmCoveredReport = @json(@$driverKmCoveredReport);
        const driverTravelTimeReport = @json(@$driverTravelTimeReport);

        const kmWiseChart = initPieChart('kmWiseChart', kmCoveredReport, [
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)'
        ], 'kmWiseLegend', 'Kms');
        const tripWiseChart = initPieChart('tripWiseChart', tripWiseReport, [
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)'
        ], 'tripWiseLegend', '');
        const travelWiseChart = initPieChart('travelWiseChart', travelTimeReport, [
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)'
        ], 'travelWiseLegend', 'Hours');
        const fuelWiseChart = initPieChart('fuelWiseChart', fuelWiseReport, [
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)'
        ], 'fuelWiseLegend', 'Litres');
        const jobWiseChart = initPieChart('jobWiseChart', jobWiseReport, [
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)'
        ], 'jobWiseLegend', 'INR');
        const driverTripWiseChart = initPieChart('driverTripWiseChart', driverTripWiseReport, [
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)'
        ], 'driverTripWiseLegend', '');
        const driverKmWiseChart = initPieChart('driverKmWiseChart', driverKmCoveredReport, [
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)'
        ], 'driverKmWiseLegend', 'Kms');
        const driverTravelWiseChart = initPieChart('driverTravelWiseChart', driverTravelTimeReport, [
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)'
        ], 'driverTravelWiseLegend', 'Hours');
        const tripWiseDriverBarChart = initBarChart('tripWiseDriverBarChart', driverTripWiseReport, [
            'rgba(255, 99, 132, 0.6)', // Red
            'rgba(54, 162, 235, 0.6)', // Blue
            'rgba(255, 206, 86, 0.6)', // Yellow
            'rgba(75, 192, 192, 0.6)', // Green
            'rgba(153, 102, 255, 0.6)', // Purple
            'rgba(255, 159, 64, 0.6)' // Orange
        ], 'tripWiseDriverBarLegend', '');
        const tripWiseBarChart = initBarChart('tripWiseBarChart', tripWiseReport, [
            'rgba(255, 99, 132, 0.6)', // Red
            'rgba(54, 162, 235, 0.6)', // Blue
            'rgba(255, 206, 86, 0.6)', // Yellow
            'rgba(75, 192, 192, 0.6)', // Green
            'rgba(153, 102, 255, 0.6)', // Purple
            'rgba(255, 159, 64, 0.6)' // Orange
        ], 'tripWiseBarLegend', '');
        // const paymentTypeChart = initDoughnutChart('paymentTypeChart', @json(@$paymentWiseReport), graphColors.slice(1, 5), 'paymentWiseLegend', 'Kms');
        // const areaWiseChart = initHorizontalBarChart('areaWiseChart', @json(@$areaWiseReport), graphColors[7], 'areaWiseLegend');

        // Initialize tables with data
        initTables();

        // Chart initialization functions
        function initPieChart(chartId, data, colors, legendId, measure) {
            const ctx = document.getElementById(chartId).getContext('2d');
            const labels = Object.keys(data);
            const values = Object.values(data);

            createLegend(legendId, labels, values, colors, measure);

            return new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 20,
                            right: 20,
                            top: 20,
                            bottom: 20
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            formatter: (value, context) => {
                                return value;
                            },
                            color: '#396A7D',
                            font: {
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label} : ${context.raw} ${measure}`;
                                }
                            }
                        }
                    },
                    aspectRatio: 1.5,
                    radius: '100%',
                    onClick: function(event, elements) {
                        if (elements.length) {
                            const index = elements[0].index;
                            const type = labels[index];
                            const url = 'get-ambulance-data';
                            // getTableData(type, 'type', url);
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        function initBarChart(chartId, data, color, legendId, measure) {
            const ctx = document.getElementById(chartId).getContext('2d');
            const labels = Object.keys(data);
            const values = Object.values(data);

            createLegend(legendId, labels, values, color, measure);

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
                        padding: {
                            left: 15,
                            right: 15,
                            top: 15,
                            bottom: 15
                        }
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
                            suggestedMax: Math.max(...values) * 1.2,
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                // maxRotation: 45,
                                // minRotation: 45,
                                // padding: 5,
                                // callback: function(value) {
                                //     // Smart label truncation with ellipsis
                                //     const label = this.getLabelForValue(value);
                                //     return label.length > 12 ?
                                //         label.substring(0, 10) + '...' :
                                //         label;
                                // }
                            },
                            grid: {
                                display: false
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
                                case 'tripWiseBarChart':
                                    type = 'driver';
                                    break;
                                case 'tripWiseDriverBarChart':
                                    type = 'vehicle';
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

        function initHorizontalBarChart(chartId, data, color, legendId) {
            const ctx = document.getElementById(chartId).getContext('2d');
            const labels = Object.keys(data).map((label, index) => `Area ${index+1}`);
            const values = Object.values(data);

            // Create legend with full label information
            createLegend(legendId, Object.keys(data), values, [color]);

            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Km Count',
                        data: values,
                        backgroundColor: color,
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 20,
                            right: 20,
                            top: 20,
                            bottom: 20
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    // Show full label in tooltip
                                    return `${Object.keys(data)[context.dataIndex]}: ${context.raw}`;
                                }
                            }
                        },
                        datalabels: {
                            color: '#396A7D',
                            anchor: 'end',
                            align: 'right',
                            formatter: (value) => value,
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            suggestedMax: function(context) {
                                const maxValue = Math.max(...context.chart.data.datasets[0].data);
                                return maxValue * 1.2; // Add 20% padding
                            }
                        },
                        y: {
                            ticks: {
                                autoSkip: false
                            }
                        }
                    },
                    onClick: function(event, elements) {
                        if (elements.length) {
                            const index = elements[0].index;
                            const area = Object.keys(data)[index];
                            const url = 'get-ambulance-data';
                            // getTableData(area, 'area', url);
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        function initDoughnutChart(chartId, data, colors, legendId) {
            const ctx = document.getElementById(chartId).getContext('2d');
            const labels = Object.keys(data);
            const values = Object.values(data);

            createLegend(legendId, labels, values, colors);

            return new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 10,
                                font: {
                                    size: 10
                                },
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    return data.labels.map((label, i) => ({
                                        text: `${label}: ${data.datasets[0].data[i]}`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false
                                    }));
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.raw} patients`;
                                }
                            }
                        },
                        datalabels: {
                            formatter: (value, context) => {
                                return value;
                            },
                            color: '#396A7D',
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    layout: {
                        padding: {
                            left: 20,
                            right: 20,
                            top: 20,
                            bottom: 20
                        }
                    },
                    aspectRatio: 1.2,
                    onClick: function(event, elements) {
                        if (elements.length) {
                            const index = elements[0].index;
                            const payment = labels[index];
                            const url = 'get-ambulance-data';
                            // getTableData(payment, 'payment', url);
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
                case 'kmWise':
                    chartId = 'kmWiseChart';
                    break;
                case 'tripWise':
                    chartId = 'tripWiseChart';
                    break;
                case 'travelWise':
                    chartId = 'travelWiseChart';
                    break;
                case 'fuelWise':
                    chartId = 'fuelWiseChart';
                    break;
                case 'jobWise':
                    chartId = 'jobWiseChart';
                    break;
                case 'driverTripWise':
                    chartId = 'driverTripWiseChart';
                case 'driverKmWise':
                    chartId = 'driverKmWiseChart';
                    break;
                case 'driverTravelWise':
                    chartId = 'driverTravelWiseChart';
                    break;
                case 'tripWiseDriverBar':
                    chartId = 'tripWiseDriverBarChart';
                    break;
                case 'tripWiseBar':
                    chartId = 'tripWiseBarChart';
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
                case 'kmWise':
                    table = document.querySelector('#kmWiseTable table');
                    break;
                case 'tripWise':
                    table = document.querySelector('#tripWiseTable table');
                    break;
                case 'travelWise':
                    table = document.querySelector('#travelWiseTable table');
                    break;
                case 'fuelWise':
                    table = document.querySelector('#fuelWiseTable table');
                    break;
                case 'jobWise':
                    table = document.querySelector('#jobWiseTable table');
                    break;
                case 'driverTripWise':
                    table = document.querySelector('#driverTripWiseTable table');
                    break;
                case 'driverKmWise':
                    table = document.querySelector('#driverKmWiseTable table');
                    break;
                case 'driverTravelWise':
                    table = document.querySelector('#driverTravelWiseTable table');
                    break;
                case 'tripWiseDriverBar':
                    table = document.querySelector('#tripWiseDriverBarTable table');
                    break;
                case 'tripWiseBar':
                    table = document.querySelector('#tripWiseBarTable table');
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
                'kmWise': @json(@$kmCoveredReport),
                'tripWise': @json(@$tripWiseReport),
                'travelWise': @json(@$travelTimeReport),
                'fuelWise': @json(@$fuelWiseReport),
                'jobWise': @json(@$jobWiseReport),
                'driverTripWise': @json(@$driverTripWiseReport),
                'driverKmWise': @json(@$driverKmCoveredReport),
                'driverTravelWise': @json(@$driverTravelTimeReport),
                'tripWiseDriverBar': @json(@$driverTripWiseReport),
                'tripWiseBar': @json(@$tripWiseReport),

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

<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('activityCalendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: @json((@$events ? @$events->map(function($event) {
                return [
                    'title' => $event->title, 'start' => $event->start_date, 'end' => $event->end_date
                ];
            }) : []))
        });

        calendar.render();
    });
</script> -->

<script>
    // Combine all data into one object
    const vehicleData = {
        metrics: [{
                name: "Total Trip",
                value: 12500,
                unit: "Kms"
            },
            {
                name: "Total Hours",
                value: 320,
                unit: "Hours"
            },
            {
                name: "Total Fuel",
                value: 980,
                unit: "Litres"
            },
            {
                name: "Total Service Cost",
                value: 1850,
                unit: "INR"
            }
        ],
        metrics1: [{
                name: "Total Trip",
                value: 12500,
                unit: "km"
            },
            {
                name: "Total Hours",
                value: 320,
                unit: "hours"
            }
        ],
        monthlySummary: [{
                month: 'Jan',
                distance: 950,
                hours: 25,
                fuel: 76,
                service: 0
            },
            {
                month: 'Feb',
                distance: 1200,
                hours: 32,
                fuel: 98,
                service: 150
            },
            {
                month: 'Mar',
                distance: 850,
                hours: 22,
                fuel: 65,
                service: 0
            },
            {
                month: 'Apr',
                distance: 1100,
                hours: 29,
                fuel: 89,
                service: 0
            },
            {
                month: 'May',
                distance: 1350,
                hours: 35,
                fuel: 105,
                service: 450
            },
            {
                month: 'Jun',
                distance: 1450,
                hours: 38,
                fuel: 112,
                service: 0
            },
            {
                month: 'Jul',
                distance: 1600,
                hours: 42,
                fuel: 125,
                service: 250
            },
            {
                month: 'Aug',
                distance: 1300,
                hours: 34,
                fuel: 104,
                service: 0
            },
            {
                month: 'Sep',
                distance: 1100,
                hours: 28,
                fuel: 87,
                service: 350
            },
            {
                month: 'Oct',
                distance: 950,
                hours: 24,
                fuel: 74,
                service: 0
            },
            {
                month: 'Nov',
                distance: 850,
                hours: 21,
                fuel: 65,
                service: 650
            },
            {
                month: 'Dec',
                distance: 800,
                hours: 20,
                fuel: 60,
                service: 0
            },
        ]
    };

    // Update DOM with actual data for first chart
    function updateMetricCards() {
        const metricCards = document.querySelectorAll('.metric-card');
        vehicleData.metrics.forEach((metric, index) => {
            if (metricCards[index]) {
                const valueElement = metricCards[index].querySelector('.metric-value');
                valueElement.innerHTML = `${metric.value.toLocaleString()}<span class="metric-unit">${metric.unit}</span>`;
            }
        });
    }

    // Create first chart (all metrics)
    function createDistributionChart() {
        const ctx = document.getElementById('distributionChart').getContext('2d');

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: vehicleData.metrics.map(item => item.name),
                datasets: [{
                    data: vehicleData.metrics.map(item => item.value),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const unit = vehicleData.metrics[context.dataIndex].unit;
                                return `${label}: ${value.toLocaleString()} ${unit}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Update DOM with actual data for second chart
    function updateMetricCards1() {
        const metricCards1 = document.querySelectorAll('.metric-card-secondary');
        vehicleData.metrics1.forEach((metric, index) => {
            if (metricCards1[index]) {
                const valueElement = metricCards1[index].querySelector('.metric-value');
                valueElement.innerHTML = `${metric.value.toLocaleString()}<span class="metric-unit">${metric.unit}</span>`;
            }
        });
    }

    // Create second chart (trips and hours only)
    function createDistributionChart1() {
        const ctx1 = document.getElementById('distributionChart1').getContext('2d');

        new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: vehicleData.metrics1.map(item => item.name),
                datasets: [{
                    data: vehicleData.metrics1.map(item => item.value),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const unit = vehicleData.metrics1[context.dataIndex].unit;
                                return `${label}: ${value.toLocaleString()} ${unit}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Initialize all charts when the page loads
    window.onload = function() {
        // Call all functions in one place
        updateMetricCards();
        createDistributionChart();
        updateMetricCards1();
        createDistributionChart1();
    };
</script>

<script>
    $('#vehicleSearch').on('click', function() {
        $.ajax({
            url: '{{ route('vehicles.summary',['vehicle']) }}',
            type: 'GET',
            data: {
                vehicle_id : $('#vehicleSelect').val(),
                driver_id : $('#driverSelect').val(),
                from_date : $('#from_date').val(),
                to_date : $('#to_date').val(),
            },
            success: function(response) {
                $('#totalTrips').text(response.totalTrips);
                $('#totalKms').text(response.totalKms + ' Kms');
                $('#totalHours').text(response.totalHours + ' Hours');
                $('#totalFuel').text(response.totalFuel + ' Litres');
                $('#totalCancel').text(response.totalCancel);
                $('#totalService').text(response.totalService + ' INR');
            },
            error: function() {
                alert('Failed to load data');
            }
        });
    });
    $('#driverSearch').on('click', function() {
        $.ajax({
            url: '{{ route('vehicles.summary',['driver']) }}',
            type: 'GET',
            data: {
                vehicle_id : $('#vehicleSelect').val(),
                driver_id : $('#driverSelect').val(),
                from_date : $('#from_date').val(),
                to_date : $('#to_date').val(),
            },
            success: function(response) {
                $('#totalTripsD').text(response.totalTrips);
                $('#totalKmsD').text(response.totalKms + ' Kms');
                $('#totalHoursD').text(response.totalHours + ' Hours');
                $('#totalFuelD').text(response.totalFuel + ' Litres');
                $('#totalCancelD').text(response.totalCancel);
                $('#totalServiceD').text(response.totalService + ' INR');
            },
            error: function() {
                alert('Failed to load data');
            }
        });
    });
</script>

@endsection