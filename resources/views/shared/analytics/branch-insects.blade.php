@extends('shared.dashboard')

@section('title', 'Branch Insect Analytics')

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
    .visit-timeline {
        position: relative;
        padding-left: 30px;
    }
    .visit-timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        height: 100%;
        width: 2px;
        background: #e9ecef;
    }
    .visit-timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    .visit-timeline-item::before {
        content: '';
        position: absolute;
        left: -30px;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #3461ff;
    }
    .visit-date {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 5px;
    }
    .insect-tag {
        display: inline-block;
        margin-right: 5px;
        margin-bottom: 5px;
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
                    <h5 class="mb-0">Insect Analytics for Branch: {{ $branch->branch_name }}</h5>
                    <div>
                        <a href="{{ route('analytics.contract.branches', ['contractId' => $contract->id]) }}" class="btn btn-sm btn-outline-primary me-2">
                            <i class="bx bx-building me-1"></i> All Branches
                        </a>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-arrow-back me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Contract #:</strong> {{ $contract->id }}</p>
                            <p><strong>Customer:</strong> {{ $contract->customer->name }}</p>
                            <p><strong>Branch Manager:</strong> {{ $branch->branch_manager_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Branch Address:</strong> {{ $branch->branch_address }}, {{ $branch->branch_city }}</p>
                            <p><strong>Contract Status:</strong> <span class="badge bg-{{ $contract->contract_status == 'approved' ? 'success' : 'danger' }}">{{ ucfirst($contract->contract_status) }}</span></p>
                            <p><strong>Total Branch Visits:</strong> {{ count($visitData) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div class="row mt-4">
        @php
            $totalVisits = count($visitData);
            $totalInsects = count($insectStats);
            $totalQuantity = 0;
            $totalReports = 0;
            
            foreach ($insectStats as $stat) {
                $totalReports += $stat['count'];
                $totalQuantity += $stat['quantity'] ?? $stat['count'];
            }
            
            $avgPerReport = $totalReports > 0 ? round($totalQuantity / $totalReports, 1) : 0;
        @endphp
        
        <div class="col-12 col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bx bx-bug fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Insect Types Found</h6>
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
                        <div class="stats-icon bg-success bg-opacity-10 text-success">
                            <i class="bx bx-calendar-check fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Total Branch Visits</h6>
                            <h3 class="mb-0">{{ $totalVisits }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-danger bg-opacity-10 text-danger">
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
                                <div id="insectTrendsChart"></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="quantities" role="tabpanel" aria-labelledby="quantities-tab">
                            <div class="chart-container">
                                <div id="insectQuantitiesChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Top Insects Found</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group top-insects-list">
                        @forelse($insectStats as $index => $stat)
                            @if($index < 10)
                                <li class="list-group-item">
                                    <span>{{ $stat['name'] }}</span>
                                    <div>
                                        <span class="badge bg-primary insect-badge">{{ $stat['count'] }} reports</span>
                                        <span class="badge bg-info insect-badge">{{ $stat['quantity'] }} total</span>
                                    </div>
                                </li>
                            @endif
                        @empty
                            <li class="list-group-item">No insects found in reports</li>
                        @endforelse
                    </ul>
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
                    <div class="chart-container">
                        <div id="insectDensityChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Visit Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="visit-timeline">
                        @forelse($visitData as $visit)
                            <div class="visit-timeline-item">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="visit-date">
                                            <i class="bx bx-calendar me-1"></i> {{ date('d M Y', strtotime($visit['visit_date'])) }}
                                        </div>
                                        <h6>Insects Found:</h6>
                                        <div class="mb-3">
                                            @if(count($visit['target_insects']) > 0)
                                                @foreach($visit['target_insects'] as $insectValue)
                                                    @php
                                                        $insect = $targetInsects->firstWhere('value', $insectValue);
                                                        $insectName = $insect ? $insect->name : $insectValue;
                                                        $quantity = $visit['insect_quantities'][$insectValue] ?? 1;
                                                    @endphp
                                                    <span class="badge bg-light text-dark insect-tag">
                                                        {{ $insectName }} ({{ $quantity }})
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No insects reported</span>
                                            @endif
                                        </div>
                                        
                                        <h6>Recommendations:</h6>
                                        <p class="mb-0">{{ $visit['recommendations'] ?: 'No recommendations provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info">No visit reports found for this branch.</div>
                        @endforelse
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
        // Prepare data for insect trends chart
        const visitData = @json($visitData);
        const targetInsects = @json($targetInsects);
        
        // Process data for charts
        const insectCounts = {};
        const insectQuantities = {};
        const visitDates = [];
        
        // Initialize insect counts and quantities
        targetInsects.forEach(insect => {
            insectCounts[insect.value] = [];
            insectQuantities[insect.value] = [];
        });
        
        // Process visit data
        visitData.forEach(visit => {
            visitDates.push(visit.visit_date);
            
            // Initialize all insects with 0 for this visit
            targetInsects.forEach(insect => {
                insectCounts[insect.value].push(0);
                insectQuantities[insect.value].push(0);
            });
            
            // Update counts for insects found in this visit
            if (visit.target_insects && Array.isArray(visit.target_insects)) {
                visit.target_insects.forEach(insectValue => {
                    const index = visitDates.length - 1;
                    insectCounts[insectValue][index] = 1;
                    
                    // Update quantities if available
                    if (visit.insect_quantities && visit.insect_quantities[insectValue]) {
                        insectQuantities[insectValue][index] = parseInt(visit.insect_quantities[insectValue]);
                    } else {
                        insectQuantities[insectValue][index] = 1; // Default to 1 if no quantity specified
                    }
                });
            }
        });
        
        // Prepare series for trends chart
        const trendsSeries = [];
        const quantitiesSeries = [];
        
        targetInsects.forEach(insect => {
            if (insectCounts[insect.value].some(count => count > 0)) {
                trendsSeries.push({
                    name: insect.name,
                    data: insectCounts[insect.value]
                });
                
                quantitiesSeries.push({
                    name: insect.name,
                    data: insectQuantities[insect.value]
                });
            }
        });
        
        // Configure trends chart
        const trendsOptions = {
            series: trendsSeries,
            chart: {
                type: 'line',
                height: 350,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'straight',
                width: 3
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                }
            },
            xaxis: {
                categories: visitDates.map(date => {
                    return new Date(date).toLocaleDateString();
                }),
                title: {
                    text: 'Visit Date'
                }
            },
            yaxis: {
                title: {
                    text: 'Occurrence (0/1)'
                },
                min: 0,
                max: 1,
                tickAmount: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val === 1 ? 'Found' : 'Not Found';
                    }
                }
            },
            title: {
                text: 'Insect Occurrence by Visit',
                align: 'left'
            },
            legend: {
                position: 'top'
            }
        };
        
        // Configure quantities chart
        const quantitiesOptions = {
            series: quantitiesSeries,
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                },
                stacked: true
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: visitDates.map(date => {
                    return new Date(date).toLocaleDateString();
                }),
                title: {
                    text: 'Visit Date'
                }
            },
            yaxis: {
                title: {
                    text: 'Quantity'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' insects';
                    }
                }
            },
            title: {
                text: 'Insect Quantities by Visit',
                align: 'left'
            },
            legend: {
                position: 'top'
            }
        };
        
        // Configure density chart
        const densityData = @json($insectStats);
        const densitySeries = [{
            name: 'Quantity',
            data: densityData.map(item => item.quantity)
        }];
        
        const densityOptions = {
            series: densitySeries,
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: true
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val;
                },
                offsetX: 20,
                style: {
                    fontSize: '12px',
                    colors: ['#304758']
                }
            },
            xaxis: {
                categories: densityData.map(item => item.name),
                title: {
                    text: 'Insect Type'
                }
            },
            yaxis: {
                title: {
                    text: 'Total Quantity'
                }
            },
            fill: {
                colors: ['#3461ff']
            },
            title: {
                text: 'Total Insect Quantities by Type',
                align: 'left'
            }
        };
        
        // Render charts if we have visit data
        if (visitData.length > 0) {
            const trendsChart = new ApexCharts(document.querySelector("#insectTrendsChart"), trendsOptions);
            trendsChart.render();
            
            const quantitiesChart = new ApexCharts(document.querySelector("#insectQuantitiesChart"), quantitiesOptions);
            quantitiesChart.render();
            
            const densityChart = new ApexCharts(document.querySelector("#insectDensityChart"), densityOptions);
            densityChart.render();
        } else {
            // Display message if no data
            document.querySelector("#insectTrendsChart").innerHTML = '<div class="alert alert-info">No visit data available for this branch.</div>';
            document.querySelector("#insectQuantitiesChart").innerHTML = '<div class="alert alert-info">No visit data available for this branch.</div>';
            document.querySelector("#insectDensityChart").innerHTML = '<div class="alert alert-info">No visit data available for this branch.</div>';
        }
    });
</script>
@endpush
