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
        <form action="{{ route('reports.ambulance') }}" method="POST">
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
                <h5 class="text-white p-2 bg-secondary text-center">Total Ambulance Request</h5>
            </div>
            <div class="col-md-12">
                <div class="row d-flex justify-content-center">
                    <div class="col mb-4">
                        <div class="box shadow" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                            <h5 class="text-secondary">Help Desk</h5>
                            <p id="totalTrips" class="h3 fw-bold" style="color: #0082A3;">{{@$ambulanceTypeChartData['Helpdesk'] ?? 0}}</p>
                        </div>
                    </div>
                    <div class="col mb-4">
                        <div class="box shadow" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                            <h5 class="text-secondary">Ward</h5>
                            <p id="totalKms" class="h3 fw-bold" style="color: #0082A3;">{{@$ambulanceTypeChartData['Ward'] ?? 0}} </p>
                        </div>
                    </div>
                    <div class="col mb-4">
                        <div class="box shadow" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                            <h5 class="text-secondary">ICU</h5>
                            <p id="totalHours" class="h3 fw-bold" style="color: #0082A3;">{{@$ambulanceTypeChartData['ICU'] ?? 0}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-3">
                <h5 class="text-white p-2 bg-secondary text-center">Total Booked</h5>
            </div>
            <div class="col-md-12">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-3 mb-4">
                        <div class="box shadow" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                            <h5 class="text-secondary">Inhouse Ambulance</h5>
                            <p id="totalTrips" class="h3 fw-bold" style="color: #0082A3;">{{array_sum(@$ambulanceIntChartData)}}</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="box shadow" style="background: linear-gradient(135deg, #e9f7fc, #d1eef9);">
                            <h5 class="text-secondary">External Ambulance</h5>
                            <p id="totalKms" class="h3 fw-bold" style="color: #0082A3;">{{array_sum(@$ambulanceExtChartData)}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <h5 class="text-white p-2 bg-secondary text-center">Ambulance Wise Report</h5>
            </div>
            <div class="col-md-6 mt-3">

                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> External Ambulance Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="ambulanceExt" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="ambulanceExt" name="table">Table</button>
                            <button class="btn btn-xs btn-info download-btn" data-target="ambulanceExt">Download</button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
						<div class="chart-wrapper">
                        <div class="chart-container" id="ambulanceExtGraph">
                            <canvas id="ambulanceExtChart"></canvas>
                        </div>
				
                        <div class="legend align-items-center mt-3" id="ambulanceExtLegend"></div></div>
                        <div class="table-container d-none mt-2" id="ambulanceExtTable">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless report-table">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Ambulance Type</th>
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

            <div class="col-md-6  mt-3">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Internal Ambulance Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="ambulanceInt" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="ambulanceInt" name="table">Table</button>
                            <button class="btn btn-xs btn-info download-btn" data-target="ambulanceInt">Download</button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
						<div class="chart-wrapper">
                        <div class="chart-container" id="ambulanceIntGraph">
                            <canvas id="ambulanceIntChart"></canvas>
                        </div>
                        <div class="legend align-items-center mt-3" id="ambulanceIntLegend"></div></div>
                        <div class="table-container d-none mt-2" id="ambulanceIntTable">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless report-table">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Ambulance Type</th>
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
            <div class="col-md-12 my-3">
                <h5 class="text-white p-2 bg-secondary text-center">Request Wise Report</h5>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="ambulanceType" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="ambulanceType" name="table">Table</button>
                            <button class="btn btn-xs btn-info download-btn" data-target="ambulanceType">Download</button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
						<div class="chart-wrapper">
                        <div class="chart-container" id="ambulanceTypeGraph">
                            <canvas id="ambulanceTypeChart"></canvas>
                        </div>
                        <div class="legend align-items-center mt-3" id="ambulanceTypeLegend"></div></div>
                        <div class="table-container d-none mt-2" id="ambulanceTypeTable">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless report-table">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Ambulance Type</th>
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

            <div class="modal fade" id="tableDataModal" tabindex="-1" aria-labelledby="tableDataModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="tableDataModalLabel">Ambulance Data</h5>
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

            <!-- <div class="col-md-12 my-3">
                <h5 class="text-white p-2 bg-secondary text-center"></h5>
            </div>
            <div class="col-md-6 col-lg-6 mb-3">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Help Desk Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="ambulanceHelpDesk" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="ambulanceHelpDesk" name="table">Table</button>
                            <button class="btn btn-xs btn-info download-btn" data-target="ambulanceHelpDesk">Download</button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
						<div class="chart-wrapper">

                        <div class="chart-container" id="ambulanceHelpDeskGraph">
                            <canvas id="ambulanceHelpDeskChart"></canvas>
                        </div>
                        <div class="legend align-items-center mt-3" id="ambulanceHelpDeskLegend"></div></div>
                        <div class="table-container d-none mt-2" id="ambulanceHelpDeskTable">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless report-table">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Ambulance Type</th>
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
            <div class="col-md-6 col-lg-6 mb-3">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> Ward Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="ambulanceWard" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="ambulanceWard" name="table">Table</button>
                            <button class="btn btn-xs btn-info download-btn" data-target="ambulanceWard">Download</button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
						<div class="chart-wrapper">
                        <div class="chart-container" id="ambulanceWardGraph">
                            <canvas id="ambulanceWardChart"></canvas>
                        </div>
                        <div class="legend align-items-center mt-3" id="ambulanceWardLegend"></div></div>
                        <div class="table-container d-none mt-2" id="ambulanceWardTable">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless report-table">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Ambulance Type</th>
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
            <div class="col-md-6 col-lg-6">
                <div class="cust-card box shadow p-2 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> ICU Report</h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="ambulanceICU" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="ambulanceICU" name="table">Table</button>
                            <button class="btn btn-xs btn-info download-btn" data-target="ambulanceICU">Download</button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
						<div class="chart-wrapper">
                        <div class="chart-container" id="ambulanceICUGraph">
                            <canvas id="ambulanceICUChart"></canvas>
                        </div>
                        <div class="legend align-items-center mt-3" id="ambulanceICULegend"></div></div>
                        <div class="table-container d-none mt-2" id="ambulanceICUTable">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless report-table">
                                    <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                        <tr>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Ambulance Type</th>
                                            <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Trip</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 0.75rem;"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
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
            const chartConfigs = [
        {
            chartId: 'ambulanceHelpDeskChart',
            labels: ['Red', 'Blue', 'Yellow', 'Green'],
            colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
        },
        {
            chartId: 'ambulanceWardChart',
            labels: ['Help Desk', 'Ward', 'ICU'],
            colors: ['#FFA500', '#008080', '#800080', '#A9A9A9']
        },
        {
            chartId: 'ambulanceICUChart',
            labels: ['Help Desk', 'Ward', 'ICU'],
            colors: ['#F94144', '#F3722C', '#90BE6D', '#577590']
        },
        {
            chartId: 'ambulanceExtChart',
            labels: ['Internal Ambulance', 'External Ambulance'],
            colors: ['#00B894', '#0984E3', '#6C5CE7']
        },
        {
            chartId: 'ambulanceIntChart',
            labels: ['Internal Ambulance', 'External Ambulance'],
            colors: ['#D63031', '#E17055', '#00CEC9']
        },
        {
            chartId: 'ambulanceTypeChart',
            labels: ['Internal Ambulance', 'External Ambulance'],
            colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
        }
    ];

    const ambulanceHelpDeskReport = @json(@$ambulanceHelpDeskChartData);
    const ambulanceExtReport = @json(@$ambulanceExtChartData);
    const ambulanceIntReport = @json(@$ambulanceIntChartData);
    const ambulanceWardReport = @json(@$ambulanceWardChartData);
    const ambulanceICUReport = @json(@$ambulanceICUChartData);
    const ambulanceTypeReport = @json(@$ambulanceTypeChartData);

    // const ambulanceHelpDeskChart = initBarChart('ambulanceHelpDeskChart', ambulanceHelpDeskReport, 'ambulanceHelpDeskLegend', 'In-house');
    // const ambulanceWardChart = initBarChart('ambulanceWardChart', ambulanceWardReport, 'ambulanceWardLegend', 'In-house');
    // const ambulanceICUChart = initBarChart('ambulanceICUChart', ambulanceICUReport, 'ambulanceICULegend', 'Internal');
    const ambulanceExtChart = initBarChart('ambulanceExtChart', ambulanceExtReport, 'ambulanceExtLegend', 'External');
    const ambulanceIntChart = initBarChart('ambulanceIntChart', ambulanceIntReport, 'ambulanceIntLegend', 'Internal');
    const ambulanceTypeChart = initBarChart('ambulanceTypeChart', ambulanceTypeReport, 'ambulanceTypeLegend', 'Type');

    initTables();

    function initBarChart(chartId, data, legendId, measure) {
        const ctx = document.getElementById(chartId).getContext('2d');
        const labels = Object.keys(data);
        const values = Object.values(data);

        const config = chartConfigs.find(cfg => cfg.chartId === chartId);
        const backgroundColors = config ? config.colors : labels.map(() => 'rgba(200,200,200,0.5)');

        createLegend(legendId, labels, values, backgroundColors, measure);

        return new Chart(ctx, {
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
                                const url = `get-ambulance-data/${measure}`;
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
                        valueSpan.textContent = `${values[i]}`;

                        item.appendChild(colorBox);
                        item.appendChild(labelSpan);
                        item.appendChild(valueSpan);

                        legendContainer.appendChild(item);
                });
            }

            function initTables() {
                const reports = {
                    'ambulanceHelpDesk': ambulanceHelpDeskReport,
                    'ambulanceExt': ambulanceExtReport,
                    'ambulanceInt': ambulanceIntReport,
                    'ambulanceType': ambulanceTypeReport,
                    'ambulanceWard': ambulanceWardReport,
                    'ambulanceICU': ambulanceICUReport
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
                    case 'ambulanceHelpDesk':
                        chartId = 'ambulanceHelpDeskChart';
                        break;
                    case 'ambulanceExt':
                        chartId = 'ambulanceExtChart';
                        break;
                    case 'ambulanceInt':
                        chartId = 'ambulanceIntChart';
                        break;
                    case 'ambulanceType':
                        chartId = 'ambulanceTypeChart';
                        break;
                    case 'ambulanceWard':
                        chartId = 'ambulanceWardChart';
                        break;
                    case 'ambulanceICU':
                        chartId = 'ambulanceICUChart';
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
                    case 'ambulanceHelpDesk':
                        table = document.querySelector('#ambulanceHelpDeskTable table');
                        break;
                    case 'ambulanceExt':
                        table = document.querySelector('#ambulanceExtTable table');
                        break;
                    case 'ambulanceInt':
                        table = document.querySelector('#ambulanceIntTable table');
                        break;
                    case 'ambulanceType':
                        table = document.querySelector('#ambulanceTypeTable table');
                        break;
                    case 'ambulanceWard':
                        table = document.querySelector('#ambulanceWardTable table');
                        break;
                    case 'ambulanceICU':
                        table = document.querySelector('#ambulanceICUTable table');
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