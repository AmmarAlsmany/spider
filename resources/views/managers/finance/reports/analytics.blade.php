@extends('shared.dashboard')
@section('content')
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
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
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
                                    <div class="col-md-3 text-center">
                                        <h4 class="text-{{ $colors[$status] ?? 'primary' }}">{{ $count }}</h4>
                                        <p class="mb-0">{{ ucfirst($status) }}</p>
                                        <div class="progress mt-2" style="height: 5px;">
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
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border card radius-10">
                        <div class="card-body">
                            <h6 class="mb-3">Cash Flow Projection</h6>
                            <div id="cashFlowProjectionChart" style="height: 300px;"></div>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Revenue Breakdown Chart
        var monthlyData = @json($analytics['monthly_breakdown']);
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
        
        // Cash Flow Projection Chart
        var cashFlowData = @json($analytics['cash_flow_projection']);
        var cashFlowMonths = cashFlowData.map(item => item.month);
        var expectedInflow = cashFlowData.map(item => item.expected_inflow);
        
        var cashFlowOptions = {
            series: [{
                name: 'Expected Inflow',
                data: expectedInflow
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
            colors: ['#0d6efd']
        };
        
        var cashFlowChart = new ApexCharts(document.querySelector("#cashFlowProjectionChart"), cashFlowOptions);
        cashFlowChart.render();
        
        // Aging Analysis Chart
        var agingData = @json($analytics['aging_analysis']);
        
        var agingOptions = {
            series: [
                agingData.current,
                agingData['1_30'],
                agingData['31_60'],
                agingData['61_90'],
                agingData.over_90
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
    });
</script>
@endsection
