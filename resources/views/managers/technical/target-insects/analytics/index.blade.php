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
    .stats-card {
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        transition: all 0.3s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .stats-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .tab-pane {
        padding-top: 1rem;
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

    <!-- Summary Stats Cards -->
    <div class="mt-4 row">
        @php
            $totalReports = 0;
            $totalInsects = 0;
            $totalQuantity = 0;
            
            foreach ($insectStats as $stat) {
                $totalReports += $stat['count'];
                $totalQuantity += $stat['quantity'];
            }
            $totalInsects = count($insectStats);
            
            $avgPerReport = $totalReports > 0 ? round($totalQuantity / $totalReports, 1) : 0;
        @endphp
        
        <div class="col-12 col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-opacity-10 stats-icon bg-primary text-primary">
                            <i class="bx bx-bug fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Total Insect Types</h6>
                            <h3 class="mb-0">{{ $totalInsects }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-opacity-10 stats-icon bg-success text-success">
                            <i class="bx bx-file fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Total Insect Reports</h6>
                            <h3 class="mb-0">{{ $totalReports }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-opacity-10 stats-icon bg-danger text-danger">
                            <i class="bx bx-bug-alt fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Total Insects Found</h6>
                            <h3 class="mb-0">{{ $totalQuantity }}</h3>
                            <small class="text-muted">{{ $avgPerReport }} avg. per report</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="occurrences-tab" data-bs-toggle="tab" data-bs-target="#occurrences" type="button" role="tab" aria-controls="occurrences" aria-selected="true">Insect Occurrences</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="quantities-tab" data-bs-toggle="tab" data-bs-target="#quantities" type="button" role="tab" aria-controls="quantities" aria-selected="false">Insect Quantities</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="occurrences" role="tabpanel" aria-labelledby="occurrences-tab">
                            <div class="chart-container">
                                <div id="monthlyTrendsChart"></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="quantities" role="tabpanel" aria-labelledby="quantities-tab">
                            <div class="chart-container">
                                <div id="monthlyQuantitiesChart"></div>
                            </div>
                        </div>
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
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="mb-1 badge bg-primary insect-badge">{{ $stat['count'] }} reports</span>
                                        <span class="mb-1 badge bg-success insect-badge">{{ $stat['quantity'] }} insects found</span>
                                        <span class="badge bg-info insect-badge">{{ $stat['percentage'] }}% of reports</span>
                                    </div>
                                </li>
                                @endif
                            @empty
                                <li class="text-center list-group-item">No data available</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 row">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Insect Report Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div id="insectDistributionChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Insect Quantity Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div id="insectQuantityChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4 row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Insect Density Analysis</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Insect Type</th>
                                    <th>Reports</th>
                                    <th>Total Found</th>
                                    <th>Avg. per Report</th>
                                    <th>% of Reports</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($insectStats as $stat)
                                <tr>
                                    <td><i class="bx bx-bug me-1"></i> {{ $stat['name'] }}</td>
                                    <td>{{ $stat['count'] }}</td>
                                    <td>{{ $stat['quantity'] }}</td>
                                    <td>{{ $stat['avg_per_report'] }}</td>
                                    <td>{{ $stat['percentage'] }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
        const quantitySeries = [];
        const currentYear = new Date().getFullYear(); // Current year
        
        // Filter data for the current year and get month labels
        let filteredMonthLabels = [];
        let firstInsectKey = Object.keys(monthlyTrendsData)[0];
        
        if (firstInsectKey && monthlyTrendsData[firstInsectKey]?.data) {
            // Extract year from month strings (format: "Jan 2025")
            filteredMonthLabels = monthlyTrendsData[firstInsectKey].data
                .filter(item => {
                    const year = parseInt(item.month.split(' ')[1]);
                    return year === currentYear;
                })
                .map(item => item.month);
        }
        
        // Limit to top 5 insects for readability
        let insectCount = 0;
        for (const [key, value] of Object.entries(monthlyTrendsData)) {
            if (insectCount < 5) {
                // Filter data for current year
                const currentYearData = value.data.filter(item => {
                    const year = parseInt(item.month.split(' ')[1]);
                    return year === currentYear;
                });
                
                const currentYearQuantityData = value.quantity_data.filter(item => {
                    const year = parseInt(item.month.split(' ')[1]);
                    return year === currentYear;
                });
                
                const seriesData = currentYearData.map(item => item.count);
                trendsSeries.push({
                    name: value.name,
                    data: seriesData
                });
                
                const quantityData = currentYearQuantityData.map(item => item.count);
                quantitySeries.push({
                    name: value.name,
                    data: quantityData
                });
                
                insectCount++;
            }
        }
        
        const monthLabels = filteredMonthLabels;
        
        const trendsOptions = {
            series: trendsSeries,
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                },
                dropShadow: {
                    enabled: true,
                    opacity: 0.3,
                    blur: 5,
                    left: -7,
                    top: 22
                }
            },
            title: {
                text: 'Insect Trends for 2025',
                align: 'left',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold'
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
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100]
                }
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
        
        const quantitiesOptions = {
            series: quantitySeries,
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                },
                dropShadow: {
                    enabled: true,
                    opacity: 0.3,
                    blur: 5,
                    left: -7,
                    top: 22
                }
            },
            title: {
                text: 'Insect Quantities for 2025',
                align: 'left',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold'
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
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100]
                }
            },
            grid: {
                borderColor: '#f1f1f1',
            },
            xaxis: {
                categories: monthLabels
            },
            yaxis: {
                title: {
                    text: 'Number of Insects Found'
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + " insects";
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
            
            const quantitiesChart = new ApexCharts(document.querySelector("#monthlyQuantitiesChart"), quantitiesOptions);
            quantitiesChart.render();
        } else {
            document.querySelector("#monthlyTrendsChart").innerHTML = '<div class="py-5 text-center">No trend data available</div>';
            document.querySelector("#monthlyQuantitiesChart").innerHTML = '<div class="py-5 text-center">No quantity data available</div>';
        }
        
        // Insect Distribution Chart
        const insectStats = @json($insectStats);
        const distributionLabels = insectStats.map(stat => stat.name);
        const distributionData = insectStats.map(stat => stat.count);
        const quantityData = insectStats.map(stat => stat.quantity);
        
        console.log('Insect Stats:', insectStats);
        console.log('Distribution Labels:', distributionLabels);
        console.log('Distribution Data:', distributionData);
        console.log('Quantity Data:', quantityData);
        
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
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + " reports";
                    }
                }
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
            }]
        };
        
        const quantityChartOptions = {
            series: quantityData,
            chart: {
                type: 'pie',
                height: 350
            },
            labels: distributionLabels,
            colors: ['#3461ff', '#12bf24', '#ff6632', '#8932ff', '#ffcb32', '#ff3e1d', '#299cdb', '#6c757d', '#0dcaf0', '#fd7e14'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + " insects found";
                    }
                }
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
            }]
        };
        
        if (distributionData.length > 0) {
            const distributionChart = new ApexCharts(document.querySelector("#insectDistributionChart"), distributionOptions);
            distributionChart.render();
            
            const quantityChart = new ApexCharts(document.querySelector("#insectQuantityChart"), quantityChartOptions);
            quantityChart.render();
        } else {
            document.querySelector("#insectDistributionChart").innerHTML = '<div class="py-5 text-center">No distribution data available</div>';
            document.querySelector("#insectQuantityChart").innerHTML = '<div class="py-5 text-center">No quantity data available</div>';
        }
    });
</script>
@endpush
