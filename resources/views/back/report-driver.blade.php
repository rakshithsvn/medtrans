@extends('layouts.back.index')

@section('content')

<style>
    h5.title {
        text-decoration: underline;
        color: #ac7070;

    }

    button.btn.btn-xs {
        font-size: 12px;
        padding: 5px;
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
        border-radius: 10px
    }

    @media only screen and (min-width:1100px) {
        .chart-wrapper {
            display: flex;
            gap: 30px;
            align-items: center;
        }
    }
	
	@media only screen and (min-width:1100px) and (max-width:1300px) {
	.chart-container {max-width: 150px;}
	}

    .chart-container {
        position: relative;
        width: 100%;
        height: 200px;max-width: 200px;
    }

    @media only screen and (min-width: 768px) {
        .chart-container {
            height: 200px;
        }
    }

    .legend {
        display: flex;
        flex-direction: column;
        gap: 12px; align-items: flex-start !important; padding:0px 10px; height: 150px;
        overflow-y: auto;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
    }

    .color-box {
        width: 14px;
        height: 14px;
        border-radius: 3px;
    }

    .label {
        flex-grow: 1;
        white-space: nowrap;
    }

    .value {
        font-weight: bold;
    }
</style>

<section class="section">
    <div class="card shadow-sm p-4">
        <form action="{{ route('reports.driver') }}" method="POST">
            @csrf
            <div class="row mt-3 align-items-end">
                <div class="col-md-5">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control border-primary" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-5">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control border-primary" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success">Search</button>
                </div>
            </div>
        </form>

        <div class="row mt-4">
            <div class="col-md-12 mb-3">
                <h5 class="text-white p-2 bg-secondary text-center">Driver Wise Report</h5>
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
                    <!-- <div class="col-md-3 mb-3">
                        <div class="box shadow" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                            <h5 class="text-secondary">Cancelled Trips</h5>
                            <p id="totalCancel" class="h3 fw-bold" style="color: #0082A3;">{{ @$vehicleSummary['totalCancel'] ?? 0 }}</p>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-3 mb-3">
                        <div class="box shadow" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                            <h5 class="text-secondary">Fuel Consumption</h5>
                            <p id="totalFuel" class="h3 fw-bold" style="color: #0082A3;">{{ @$vehicleSummary['totalFuel'] ?? 0 }} <span class="metric-unit">Litres</span></p>
                        </div>
                    </div> -->
                    
                </div>
            </div>

            <!-- <div class="col-md-12 mb-3">
                <h5 class="text-white p-2 bg-secondary text-center">Ambulance Wise Report</h5>
            </div> -->
            <div class="col-md-6 mt-4">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Km Wise Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="kmWise" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="kmWise" name="table">Table</button>
                            <button class="btn btn-xs btn-info download-btn" data-target="kmWise">Download</button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
						<div class="chart-wrapper">
                        <div class="chart-container" id="kmWiseGraph">
                            <canvas id="kmWiseChart"></canvas>
                        </div>
							<div class="legend align-items-center mt-3" id="kmWiseLegend"></div></div>
                        <div class="table-container d-none mt-2" id="kmWiseTable">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless report-table">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Driver</th>
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

            <div class="col-md-6 mt-4">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Hour Wise Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="travelTime" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="travelTime" name="table">Table</button>
                            <button class="btn btn-xs btn-info download-btn" data-target="travelTime">Download</button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
						<div class="chart-wrapper">
                        <div class="chart-container" id="travelTimeGraph">
                            <canvas id="travelTimeChart"></canvas>
                        </div>
                        <div class="legend align-items-center mt-3" id="travelTimeLegend"></div></div>
                        <div class="table-container d-none mt-2" id="travelTimeTable">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless report-table">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Driver</th>
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

            <!-- <div class="col-md-12 my-3">
                <h5 class="text-white p-2 bg-secondary text-center">Trip Wise Report</h5>
            </div> -->
            <div class="col-md-6 mt-4">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;">Trip Wise Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="tripWise" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="tripWise" name="table">Table</button>
                            <button class="btn btn-xs btn-info download-btn" data-target="tripWise">Download</button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
						<div class="chart-wrapper">
                        <div class="chart-container" id="tripWiseGraph">
                            <canvas id="tripWiseChart"></canvas>
                        </div>
							<div class="legend align-items-center mt-3" id="tripWiseLegend"></div></div>
                        <div class="table-container d-none mt-2" id="tripWiseTable">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless report-table">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Driver</th>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Trip</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 0.75rem;"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="col-md-6 mt-4">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;">Fuel Consumption Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="fuelWise" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="fuelWise" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="fuelWise">Download</button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="chart-container" id="fuelWiseGraph">
                            <canvas id="fuelWiseChart"></canvas>
                        </div>
                        <div class="legend align-items-center mt-3" id="fuelWiseLegend"></div>
                        <div class="table-container d-none mt-2" id="fuelWiseTable">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless report-table">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Driver</th>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Fuel</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 0.75rem;"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- <div class="col-md-6 mt-4">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;">Job Card Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="jobWise" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="jobWise" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="jobWise">Download</button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="chart-container" id="jobWiseGraph">
                            <canvas id="jobWiseChart"></canvas>
                        </div>
                        <div class="legend align-items-center mt-3" id="jobWiseLegend"></div>
                        <div class="table-container d-none mt-2" id="jobWiseTable">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless report-table">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Driver</th>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Bill</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 0.75rem;"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- <div class="row mt-4">
                <div class="col-md-12">
                    <h5 class="text-white p-2 bg-secondary">Cancelled Trips</h5>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Request Date</th>
                                <th>Driver</th>
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
            </div> -->

            <div class="modal fade" id="tableDataModal" tabindex="-1" aria-labelledby="tableDataModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="tableDataModalLabel">Driver Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table id="prodDataTable" class="table">
                                    <thead>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    </div>

        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.chart-legend').forEach(el => el.classList.add('d-none'));
                document.querySelectorAll('.chart-wrapper').forEach(el => el.classList.remove('d-none'));
            });
        </script>

        <script>
            document.querySelectorAll('.toggle-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const target = button.dataset.target; // e.g., "kmWise", "travelWise"
                    const type = button.getAttribute('name'); // "graph" or "table"

                    // Button toggle styles (only inside this group)
                    const btnGroup = button.parentElement;
                    btnGroup.querySelectorAll('.toggle-btn').forEach(btn => {
                        btn.classList.remove('active', 'btn-primary', 'btn-secondary');
                        btn.classList.add('btn-secondary');
                    });
                    button.classList.add('active');
                    button.classList.replace('btn-secondary', 'btn-primary');

                    // Get the correct wrapper and legend for this section
                    const chartWrapper = document.getElementById(`${target}ChartWrapper`);
                    const chartLegend = document.getElementById(`${target}Legend`);

                    if (!chartWrapper || !chartLegend) return;

                    if (type === 'graph') {
                        chartWrapper.classList.remove('d-none');
                        chartLegend.classList.add('d-none');
                    } else if (type === 'table') {
                        chartWrapper.classList.add('d-none');
                        chartLegend.classList.remove('d-none');
                    }
                });
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
        <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

        <script>
            const chartConfigs = [{
                    chartId: 'kmWiseChart',
                    labels: ['Red', 'Blue', 'Yellow', 'Green'],
                    colors: ['#F08080', '#8FBC8F', '#FFB6C1', '#87CEFA', '#D2691E', '#6495ED', '#00BFFF', '#FFDEAD', '#9ACD32', '#AFEEEE']
                },
                {
                    chartId: 'travelTimeChart',
                    labels: ['Orange', 'Teal', 'Purple', 'Grey'],
                    colors: ['#DC143C', '#00CED1', '#6A5ACD', '#B0C4DE', '#FF8C00', '#2E8B57', '#FF4500', '#20B2AA', '#9932CC', '#ADFF2F']
                },
                {
                    chartId: 'tripWiseChart',
                    labels: ['Help Desk', 'Ward', 'ICU'],
                    colors: ['#FF8C00', '#2E8B57', '#FF4500', '#20B2AA', '#9932CC', '#ADFF2F', '#6495ED', '#00BFFF', '#FFDEAD', '#9ACD32']
                },
                {
                    
                    labels: ['Internal Ambulance', 'External Ambulance'],
                    colors: ['#FFA500', '#008080', '#800080', '#A9A9A9', '#FF6347', '#4682B4', '#32CD32', '#FF1493', '#1E90FF', '#FFD700']
                },
                {
                    
                    labels: ['Internal Ambulance', 'External Ambulance'],
                    colors: ['#556B2F', '#6B8E23', '#DDA0DD', '#BC8F8F', '#FF00FF', '#E9967A', '#483D8B', '#BDB76B', '#00FF7F', '#C71585']
                },
                {
                    
                    labels: ['Internal Ambulance', 'External Ambulance'],
                    colors: ['#DC143C', '#00CED1', '#6A5ACD', '#B0C4DE', '#FF8C00', '#2E8B57', '#FF4500', '#20B2AA', '#9932CC', '#ADFF2F']
                }
            ];

            const kmWiseReport = @json(@$kmCoveredReport);
            const travelTimeReport = @json(@$travelTimeReport);
            const tripWiseReport = @json(@$tripWiseReport);
            const fuelWiseReport = @json(@$fuelWiseReport);
            const jobWiseReport = @json(@$jobWiseReport);            

            const kmWiseChart = initBarChart('kmWiseChart', kmWiseReport, 'kmWiseLegend', 'Kms');

            const travelTimeChart = initBarChart('travelTimeChart', travelTimeReport, 'travelTimeLegend', 'Hrs');

            const tripWiseChart = initBarChart('tripWiseChart', tripWiseReport, 'tripWiseLegend', ''); 

            // const fuelWiseChart = initBarChart('fuelWiseChart', fuelWiseReport, [
            //     'rgba(255, 99, 132, 0.6)', // Red
            //     'rgba(54, 162, 235, 0.6)', // Blue
            //     'rgba(255, 206, 86, 0.6)', // Yellow
            //     'rgba(75, 192, 192, 0.6)', // Green
            //     'rgba(153, 102, 255, 0.6)', // Purple
            //     'rgba(255, 159, 64, 0.6)' // Orange
            // ], 'fuelWiseLegend', 'In-house');

            // const jobWiseChart = initBarChart('jobWiseChart', jobWiseReport, [
            //     'rgba(255, 99, 132, 0.6)', // Red
            //     'rgba(54, 162, 235, 0.6)', // Blue
            //     'rgba(255, 206, 86, 0.6)', // Yellow
            //     'rgba(75, 192, 192, 0.6)', // Green
            //     'rgba(153, 102, 255, 0.6)', // Purple
            //     'rgba(255, 159, 64, 0.6)' // Orange
            // ], 'jobWiseLegend', 'In-house');
            
            initTables();

            function initBarChart(chartId, data, legendId, measure) {
        const ctx = document.getElementById(chartId).getContext('2d');
        const labels = Object.keys(data);
        const values = Object.values(data);

        const config = chartConfigs.find(cfg => cfg.chartId === chartId);
        const backgroundColors = config ? config.colors : labels.map(() => 'rgba(200,200,200,0.5)');

        createLegend(legendId, labels, values, backgroundColors, measure);

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: backgroundColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                color: '#fff',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                                formatter: (value) => value,
                            }
                        },
                        onClick: function(event, elements) {
                            if (elements.length) {
                                const index = elements[0].datasetIndex;
                                const value = elements[0].index;
                                const label = this.data.labels[value];
                                const url = `get-driver-data/${measure}`;
                                getTableData(index, label, url);
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            }

            function createLegend(legendId, labels, values, colors, measure) {
                const legendContainer = document.getElementById(legendId);
                if (!legendContainer) return;

                legendContainer.innerHTML = '';

                labels.forEach((label, i) => {

                    const item = document.createElement('div');
                        item.className = 'legend-item';

                        const colorBox = document.createElement('span');
                        colorBox.className = 'color-box';
                        colorBox.style.backgroundColor = colors[i];

                        const labelSpan = document.createElement('span');
                        labelSpan.className = 'label';
                        labelSpan.textContent = label;

                        const valueSpan = document.createElement('span');
                        valueSpan.className = 'value';
                        valueSpan.textContent = `${values[i]} ${measure}`;

                        item.appendChild(colorBox);
                        item.appendChild(labelSpan);
                        item.appendChild(valueSpan);

                        legendContainer.appendChild(item);
                });
            }

            function initTables() {
                const reports = {
                    'fuelWise': fuelWiseReport,
                    'kmWise': kmWiseReport,
                    'travelTime': travelTimeReport,
                    'tripWise': tripWiseReport,
                    'jobWise': jobWiseReport,
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
                    case 'fuelWise':
                        chartId = 'fuelWiseChart';
                        break;
                    case 'kmWise':
                        chartId = 'kmWiseChart';
                        break;
                    case 'travelTime':
                        chartId = 'travelTimeChart';
                        break;
                    case 'tripWise':
                        chartId = 'tripWiseChart';
                        break;
                    case 'jobWise':
                        chartId = 'jobWiseChart';
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

            function downloadTable(target) {
                let table;
                switch (target) {
                    case 'fuelWise':
                        table = document.querySelector('#fuelWiseTable table');
                        break;
                    case 'kmWise':
                        table = document.querySelector('#kmWiseTable table');
                        break;
                    case 'travelTime':
                        table = document.querySelector('#travelTimeTable table');
                        break;
                    case 'tripWise':
                        table = document.querySelector('#tripWiseTable table');
                        break;
                    case 'jobWise':
                        table = document.querySelector('#jobWiseTable table');
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

            async function getTableData(exData, label, url) {
            try {
                const params = new URLSearchParams(window.location.search);

                const type = params.get('type') || '';
                const patientType = params.get('patient_type') || '';
                const year = params.get('year') || '';
                const executiveId = params.get('executive_id') || '';

                const requestURL = `/${url}?label=${encodeURIComponent(label)}`;
                const response = await fetch(requestURL);

                // Check if the response is okay
                if (!response.ok) {
                    throw new Error('Failed to fetch data from the server');
                }

                // Parse the JSON response
                const data = await response.json();

                // Check if there is an error (e.g., executive not found)
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

        // Function to display the patient data in the Bootstrap modal
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
                const row = document.createElement('tr');
                const td = document.createElement('td');
                td.colSpan = 6; // Adjust the colspan according to the number of columns
                td.textContent = 'No data available.';
                row.appendChild(td);
                tableBody.appendChild(row);
            }

            // Show the modal using Bootstrap's Modal API
            const modal = new bootstrap.Modal(document.getElementById('tableDataModal'));
            modal.show();
        }

            // chartConfigs.forEach(cfg => {
            //     const ctx = document.getElementById(`donutChart${cfg.id}`).getContext('2d');

            //     new Chart(ctx, {
            //         type: 'doughnut',
            //         data: {
            //             labels: cfg.labels,
            //             datasets: [{
            //                 data: cfg.data,
            //                 backgroundColor: cfg.colors,
            //                 borderWidth: 1
            //             }]
            //         },
            //         options: {
            //             responsive: true,
            //             maintainAspectRatio: false,
            //             cutout: '70%',
            //             plugins: {
            //                 legend: {
            //                     display: false
            //                 },
            //                 datalabels: {
            //                     color: '#fff',
            //                     font: {
            //                         weight: 'bold',
            //                         size: 12
            //                     },
            //                     formatter: (value, context) => {
            //                         const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
            //                         return ((value / total) * 100).toFixed(1);
            //                     }
            //                 }
            //             }
            //         },
            //         plugins: [ChartDataLabels]
            //     });

            //     // Create custom legend
            //     const legendContainer = document.getElementById(`customLegend${cfg.id}`);
            //     cfg.labels.forEach((label, i) => {
            //         const item = document.createElement('div');
            //         item.className = 'legend-item';

            //         const colorBox = document.createElement('span');
            //         colorBox.className = 'color-box';
            //         colorBox.style.backgroundColor = cfg.colors[i];

            //         const labelSpan = document.createElement('span');
            //         labelSpan.className = 'label';
            //         labelSpan.textContent = label;

            //         const valueSpan = document.createElement('span');
            //         valueSpan.className = 'value';
            //         valueSpan.textContent = `${cfg.data[i]}`;

            //         item.appendChild(colorBox);
            //         item.appendChild(labelSpan);
            //         item.appendChild(valueSpan);

            //         legendContainer.appendChild(item);
            //     });
            // });
        </script>

        @endsection