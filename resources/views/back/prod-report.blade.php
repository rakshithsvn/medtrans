@extends('layouts.back.index')

@section('content')

<style>
    @media (max-width: 768px) {
        .card-body {
            padding: 0.5rem !important;
        }

        .text-center {
            margin: 0 5px !important;
        }

        .form-select {
            font-size: 10px !important;
        }
    }

    @media (max-width: 768px) {
        .form-select {
            font-size: 10px !important;
            padding: 0.25rem 0.5rem !important;
        }

        .d-flex.flex-wrap {
            gap: 5px !important;
        }
    }

    /* Custom thin scrollbar styling */
    .cust-card {
        height: 300px;
        overflow: auto;
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

    /* Chart container styling */
    .chart-container {
        flex-grow: 1;
        width: 100%;
        position: relative;
    }

    /* Table container adjustments */
    .table-container {
        flex-grow: 1;
        overflow: visible;
    }

    .table-container .table-responsive {
        overflow: visible;
        min-height: auto;
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

    /* Additional CSS for the doughnut chart */
    #patientMarketingGraph {
        position: relative;
    }

     #employeePerformanceTable {
        scrollbar-width: thin;
        scrollbar-color: #888 #f1f1f1;
    }
    
    #employeePerformanceTable::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    #employeePerformanceTable::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    #employeePerformanceTable::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
    
    /* Ensure the canvas inside has proper dimensions */
    #employeePerformanceTable canvas {
        width: 100% !important;
        height: auto !important;
        min-height: 180px;
    }

</style>

<section class="section">

    <!-- Filters Section -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <!-- Top Section: Combined Patient Statistics and Filters -->
            <div class="row g-3 align-items-center">
                <!-- Left Side: Combined Patient Statistics -->
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm p-2" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9); height: 60px;">
                        <div class="card-body p-1 d-flex justify-content-between align-items-center flex-wrap overflow-hidden">
                            
                            <div class="text-center mx-1 flex-grow-1" style="min-width: 60px;">
                                <h6 class="text-secondary mb-0" style="font-size: 14px;">{{@$visitData->count()}}</h6>
                                <div class="fw-bold text-primary" style="font-size: 14px;">Total Visits</div>
                            </div>
                            @foreach(@$employees as $ex)
                            <div class="text-center mx-1 flex-grow-1" style="min-width: 60px;">
                                <h6 class="text-secondary mb-0" style="font-size: 14px;">{{@$visitData->where('employee_id', $ex->id)->count()}}</h6>
                                <div class="fw-bold text-primary" style="font-size: 12px;">{{@$ex->name}}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Side: Filters -->
                <div class="col-12 col-md-6">
                    <form action="{{ route('prod-report') }}" method="GET">
                        @csrf
                        <div class="justify-content-end gap-2 row g-2 align-items-center" style="margin-top:-50px;">
                            <div class="col-12 col-md-auto">
                                <label class="form-label text-secondary small mb-0" style="font-size: 12px;">Type</label>
                                <select name="type" class="form-select-sm border-primary">
                                    <option value="">All</option>
                                    @foreach(['IP','OP'] as $typeData)
                                    <option value="{{ $typeData }}" {{ request('type') == $typeData ? 'selected' : '' }}>{{ $typeData }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-auto">
                                <label class="form-label text-secondary small mb-0" style="font-size: 12px;">Patient Type</label>
                                <select name="patient_type" class="form-select-sm border-primary">
                                    <option value="">All</option>
                                    @foreach(['HMIS','Marketing'] as $typeData1)
                                    <option value="{{ $typeData1 }}" {{ request('patient_type') == $typeData1 ? 'selected' : '' }}>{{ $typeData1 }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-auto">
                                <label class="form-label text-secondary small mb-0" style="font-size: 12px;">Year</label>
                                <select name="year" id="year" class="form-select-sm border-primary">
                                    <option value="">All</option>
                                    @foreach(range(date('Y') - 3, date('Y')) as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-auto">
                                <label class="form-label text-secondary small mb-0" style="font-size: 12px;">Employee</label>
                                <select id="employeeIdSelect" name="employee_id" class="form-select-sm border-primary">
                                    <option value="">All</option>
                                    @foreach($employees as $id=>$employee_data)
                                    <option value="{{$id+1}}" {{ request('employee_id') == $employee_data->id ? 'selected' : '' }}>{{ $employee_data->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-auto mt-4">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bottom Section: Patient Statistics Cards -->
            <div class="row g-2 g-md-3 pt-1">
                <!-- Total Patients Card -->
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm mb-2 mb-md-0" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9); height: 60px;">
                        <div class="card-body p-1 d-flex justify-content-between align-items-center">
                            <div class="text-center mx-2">
                                <h6 class="text-secondary mb-0" style="font-size: 14px;">{{@$patients->count()}}</h6>
                                <div class="fw-bold text-primary" style="font-size: 12px;">Total Patients</div>
                            </div>
                            <div class="text-center mx-2">
                                <h6 class="text-secondary mb-0" style="font-size: 14px;">{{@$patients->where('type', 'IP')->count()}}</h6>
                                <div class="fw-bold text-primary" style="font-size: 12px;">IP Patients</div>
                            </div>
                            <div class="text-center mx-2">
                                <h6 class="text-secondary mb-0" style="font-size: 14px;">{{@$patients->where('type', 'OP')->count()}}</h6>
                                <div class="fw-bold text-primary" style="font-size: 12px;">OP Patients</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @php
                    $patientsMarketCount = @$patients->where('patient_type', 'Marketing');
                @endphp

                <!-- Marketing Patients Card -->
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm mb-2 mb-md-0" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9); height: 60px;">
                        <div class="card-body p-1 d-flex justify-content-between align-items-center">
                            <div class="text-center mx-2">
                                <h6 class="text-secondary mb-0" style="font-size: 14px;">{{@$patientsMarketCount->count()}}</h6>
                                <div class="fw-bold text-primary" style="font-size: 12px;">Marketing Patients</div>
                            </div>
                            <div class="text-center mx-2">
                                <h6 class="text-secondary mb-0" style="font-size: 14px;">{{@$patientsMarketCount->where('type', 'IP')->count()}}</h6>
                                <div class="fw-bold text-primary" style="font-size: 12px;">Marketing IP Patients</div>
                            </div>
                            <div class="text-center mx-2">
                                <h6 class="text-secondary mb-0" style="font-size: 14px;">{{@$patientsMarketCount->where('type', 'OP')->count()}}</h6>
                                <div class="fw-bold text-primary" style="font-size: 12px;">Marketing OP Patients</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employees Card -->
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9); height: 60px;">
                        <div class="card-body p-1 d-flex justify-content-between align-items-center">
                            <div class="text-center mx-2">
                                <h6 class="text-secondary mb-0" style="font-size: 14px;">{{@$employees->count()}}</h6>
                                <div class="fw-bold text-primary" style="font-size: 12px;">Employees</div>
                            </div>
                            <div class="text-center mx-2">
                                <h6 class="text-secondary mb-0" style="font-size: 14px;">{{@$doctors->count()}}</h6>
                                <div class="fw-bold text-primary" style="font-size: 12px;">Total Doctors</div>
                            </div>
                            <div class="text-center mx-2">
                                <h6 class="text-secondary mb-0" style="font-size: 14px;">{{@$ambulances->count()}}</h6>
                                <div class="fw-bold text-primary" style="font-size: 12px;">Ambulances</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 p-3 mb-4">
        <div class="card-body p-3">
            <!-- Patient Stats and Reports Section -->
            <div class="row g-4">
                <!-- Total Marketing Ref Patients -->
                <div class="col-md-4">
                    <div class="cust-card shadow-sm p-2 equal-height" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="text-dark mb-0" style="font-size: 0.7rem;">
                                <i class="fas fa-chart-pie me-2"></i>Total Marketing Ref Patients
                            </h6>
                            <div class="btn-group">
                                <button class="btn btn-xs btn-primary toggle-btn active" data-target="patientMarketing" name="graph">Graph</button>
                                <button class="btn btn-xs btn-secondary toggle-btn" data-target="patientMarketing" name="table">Table</button>
                                <button class="btn btn-xs btn-success download-btn" data-target="patientMarketing">Download</button>
                            </div>
                        </div>
                        <div class="chart-container" id="patientMarketingGraph" style="height: 200px;">
                            <canvas id="patientListGraph"></canvas>
                        </div>
                        <div class="chart-legend" id="patientMarketingLegend"></div>
                        <div class="table-container d-none mt-2" id="patientMarketingTable" style="height: 200px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Employee</th>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Patients</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 0.75rem;"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Activity Report -->
                <div class="col-md-8">
                    <div class="cust-card shadow-sm p-2 equal-height" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="text-dark mb-0" style="font-size: 0.7rem;">
                                <i class="fas fa-layer-group me-2"></i>Employee Activity Report
                            </h6>
                            <div class="btn-group">
                                <button class="btn btn-xs btn-primary toggle-btn active" data-target="employeeActivity" name="graph">Graph</button>
                                <button class="btn btn-xs btn-secondary toggle-btn" data-target="employeeActivity" name="table">Table</button>
                                <button class="btn btn-xs btn-success download-btn" data-target="employeeActivity">Download</button>
                            </div>
                        </div>
                        <div class="chart-container" id="employeeActivityGraph" style="height: 200px;">
                            <canvas id="activityBarChart"></canvas>
                        </div>
                        <div class="chart-legend" id="employeeActivityLegend"></div>
                        <div class="table-container d-none mt-2" id="employeeActivityTable" style="height: 200px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Month</th>
                                            @foreach(@$employees as $ex)
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">{{@$ex->name}}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 0.75rem;"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row: Ambulance Utilization & Employee Visit Report -->
            <div class="row g-4 mt-3">
                <!-- 0% Of Employee Performance -->
                <div class="col-md-4">
                    <div class="cust-card shadow-sm p-2 equal-height" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="text-dark mb-0" style="font-size: 0.7rem;">
                                <i class="fas fa-chart-pie me-2"></i>0% Of Employee Performance
                            </h6>
                            <div class="btn-group">
                                <button class="btn btn-xs btn-primary toggle-btn active" data-target="employeePerformance" name="graph">Visits</button>
                                <button class="btn btn-xs btn-secondary toggle-btn" data-target="employeePerformance" name="table">Activities</button>
                                <button class="btn btn-xs btn-success download-btn" data-target="employeePerformance">Download</button>
                            </div>
                        </div>
                        <div class="chart-container" id="employeePerformanceGraph" style="height: 200px;">
                            <canvas id="employeeVisitBarChart"></canvas>
                        </div>
                        <div class="chart-legend" id="employeePerformanceLegend"></div>
                        <div class="table-container d-none mt-2" id="employeePerformanceTable" style="height: 200px; overflow-y: auto;">
                            <canvas id="employeeActivityBarChart"></canvas>
                            <div class="chart-legend" id="employeePerformanceTableLegend"></div>
                        </div>
                    </div>
                </div>

                <!-- Employee Visit Report -->
                <div class="col-md-8">
                    <div class="cust-card shadow-sm p-2 equal-height" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="text-dark mb-0" style="font-size: 0.7rem;">
                                <i class="fas fa-chart-bar me-2"></i>Employee Visit Report
                            </h6>
                            <div class="btn-group">
                                <button class="btn btn-xs btn-primary toggle-btn active" data-target="employeeVisit" name="graph">Graph</button>
                                <button class="btn btn-xs btn-secondary toggle-btn" data-target="employeeVisit" name="table">Table</button>
                                <button class="btn btn-xs btn-success download-btn" data-target="employeeVisit">Download</button>
                            </div>
                        </div>
                        <div class="chart-container" id="employeeVisitGraph" style="height: 200px;">
                            <canvas id="visitBarChart"></canvas>
                        </div>
                        <div class="chart-legend" id="employeeVisitLegend"></div>
                        <div class="table-container d-none mt-2" id="employeeVisitTable" style="height: 200px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Month</th>
                                            @foreach(@$employees as $ex)
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">{{@$ex->name}}</th>
                                            @endforeach
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
    </div>

    <div class="modal fade" id="tableDataModal" tabindex="-1" aria-labelledby="tableDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tableDataModalLabel">Employee Patient Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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
</section>

@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Register the datalabels plugin
        Chart.register(ChartDataLabels);

        const employeeColors = [
            '#A3D8FF', '#D4A5A5', '#FFABAB', '#4CAF50'
            , '#D8BFD8', '#C7CEEA', '#FFDDC1', '#A8E6CF'
            , '#FFD3B6', '#74B9FF', '#FF9AA2', '#B5EAD7'
            , '#FEC3A6', '#85C1E9', '#F9EBEA'
        ];

        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const employeeNames = @json($employees->pluck('name'));

        const employeeWiseReport = @json(@$employeeWiseReport);
        const employees = Object.keys(employeeWiseReport);
        const employeePatientCounts = Object.values(employeeWiseReport);

        const employeeVisitReport = @json(@$employeeVisitReport);
        const visitEmployees = Object.keys(employeeVisitReport);
        const employeeVisitCounts = Object.values(employeeVisitReport);

        const employeeActivityReport = @json(@$employeeActivityReport);
        const activityEmployees = Object.keys(employeeActivityReport);
        const employeeActivityCounts = Object.values(employeeActivityReport);

        const activityData = Object.values(@json($employeeActivityData));
        const employeeActivityData = activityData.map(item => {
            let arr = new Array(12).fill(0);
            Object.keys(item).forEach(key => {
                arr[parseInt(key)] = item[key];
            });
            return arr;
        });

        const visitData = Object.values(@json($employeeVisitData));
        const employeeVisitData = visitData.map(item => {
            let arr = new Array(12).fill(0);
            Object.keys(item).forEach(key => {
                arr[parseInt(key)] = item[key];
            });
            return arr;
        });

        // Initialize all charts
        const patientListGraph = initPatientListGraph();
        const employeeVisitBarChart = initEmployeeVisitBarChart();
        const employeeActivityBarChart = initEmployeeActivityBarChart();
        const visitBarChart = initVisitBarChart();
        const activityBarChart = initActivityBarChart();

        // Populate tables initially
        populateTable("patientMarketingTable", employees, employeePatientCounts);
        populateActivityTable();
        populateVisitTable();

        // Create legends for all charts
        createMarketingLegend();
        createActivityLegend();
        createVisitLegend();
        createPerformanceLegend();

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
                    if (target === 'employeePerformance') {
                        await downloadPerformanceTable();
                    } else {
                        downloadTable(target);
                    }
                }
            });
        });

        async function downloadPerformanceTable() {
            const canvas = document.getElementById('employeeActivityBarChart');
            const legend = document.getElementById('employeePerformanceTableLegend');
            
            // Create a container to hold everything
            const container = document.createElement('div');
            container.style.position = 'fixed';
            container.style.left = '-10000px';
            container.style.top = '0';
            container.style.zIndex = '99999';
            container.style.backgroundColor = 'white';
            container.style.padding = '20px';
            container.style.borderRadius = '8px';
            container.style.boxShadow = '0 0 10px rgba(0,0,0,0.1)';
            
            // Clone the chart
            const chartClone = document.createElement('canvas');
            chartClone.width = canvas.width;
            chartClone.height = canvas.height;
            chartClone.getContext('2d').drawImage(canvas, 0, 0);
            container.appendChild(chartClone);
            
            // Clone and prepare the legend
            if (legend) {
                const legendClone = legend.cloneNode(true);
                legendClone.style.display = 'block';
                legendClone.style.backgroundColor = 'white';
                legendClone.style.padding = '10px';
                legendClone.style.marginTop = '20px';
                legendClone.style.maxHeight = 'none';
                legendClone.style.overflow = 'visible';
                container.appendChild(legendClone);
            }
            
            document.body.appendChild(container);
            
            try {
                // Use html2canvas to capture everything
                const canvasImage = await html2canvas(container, {
                    scale: 2,
                    backgroundColor: null,
                    logging: false,
                    useCORS: true,
                    allowTaint: true,
                    scrollX: 0,
                    scrollY: 0
                });
                
                // Create final canvas with white background
                const finalCanvas = document.createElement('canvas');
                finalCanvas.width = canvasImage.width;
                finalCanvas.height = canvasImage.height;
                const ctx = finalCanvas.getContext('2d');
                
                // Fill with white background
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, finalCanvas.width, finalCanvas.height);
                
                // Draw the captured image
                ctx.drawImage(canvasImage, 0, 0);
                
                // Download
                finalCanvas.toBlob(blob => {
                    saveAs(blob, 'employee_performance_report.png');
                }, 'image/png', 1);
                
            } catch (error) {
                console.error('Error generating performance chart:', error);
            } finally {
                // Clean up
                document.body.removeChild(container);
            }
        }

        // Marketing Legend Creation
        function createMarketingLegend() {
            const container = document.getElementById('patientMarketingLegend');
            container.innerHTML = '';

            employees.forEach((exec, index) => {
                const legendItem = document.createElement('div');
                legendItem.style.display = 'flex';
                legendItem.style.alignItems = 'center';
                legendItem.style.marginBottom = '5px';

                const colorBox = document.createElement('div');
                colorBox.className = 'legend-color-box';
                colorBox.style.backgroundColor = employeeColors[index % employeeColors.length];

                const labelText = document.createElement('span');
                labelText.textContent = `${exec}: ${employeePatientCounts[index]} patients`;
                labelText.style.fontSize = '12px';

                legendItem.appendChild(colorBox);
                legendItem.appendChild(labelText);
                container.appendChild(legendItem);
            });
        }

        // Activity Legend Creation
        function createActivityLegend() {
            const container = document.getElementById('employeeActivityLegend');
            container.innerHTML = '';

            employeeNames.forEach((exec, index) => {
                const total = employeeActivityData[index].reduce((a, b) => a + b, 0);
                
                const legendItem = document.createElement('div');
                legendItem.style.display = 'flex';
                legendItem.style.alignItems = 'center';
                legendItem.style.marginBottom = '5px';

                const colorBox = document.createElement('div');
                colorBox.className = 'legend-color-box';
                colorBox.style.backgroundColor = employeeColors[index % employeeColors.length];

                const labelText = document.createElement('span');
                labelText.textContent = `${exec}: ${total} activities`;
                labelText.style.fontSize = '12px';

                legendItem.appendChild(colorBox);
                legendItem.appendChild(labelText);
                container.appendChild(legendItem);
            });
        }

        // Visit Legend Creation
        function createVisitLegend() {
            const container = document.getElementById('employeeVisitLegend');
            container.innerHTML = '';

            employeeNames.forEach((exec, index) => {
                const legendItem = document.createElement('div');
                legendItem.style.display = 'flex';
                legendItem.style.alignItems = 'center';
                legendItem.style.marginBottom = '5px';

                const colorBox = document.createElement('div');
                colorBox.className = 'legend-color-box';
                colorBox.style.backgroundColor = employeeColors[index % employeeColors.length];

                const labelText = document.createElement('span');
                const total = employeeVisitData[index].reduce((a, b) => a + b, 0);
                labelText.textContent = `${exec}: ${total} visits`;
                labelText.style.fontSize = '12px';

                legendItem.appendChild(colorBox);
                legendItem.appendChild(labelText);
                container.appendChild(legendItem);
            });
        }

        // Performance Legend Creation
        function createPerformanceLegend() {
            const graphLegend = document.getElementById('employeePerformanceLegend');
            const tableLegend = document.getElementById('employeePerformanceTableLegend');

            // Graph view legend
            graphLegend.innerHTML = '';
            visitEmployees.forEach((exec, index) => {
                const legendItem = document.createElement('div');
                legendItem.style.display = 'flex';
                legendItem.style.alignItems = 'center';
                legendItem.style.marginBottom = '5px';

                const colorBox = document.createElement('div');
                colorBox.className = 'legend-color-box';
                colorBox.style.backgroundColor = employeeColors[index % employeeColors.length];

                const labelText = document.createElement('span');
                labelText.textContent = `${exec}: ${employeeVisitCounts[index]} visits`;
                labelText.style.fontSize = '12px';

                legendItem.appendChild(colorBox);
                legendItem.appendChild(labelText);
                graphLegend.appendChild(legendItem);
            });

            // Table view legend
            tableLegend.innerHTML = '';
            activityEmployees.forEach((exec, index) => {
                const legendItem = document.createElement('div');
                legendItem.style.display = 'flex';
                legendItem.style.alignItems = 'center';
                legendItem.style.marginBottom = '5px';

                const colorBox = document.createElement('div');
                colorBox.className = 'legend-color-box';
                colorBox.style.backgroundColor = employeeColors[index % employeeColors.length];

                const labelText = document.createElement('span');
                labelText.textContent = `${exec}: ${employeeActivityCounts[index]} activities`;
                labelText.style.fontSize = '12px';

                legendItem.appendChild(colorBox);
                legendItem.appendChild(labelText);
                tableLegend.appendChild(legendItem);
            });
        }

        // Patient List Graph (Doughnut Chart)
        function initPatientListGraph() {
            const ctx = document.getElementById('patientListGraph').getContext('2d');
            return new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: employees,
                    datasets: [{
                        data: employeePatientCounts,
                        backgroundColor: employeeColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            color: '#396A7D',
                            font: {
                                size: 10,
                                weight: 'bold'
                            },
                            formatter: (value) => value, // Show actual value instead of percentage
                            anchor: 'center',
                            align: 'center'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.raw} patients`;
                                }
                            }
                        }
                    },
                    onClick: function (event, elements) {
                        if (elements.length) {
                            const index = elements[0].index;
                            const exData = employees[index];
                            const url = 'get-prod-data';
                            getTableData(exData, null, url);
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        // Function to get data from the Laravel backend
        async function getTableData(exData, monthId, url) {
            try {
                const params = new URLSearchParams(window.location.search);

                const type = params.get('type') || '';
                const patientType = params.get('patient_type') || '';
                const year = params.get('year') || '';
                const employeeId = params.get('employee_id') || '';

                const requestURL = `/${url}?exData=${encodeURIComponent(exData)}&monthId=${encodeURIComponent(monthId)}&type=${encodeURIComponent(type)}&patient_type=${encodeURIComponent(patientType)}&year=${encodeURIComponent(year)}&employee_id=${encodeURIComponent(employeeId)}`;
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
                modalTitle.textContent = `${data.employee_name} - ${data.title}`;

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

        // Employee Visit Bar Chart
        function initEmployeeVisitBarChart() {
            const ctx = document.getElementById('employeeVisitBarChart').getContext('2d');
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: visitEmployees,
                    datasets: [{
                        label: 'Total Visits',
                        data: employeeVisitCounts,
                        backgroundColor: employeeColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 20,
                            right: 60,
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
                                    return `${context.label}: ${context.raw} visits`;
                                }
                            }
                        },
                        datalabels: {
                            display: true,
                            color: '#396A7D',
                            anchor: 'end', // Position at end of bar
                            align: 'right', // Align to right
                            offset: 5, // Small offset from bar
                            formatter: (value) => value,
                            font: {
                                weight: 'bold',
                                size: 10
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                padding: 10
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            ticks: {
                                autoSkip: false,
                                padding: 10
                            }
                        }
                    },
                    onClick: function (event, elements) {
                        if (elements.length) {
                            const index = elements[0].index;
                            const exData = visitEmployees[index];
                            const url = 'get-data/visit';
                            getTableData(exData, null, url);
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        // Employee Activity Bar Chart
        function initEmployeeActivityBarChart() {
            const ctx = document.getElementById('employeeActivityBarChart').getContext('2d');
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: activityEmployees,
                    datasets: [{
                        label: 'Total Activities',
                        data: employeeActivityCounts,
                        backgroundColor: employeeColors,
                        borderWidth: 1,
                        borderRadius: 4 // Match the rounded corners
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 20,
                            right: 60, // Match the right padding
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
                                    return `${context.label}: ${context.raw} activities`; // Updated label
                                }
                            }
                        },
                        datalabels: {
                            display: true,
                            color: '#396A7D',
                            anchor: 'end', // Match positioning
                            align: 'right', // Align to right
                            offset: 5, // Same offset
                            formatter: (value) => value,
                            font: {
                                weight: 'bold',
                                size: 10
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                padding: 10
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            ticks: {
                                autoSkip: false,
                                padding: 10
                            }
                        }
                    },
                    onClick: function (event, elements) {
                        if (elements.length) {
                            const index = elements[0].index;
                            const exData = activityEmployees[index];
                            const url = 'get-data/activity';
                            getTableData(exData, null, url);
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        // Visit Bar Chart
        function initVisitBarChart() {
            const ctx = document.getElementById('visitBarChart').getContext('2d');
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: employeeNames.map((exec, i) => ({
                        label: exec,
                        data: employeeVisitData[i],
                        backgroundColor: employeeColors[i],
                        borderWidth: 1
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            display: false
                        }
                    },
                    onClick: function (event, elements) {
                        if (elements.length) {
                            const index = elements[0].datasetIndex;
                            const exData = employeeNames[index];                            
                            const month = elements[0].index;
                            const url = 'get-data/visit';
                            getTableData(exData, month+1, url);
                        }
                    }
                }
            });
        }

        // Activity Bar Chart
        function initActivityBarChart() {
            const ctx = document.getElementById('activityBarChart').getContext('2d');
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: employeeNames.map((exec, i) => ({
                        label: exec,
                        data: employeeActivityData[i],
                        backgroundColor: employeeColors[i],
                        borderWidth: 1
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 10,
                                font: {
                                    size: 10
                                }
                            }
                        },
                        datalabels: {
                            display: false
                        }
                    },
                    onClick: function (event, elements) {
                        if (elements.length) {
                            const index = elements[0].datasetIndex;
                            const exData = employeeNames[index];  
                            const month = elements[0].index;
                            const url = 'get-data/activity';
                            getTableData(exData, month+1, url);
                        }
                    }
                }
            });
        }

        // Table population functions
        function populateTable(tableId, headers, dataRows) {
            const tableBody = document.querySelector(`#${tableId} tbody`);
            if (!tableBody) return;
            tableBody.innerHTML = "";

            headers.forEach((header, index) => {
                let tableRow = `<tr><td style="padding: 4px 8px;">${header}</td><td style="padding: 4px 8px;">${dataRows[index]}</td></tr>`;
                tableBody.innerHTML += tableRow;
            });
        }

        function populateActivityTable() {
            const tableBody = document.querySelector("#employeeActivityTable tbody");
            if (!tableBody) return;
            tableBody.innerHTML = "";

            months.forEach((month, i) => {
                let row = `<tr><td style="padding: 4px 8px;">${month}</td>`;
                employeeActivityData.forEach(employee => row += `<td style="padding: 4px 8px;">${employee[i]}</td>`);
                row += "</tr>";
                tableBody.innerHTML += row;
            });
        }

        function populateVisitTable() {
            const tableBody = document.querySelector("#employeeVisitTable tbody");
            if (!tableBody) return;
            tableBody.innerHTML = "";

            months.forEach((month, i) => {
                let row = `<tr><td style="padding: 4px 8px;">${month}</td>`;
                employeeVisitData.forEach(employee => row += `<td style="padding: 4px 8px;">${employee[i]}</td>`);
                row += "</tr>";
                tableBody.innerHTML += row;
            });
        }

        // Enhanced Download functions
        async function downloadChart(target) {
            let canvas, legend, container;

            switch (target) {
                case 'patientMarketing':
                    canvas = document.getElementById('patientListGraph');
                    legend = document.getElementById('patientMarketingLegend');
                    container = document.getElementById('patientMarketingGraph');
                    break;
                case 'employeeActivity':
                    canvas = document.getElementById('activityBarChart');
                    legend = document.getElementById('employeeActivityLegend');
                    container = document.getElementById('employeeActivityGraph');
                    break;
                case 'employeePerformance':
                    canvas = document.getElementById('employeeVisitBarChart');
                    legend = document.getElementById('employeePerformanceLegend');
                    container = document.getElementById('employeePerformanceGraph');
                    break;
                case 'employeeVisit':
                    canvas = document.getElementById('visitBarChart');
                    legend = document.getElementById('employeeVisitLegend');
                    container = document.getElementById('employeeVisitGraph');
                    break;
                default:
                    return;
            }

            // Create a temporary container for the screenshot
            const tempContainer = document.createElement('div');
            tempContainer.style.position = 'fixed';
            tempContainer.style.left = '-10000px';
            tempContainer.style.backgroundColor = 'white';
            tempContainer.style.padding = '20px';
            tempContainer.style.borderRadius = '8px';
            tempContainer.style.boxShadow = '0 0 10px rgba(0,0,0,0.1)';

            // Clone elements
            const chartClone = document.createElement('canvas');
            chartClone.width = canvas.width;
            chartClone.height = canvas.height;
            chartClone.getContext('2d').drawImage(canvas, 0, 0);
            tempContainer.appendChild(chartClone);

            if (legend) {
                const legendClone = legend.cloneNode(true);
                legendClone.style.display = 'block';
                legendClone.style.marginTop = '15px';
                legendClone.style.padding = '10px';
                tempContainer.appendChild(legendClone);
            }

            document.body.appendChild(tempContainer);

            try {
                const canvasImage = await html2canvas(tempContainer, {
                    scale: 3,
                    backgroundColor: null,
                    logging: false,
                    useCORS: true
                });

                // Create final canvas with white background
                const finalCanvas = document.createElement('canvas');
                finalCanvas.width = canvasImage.width;
                finalCanvas.height = canvasImage.height;
                const ctx = finalCanvas.getContext('2d');
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, finalCanvas.width, finalCanvas.height);
                ctx.drawImage(canvasImage, 0, 0);

                finalCanvas.toBlob(blob => {
                    saveAs(blob, `${target}_chart.png`);
                }, 'image/png', 1);

            } catch (error) {
                console.error('Download error:', error);
            } finally {
                document.body.removeChild(tempContainer);
            }
        }

        function downloadTable(target) {
            let table;
            switch (target) {
                case 'patientMarketing':
                    table = document.querySelector('#patientMarketingTable table');
                    break;
                case 'employeeActivity':
                    table = document.querySelector('#employeeActivityTable table');
                    break;
                case 'employeePerformance':
                    // For employeePerformance, we have a chart in table view
                    const canvas = document.getElementById('employeeActivityBarChart');
                    const legend = document.getElementById('employeePerformanceTableLegend');

                    // Create a temporary container
                    const tempContainer = document.createElement('div');
                    tempContainer.style.position = 'fixed';
                    tempContainer.style.left = '-10000px';
                    tempContainer.style.top = '0';
                    tempContainer.style.zIndex = '99999';
                    tempContainer.style.backgroundColor = 'white';
                    tempContainer.style.padding = '20px';

                    // Clone the chart
                    const chartClone = document.createElement('canvas');
                    chartClone.width = canvas.width;
                    chartClone.height = canvas.height;
                    chartClone.getContext('2d').drawImage(canvas, 0, 0);
                    tempContainer.appendChild(chartClone);

                    // Clone the legend
                    if (legend) {
                        const legendClone = legend.cloneNode(true);
                        legendClone.style.display = 'block';
                        legendClone.style.marginTop = '10px';
                        tempContainer.appendChild(legendClone);
                    }

                    document.body.appendChild(tempContainer);

                    // Capture and download
                    html2canvas(tempContainer, {
                        scale: 2
                        , backgroundColor: '#ffffff'
                        , logging: false
                        , useCORS: true
                        , allowTaint: true
                    }).then(canvas => {
                        canvas.toBlob(blob => {
                            saveAs(blob, `${target}_chart.png`);
                        }, 'image/png', 1);
                        document.body.removeChild(tempContainer);
                    });
                    return;
                case 'employeeVisit':
                    table = document.querySelector('#employeeVisitTable table');
                    break;
                default:
                    return;
            }

            if (!table) return;

            let csv = [];
            let rows = table.querySelectorAll("tr");

            rows.forEach(row => {
                let cols = row.querySelectorAll("th, td");
                let rowData = [];
                cols.forEach(col => rowData.push(col.innerText.trim()));
                csv.push(rowData.join(","));
            });

            let csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
            let encodedUri = encodeURI(csvContent);
            let link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `${target}_data.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Equal Height Adjustment
        function adjustEqualHeight() {
            const cardGroups = [
                document.querySelectorAll('.row:first-child .equal-height')
                , document.querySelectorAll('.row:last-child .equal-height')
            ];

            cardGroups.forEach(group => {
                let maxHeight = Math.max(...Array.from(group, card => card.offsetHeight));
                group.forEach(card => card.style.height = maxHeight + 'px');
            });
        }

        adjustEqualHeight();
        window.addEventListener('resize', adjustEqualHeight);
    });
</script>
@endsection