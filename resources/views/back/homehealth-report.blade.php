@extends('layouts.back.index')

@section('content')
<style>
    .scroll {
        height: 275px;
        overflow-y: scroll;
    }
    canvas {
        max-width: 100%;
    }
    .bg-dark {
        background-color: #dfdfdf !important;
    }
    .space {
        margin-top: 28px;
    }
</style>

<section class="section">
    <div class="card shadow-sm p-4">
         <form action="{{ route('home-health.report') }}" method="POST">
                @csrf
            <div class="row mt-3">
                <h5>Date</h5>
                <div class="col-md-6">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control border-primary" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-6">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control border-primary" value="{{ request('to_date') }}">
                </div>
                <!-- <div class="col-md-3">
                    <label>Type of Visit</label>
                    <select class="form-select" aria-label="Default select example">
                        <option value="1">All</option>
                        <option value="2">Health Check-Up</option>
                        <option value="3">Consultations</option>
                        <option value="4">In-Patients</option>

                    </select>
                </div> -->
                <div class="col-md-4 mt-4">
                    <button type="submit" class="btn btn-success">
                        Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="card shadow-sm p-4">
        <div class="row mt-5 d-flex justify-content-center">
            <div class="col-md-12 d-flex justify-content-center mb-4">
                <button class="btn btn-primary">
                    <h5 class="text-white mb-0">AJ Patients : {{ @$HomeHealthAJGrouped->count() }}</h5>
                </button>
            </div>

             <div class="row mt-4">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> </h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="HomeHealthAJ" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="HomeHealthAJ" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="HomeHealthAJ">Download</button>
                        </div>
                    </div>
                    <div class="chart-container" id="HomeHealthAJGraph">
                        <canvas id="HomeHealthAJChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="HomeHealthAJLegend"></div>
                    <div class="table-container d-none mt-2" id="HomeHealthAJTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Visit Type</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Visit</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive scroll">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>Type of Visit</th>
                                <th>Date</th>
                                <th>Total Visits</th>
                                <th>Location</th>
                            </tr>
                            @forelse ($HomeHealthAJGrouped as $serviceType => $requests)

                            <!-- <tr>
                                <td rowspan="{{@$requests->count()}}">{{ $serviceType }}</td>
                            </tr>
                            <tr> -->
                            @foreach ($requests as $index=>$row)
                            <tr>
                                @if ($index === 0)
                                <td rowspan="{{@$requests->count()}}">{{ $serviceType }}</td>
                                @endif
                                <td>{{ \Carbon\Carbon::parse($row->booking_date)->format('d/m/Y') }}</td>
                                @if ($index === 0)
                                <td rowspan="{{@$requests->count()}}">{{@$requests->count()}}</td>
                                @endif
                                <td>{{ $row->address }}</td>
                            </tr>

                            @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No data found.</td>
                            </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12 d-flex justify-content-center mb-4">
                    <button class="btn btn-primary">
                        <h5 class="text-white mb-0">Non-AJ Patients : {{ @$HomeHealthNonAJGrouped->count() }}</h5>
                    </button>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-2" style="font-size: 1rem;"> </h5>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-primary toggle-btn active" data-target="HomeHealthNonAJ" name="graph">Graph</button>
                            <button class="btn btn-xs btn-secondary toggle-btn" data-target="HomeHealthNonAJ" name="table">Table</button>
                            <button class="btn btn-xs btn-success download-btn" data-target="HomeHealthNonAJ">Download</button>
                        </div>
                    </div>
                    <div class="chart-container" id="HomeHealthNonAJGraph">
                        <canvas id="HomeHealthNonAJChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2" id="HomeHealthNonAJLegend"></div>
                    <div class="table-container d-none mt-2" id="HomeHealthNonAJTable">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless report-table">
                                <thead style="background-color:#396A7D !important; position: sticky; top: 0;">
                                    <tr>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Visit Type</th>
                                        <th style="background-color:#396A7D !important; color: white; padding: 4px 8px;">Total Visit</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.75rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive scroll">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>Type of Visit</th>
                                <th>Date</th>
                                <th>Total Visits</th>
                                <th>Location</th>
                            </tr>
                            @forelse ($HomeHealthNonAJGrouped as $serviceType => $requests)

                            <!-- <tr>
                                <td rowspan="{{@$requests->count()}}">{{ $serviceType }}</td>
                            </tr>
                            <tr> -->
                            @foreach ($requests as $index=>$row)
                            <tr>
                                @if ($index === 0)
                                <td rowspan="{{@$requests->count()}}">{{ $serviceType }}</td>
                                @endif
                                <td>{{ \Carbon\Carbon::parse($row->booking_date)->format('d/m/Y') }}</td>
                                @if ($index === 0)
                                <td rowspan="{{@$requests->count()}}">{{@$requests->count()}}</td>
                                @endif
                                <td>{{ $row->address }}</td>
                            </tr>

                            @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No data found.</td>
                            </tr>
                            @endforelse
                        </table>
                    </div>
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
            // Register plugins
            Chart.register(ChartDataLabels);

            const graphColors = [
                '#A3D8FF', '#D4A5A5', '#FFABAB', '#4CAF50', '#D8BFD8', '#C7CEEA', '#FFDDC1', '#A8E6CF', '#FFD3B6', '#74B9FF', '#FF9AA2', '#B5EAD7', '#FEC3A6', '#85C1E9', '#F9EBEA'
            ];

            const HomeHealthAJReport = @json(@$HomeHealthAJChartData);
            const HomeHealthNonAJReport = @json(@$HomeHealthNonAJChartData);

            const HomeHealthAJChart = initBarChart('HomeHealthAJChart', HomeHealthAJReport, [
                'rgba(255, 99, 132, 0.6)', // Red
                'rgba(54, 162, 235, 0.6)', // Blue
                'rgba(255, 206, 86, 0.6)', // Yellow
                'rgba(75, 192, 192, 0.6)', // Green
                'rgba(153, 102, 255, 0.6)', // Purple
                'rgba(255, 159, 64, 0.6)' // Orange
            ], 'HomeHealthAJLegend', '');
            const HomeHealthNonAJChart = initBarChart('HomeHealthNonAJChart', HomeHealthNonAJReport, [
                'rgba(255, 99, 132, 0.6)', // Red
                'rgba(54, 162, 235, 0.6)', // Blue
                'rgba(255, 206, 86, 0.6)', // Yellow
                'rgba(75, 192, 192, 0.6)', // Green
                'rgba(153, 102, 255, 0.6)', // Purple
                'rgba(255, 159, 64, 0.6)' // Orange
            ], 'HomeHealthNonAJLegend', '');
          
            // const paymentTypeChart = initDoughnutChart('paymentTypeChart', @json(@$paymentWiseReport), graphColors.slice(1, 5), 'paymentWiseLegend', 'Kms');
            // const areaWiseChart = initHorizontalBarChart('areaWiseChart', @json(@$areaWiseReport), graphColors[7], 'areaWiseLegend');

            // Initialize tables with data
            // initTables();

            function initBarChart(chartId, data, color, legendId, measure) {
                const ctx = document.getElementById(chartId).getContext('2d');
                const labels = Object.keys(data);
                const values = Object.values(data);

                // createLegend(legendId, labels, values, color, measure);

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
                            //     padding: {
                            //         left: 15,
                            //         right: 15,
                            //         top: 15,
                            //         bottom: 15
                            //     }
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
                                title: {
                                    display: true,
                                    text: 'Total Visits',
                                    color: '#666',
                                    font: {
                                        size: 14,
                                        weight: 'bold'
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Type of Visit',
                                    color: '#666',
                                    font: {
                                        size: 14,
                                        weight: 'bold'
                                    }
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
                                    case 'HomeHealthAJChart':
                                        type = 'driver';
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
                    case 'HomeHealthAJ':
                        chartId = 'HomeHealthAJChart';
                        break;
                    case 'HomeHealthNonAJ':
                        chartId = 'HomeHealthNonAJChart';
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
                    case 'HomeHealthAJ':
                        table = document.querySelector('#HomeHealthAJTable table');
                        break;
                    case 'HomeHealthNonAJ':
                        table = document.querySelector('#HomeHealthNonAJTable table');
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
                    'HomeHealthAJ': HomeHealthAJReport,
                    'HomeHealthNonAJ': HomeHealthNonAJReport
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

@endsection