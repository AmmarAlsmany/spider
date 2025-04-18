@extends('shared.dashboard')
@section('content')
<script>
    function printAnalyticsReport() {
        // Create an iframe element
        var printFrame = document.createElement('iframe');
        
        // Make it invisible
        printFrame.style.position = 'fixed';
        printFrame.style.right = '0';
        printFrame.style.bottom = '0';
        printFrame.style.width = '0';
        printFrame.style.height = '0';
        printFrame.style.border = '0';
        
        document.body.appendChild(printFrame);
        
        // Get the iframe document
        var frameDoc = printFrame.contentWindow || printFrame.contentDocument.document || printFrame.contentDocument;
        
        // Write the HTML content to the iframe
        frameDoc.document.open();
        frameDoc.document.write('<html><head><title>Financial Analytics Report</title>');
        frameDoc.document.write('<link rel="stylesheet" href="{{ asset("assets/css/bootstrap.min.css") }}" type="text/css" />');
        frameDoc.document.write('<style>body { padding: 20px; } .actions-column { display: none; }</style>');
        frameDoc.document.write('</head><body>');
        frameDoc.document.write('<h3 class="mb-4 text-center">Advanced Financial Analytics</h3>');
        
        // Get the content we want to print - all reports and charts
        var kpiSection = document.querySelector('.card-body .row:nth-child(2)').outerHTML;
        var chartsRow = document.querySelector('.card-body .row:nth-child(3)').outerHTML;
        var agingAnalysis = document.querySelector('.card-body .card.radius-10:last-child').outerHTML;
        
        // Add to iframe document
        frameDoc.document.write('<div class="container">');
        frameDoc.document.write(kpiSection);
        frameDoc.document.write(chartsRow);
        frameDoc.document.write(agingAnalysis);
        frameDoc.document.write('</div>');
        
        frameDoc.document.write('</body></html>');
        frameDoc.document.close();
        
        // Use setTimeout to ensure the content is loaded before printing
        setTimeout(function () {
            frameDoc.focus();
            frameDoc.print();
            
            // Remove the iframe after printing
            setTimeout(function() {
                document.body.removeChild(printFrame);
            }, 1000);
        }, 500);
    }
</script>
<div class="page-content">
    @if(session('error'))
    <div class="mb-3 alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bx bx-error-circle me-1"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-3 alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-1"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Advanced Financial Analytics</h6>
                </div>
                <div class="ms-auto">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printAnalyticsReport()">
                        <i class='bx bx-printer'></i> Print Report
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Date Range Filter -->
            <div class="mb-4">
                <form action="{{ route('finance.reports.analytics') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div class="gap-2 d-flex">
                            <button type="submit" class="btn btn-primary flex-grow-1">Update Analytics</button>
                            <a href="{{ route('finance.reports.analytics') }}" class="btn btn-outline-secondary">
                                <i class='bx bx-reset'></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Key Performance Indicators -->
            <div class="mb-4 row">
                <div class="col-md-4">
                    <div class="border card radius-10 bg-light-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Collection Rate</p>
                                    <h4 class="my-1 text-success">{{ number_format($analytics['collection_rate'], 2) }}%</h4>
                                    <p class="mb-0 font-13">For selected period</p>
                                </div>
                                <div class="text-success ms-auto font-35">
                                    <i class='bx bx-line-chart'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="border card radius-10">
                        <div class="card-body">
                            <h6 class="mb-3">Payment Status Distribution</h6>
                            <div class="row">
                                @php
                                    $statuses = $analytics['payment_status_distribution'];
                                    $total = array_sum($statuses);
                                    $colors = [
                                        'paid' => 'success',
                                        'pending' => 'warning',
                                        'unpaid' => 'secondary',
                                        'overdue' => 'danger'
                                    ];
                                @endphp
                                
                                @foreach($statuses as $status => $count)
                                    <div class="text-center col-md-3">
                                        <h4 class="text-{{ $colors[$status] ?? 'primary' }}">{{ $count }}</h4>
                                        <p class="mb-0">{{ ucfirst($status) }}</p>
                                        <div class="mt-2 progress" style="height: 5px;">
                                            <div class="progress-bar bg-{{ $colors[$status] ?? 'primary' }}" role="progressbar" 
                                                style="width: {{ $total > 0 ? ($count / $total * 100) : 0 }}%" 
                                                aria-valuenow="{{ $total > 0 ? ($count / $total * 100) : 0 }}" 
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="mb-4 row">
                <div class="col-md-6">
                    <div class="border card radius-10">
                        <div class="card-body">
                            <h6 class="mb-3">Monthly Revenue Breakdown</h6>
                            <div id="monthlyRevenueChart" style="height: 300px;"></div>
                            <div id="monthlyRevenueChartFallback" class="d-none">
                                <div class="alert alert-warning">
                                    <i class="bx bx-info-circle me-1"></i>
                                    No data available for the selected period. Try adjusting your date range.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border card radius-10">
                        <div class="card-body">
                            <h6 class="mb-3">Cash Flow Projection</h6>
                            <div id="cashFlowProjectionChart" style="height: 300px;"></div>
                            <div id="cashFlowProjectionChartFallback" class="d-none">
                                <div class="alert alert-warning">
                                    <i class="bx bx-info-circle me-1"></i>
                                    No cash flow projection data available. Try adjusting your date range.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aging Analysis -->
            <div class="card radius-10">
                <div class="card-header">
                    <h6 class="mb-0">Aging Analysis</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div id="agingAnalysisChart" style="height: 300px;"></div>
                        </div>
                        <div class="col-md-5">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Aging Bucket</th>
                                            <th>Amount (SAR)</th>
                                            <th>% of Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $aging = $analytics['aging_analysis'];
                                            $totalAging = array_sum($aging);
                                        @endphp
                                        <tr>
                                            <td>Current</td>
                                            <td>{{ number_format($aging['current'], 2) }}</td>
                                            <td>{{ $totalAging > 0 ? number_format(($aging['current'] / $totalAging) * 100, 1) : 0 }}%</td>
                                        </tr>
                                        <tr>
                                            <td>1-30 Days</td>
                                            <td>{{ number_format($aging['1_30'], 2) }}</td>
                                            <td>{{ $totalAging > 0 ? number_format(($aging['1_30'] / $totalAging) * 100, 1) : 0 }}%</td>
                                        </tr>
                                        <tr>
                                            <td>31-60 Days</td>
                                            <td>{{ number_format($aging['31_60'], 2) }}</td>
                                            <td>{{ $totalAging > 0 ? number_format(($aging['31_60'] / $totalAging) * 100, 1) : 0 }}%</td>
                                        </tr>
                                        <tr>
                                            <td>61-90 Days</td>
                                            <td>{{ number_format($aging['61_90'], 2) }}</td>
                                            <td>{{ $totalAging > 0 ? number_format(($aging['61_90'] / $totalAging) * 100, 1) : 0 }}%</td>
                                        </tr>
                                        <tr>
                                            <td>Over 90 Days</td>
                                            <td>{{ number_format($aging['over_90'], 2) }}</td>
                                            <td>{{ $totalAging > 0 ? number_format(($aging['over_90'] / $totalAging) * 100, 1) : 0 }}%</td>
                                        </tr>
                                        <tr class="table-active">
                                            <td><strong>Total</strong></td>
                                            <td><strong>{{ number_format($totalAging, 2) }}</strong></td>
                                            <td>100%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Debug data to console
        console.log('Monthly Data:', @json($analytics['monthly_breakdown']));
        console.log('Cash Flow Data:', @json($analytics['cash_flow_projection']));
        console.log('Aging Data:', @json($analytics['aging_analysis']));
        
        try {
            // Monthly Revenue Breakdown Chart
            var monthlyData = @json($analytics['monthly_breakdown'] ?? []);
            if (monthlyData && monthlyData.length > 0) {
                var months = monthlyData.map(item => item.month);
                var revenue = monthlyData.map(item => item.revenue);
                var pending = monthlyData.map(item => item.pending);
                var overdue = monthlyData.map(item => item.overdue);
                
                var monthlyRevenueOptions = {
                    series: [{
                        name: 'Revenue',
                        data: revenue
                    }, {
                        name: 'Pending',
                        data: pending
                    }, {
                        name: 'Overdue',
                        data: overdue
                    }],
                    chart: {
                        type: 'bar',
                        height: 300,
                        stacked: true,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            endingShape: 'rounded'
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: months,
                    },
                    yaxis: {
                        title: {
                            text: 'Amount (SAR)'
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val.toLocaleString() + " SAR"
                            }
                        }
                    },
                    colors: ['#20c997', '#ffc107', '#dc3545']
                };
                
                var monthlyRevenueChart = new ApexCharts(document.querySelector("#monthlyRevenueChart"), monthlyRevenueOptions);
                monthlyRevenueChart.render();
                console.log('Monthly Revenue chart rendered');
                document.querySelector("#monthlyRevenueChartFallback").classList.add('d-none');
            } else {
                document.querySelector("#monthlyRevenueChart").innerHTML = '';
                document.querySelector("#monthlyRevenueChartFallback").classList.remove('d-none');
            }
            
            // Cash Flow Projection Chart
            var cashFlowData = @json($analytics['cash_flow_projection'] ?? []);
            if (cashFlowData && cashFlowData.length > 0) {
                var cashFlowMonths = cashFlowData.map(item => item.month);
                var expectedInflow = cashFlowData.map(item => item.expected_inflow);
                var actualInflow = cashFlowData.map(item => item.actual_inflow || 0);
                
                var cashFlowOptions = {
                    series: [{
                        name: 'Expected Inflow',
                        data: expectedInflow
                    }, {
                        name: 'Actual Inflow',
                        data: actualInflow
                    }],
                    chart: {
                        type: 'area',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    xaxis: {
                        categories: cashFlowMonths
                    },
                    yaxis: {
                        title: {
                            text: 'Amount (SAR)'
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val.toLocaleString() + " SAR"
                            }
                        }
                    },
                    colors: ['#0d6efd', '#20c997']
                };
                
                var cashFlowChart = new ApexCharts(document.querySelector("#cashFlowProjectionChart"), cashFlowOptions);
                cashFlowChart.render();
                console.log('Cash Flow chart rendered');
                document.querySelector("#cashFlowProjectionChartFallback").classList.add('d-none');
            } else {
                document.querySelector("#cashFlowProjectionChart").innerHTML = '';
                document.querySelector("#cashFlowProjectionChartFallback").classList.remove('d-none');
            }
            
            // Aging Analysis Chart
            var agingData = @json($analytics['aging_analysis'] ?? []);
            if (agingData && Object.keys(agingData).length > 0) {
                var agingOptions = {
                    series: [
                        agingData.current || 0,
                        agingData['1_30'] || 0,
                        agingData['31_60'] || 0,
                        agingData['61_90'] || 0,
                        agingData.over_90 || 0
                    ],
                    chart: {
                        type: 'donut',
                        height: 300
                    },
                    labels: ['Current', '1-30 Days', '31-60 Days', '61-90 Days', 'Over 90 Days'],
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }],
                    colors: ['#20c997', '#0dcaf0', '#ffc107', '#fd7e14', '#dc3545']
                };
                
                var agingChart = new ApexCharts(document.querySelector("#agingAnalysisChart"), agingOptions);
                agingChart.render();
                console.log('Aging Analysis chart rendered');
            } else {
                document.querySelector("#agingAnalysisChart").innerHTML = '<div class="alert alert-info">No aging analysis data available.</div>';
            }
        } catch (error) {
            console.error('Error rendering charts:', error);
            document.querySelector("#monthlyRevenueChart").innerHTML = '<div class="alert alert-danger">Error rendering chart: ' + error.message + '</div>';
            document.querySelector("#cashFlowProjectionChart").innerHTML = '<div class="alert alert-danger">Error rendering chart: ' + error.message + '</div>';
            document.querySelector("#agingAnalysisChart").innerHTML = '<div class="alert alert-danger">Error rendering chart: ' + error.message + '</div>';
        }
    });
</script>
@endpush
