@extends('shared.dashboard')

@section('title', 'Target Insects Analytics')

@push('styles')
<link href="{{ asset('backend/assets/plugins/apexcharts-bundle/css/apexcharts.css') }}" rel="stylesheet">
<style>
    .analytics-card {
        transition: all 0.3s;
    }
    .analytics-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .chart-container {
        min-height: 300px;
    }
    .top-insects-list .list-group-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .insect-badge {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Target Insects Analytics Dashboard</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-1"></i>
                        This dashboard shows analytics for target insects across all contracts and visits.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Monthly Insect Trends (Past 12 Months)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div id="monthlyTrendsChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Top Target Insects</h6>
                </div>
                <div class="card-body">
                    <div class="top-insects-list">
                        <ul class="list-group">
                            @forelse($insectStats as $index => $stat)
                                @if($index < 5)
                                <li class="list-group-item">
                                    <span>
                                        <i class="bx bx-bug me-1"></i>
                                        {{ $stat['name'] }}
                                    </span>
                                    <span>
                                        <span class="badge bg-primary insect-badge">{{ $stat['count'] }} reports</span>
                                        <span class="badge bg-info insect-badge">{{ $stat['percentage'] }}%</span>
                                    </span>
                                </li>
                                @endif
                            @empty
                                <li class="list-group-item text-center">No data available</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Insect Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div id="insectDistributionChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Monthly Trends Chart
        const monthlyTrendsData = @json($monthlyTrends);
        const trendsSeries = [];
        
        // Limit to top 5 insects for readability
        let insectCount = 0;
        for (const [key, value] of Object.entries(monthlyTrendsData)) {
            if (insectCount < 5) {
                const seriesData = value.data.map(item => item.count);
                trendsSeries.push({
                    name: value.name,
                    data: seriesData
                });
                insectCount++;
            }
        }
        
        const monthLabels = monthlyTrendsData[Object.keys(monthlyTrendsData)[0]]?.data.map(item => item.month) || [];
        
        const trendsOptions = {
            series: trendsSeries,
            chart: {
                type: 'line',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            colors: ['#3461ff', '#12bf24', '#ff6632', '#8932ff', '#ffcb32'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            grid: {
                borderColor: '#f1f1f1',
            },
            xaxis: {
                categories: monthLabels
            },
            yaxis: {
                title: {
                    text: 'Number of Reports'
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + " reports";
                    }
                }
            },
            legend: {
                position: 'top'
            }
        };
        
        if (trendsSeries.length > 0) {
            const trendsChart = new ApexCharts(document.querySelector("#monthlyTrendsChart"), trendsOptions);
            trendsChart.render();
        } else {
            document.querySelector("#monthlyTrendsChart").innerHTML = '<div class="text-center py-5">No trend data available</div>';
        }
        
        // Insect Distribution Chart
        const insectStats = @json($insectStats);
        const distributionLabels = insectStats.map(stat => stat.name);
        const distributionData = insectStats.map(stat => stat.count);
        
        const distributionOptions = {
            series: distributionData,
            chart: {
                type: 'pie',
                height: 350
            },
            labels: distributionLabels,
            colors: ['#3461ff', '#12bf24', '#ff6632', '#8932ff', '#ffcb32', '#ff3e1d', '#299cdb', '#6c757d', '#0dcaf0', '#fd7e14'],
            legend: {
                position: 'bottom'
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 360
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + " reports";
                    }
                }
            }
        };
        
        if (distributionData.length > 0) {
            const distributionChart = new ApexCharts(document.querySelector("#insectDistributionChart"), distributionOptions);
            distributionChart.render();
        } else {
            document.querySelector("#insectDistributionChart").innerHTML = '<div class="text-center py-5">No distribution data available</div>';
        }
    });
</script>
@endpush
