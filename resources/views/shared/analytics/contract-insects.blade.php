@extends('shared.dashboard')

@section('title', 'Contract Insect Analytics')

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
</style>
@endpush

@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Insect Analytics for Contract #{{ $contract->id }}</h5>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary">
                        <i class="bx bx-arrow-back me-1"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Customer:</strong> {{ $contract->customer->name }}</p>
                            <p><strong>Contract Type:</strong> {{ $contract->type->name }}</p>
                            <p><strong>Start Date:</strong> {{ date('d M Y', strtotime($contract->contract_start_date)) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>End Date:</strong> {{ date('d M Y', strtotime($contract->contract_end_date)) }}</p>
                            <p><strong>Status:</strong> <span class="badge bg-{{ $contract->contract_status == 'approved' ? 'success' : 'danger' }}">{{ ucfirst($contract->contract_status) }}</span></p>
                            <p><strong>Total Visits:</strong> {{ count($visitData) }}</p>
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
                    <h6 class="mb-0">Insect Prevalence Over Time</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div id="insectTrendsChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Most Common Insects</h6>
                </div>
                <div class="card-body">
                    <div class="top-insects-list">
                        <ul class="list-group">
                            @forelse($insectStats as $stat)
                                <li class="list-group-item">
                                    <span>
                                        <i class="bx bx-bug me-1"></i>
                                        {{ $stat['name'] }}
                                    </span>
                                    <span>
                                        <span class="badge bg-primary insect-badge">{{ $stat['count'] }} visits</span>
                                        <span class="badge bg-info insect-badge">{{ $stat['percentage'] }}%</span>
                                    </span>
                                </li>
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

    <div class="mt-4 row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Visit History & Insect Findings</h6>
                </div>
                <div class="card-body">
                    <div class="visit-timeline">
                        @forelse($visitData as $visit)
                            <div class="visit-timeline-item">
                                <div class="visit-date">
                                    <strong>Visit Date:</strong> {{ date('d M Y', strtotime($visit['visit_date'])) }}
                                </div>
                                <div class="mb-3 card">
                                    <div class="card-body">
                                        <h6 class="mb-3">Target Insects Found:</h6>
                                        <div class="mb-3">
                                            @if(count($visit['target_insects']) > 0)
                                                @foreach($visit['target_insects'] as $insectValue)
                                                    @php
                                                        $insect = $targetInsects->where('value', $insectValue)->first();
                                                        $insectName = $insect ? $insect->name : $insectValue;
                                                    @endphp
                                                    <span class="badge bg-primary insect-tag">{{ $insectName }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No insects reported</span>
                                            @endif
                                        </div>
                                        <h6 class="mb-2">Recommendations:</h6>
                                        <p class="mb-0">{{ $visit['recommendations'] ?: 'No recommendations provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-4 text-center">
                                <p>No visit data available for this contract</p>
                            </div>
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
        
        // Create a mapping of insect values to names
        const insectNames = {};
        targetInsects.forEach(insect => {
            insectNames[insect.value] = insect.name;
        });
        
        // Get unique insects from all visits
        const uniqueInsects = new Set();
        visitData.forEach(visit => {
            visit.target_insects.forEach(insect => {
                uniqueInsects.add(insect);
            });
        });
        
        // Prepare series data for each insect
        const seriesData = {};
        uniqueInsects.forEach(insect => {
            seriesData[insect] = {
                name: insectNames[insect] || insect,
                data: []
            };
        });
        
        // Sort visits by date (oldest first for the chart)
        const sortedVisits = [...visitData].sort((a, b) => 
            new Date(a.visit_date) - new Date(b.visit_date)
        );
        
        // Populate the data points
        const visitDates = [];
        sortedVisits.forEach(visit => {
            const formattedDate = new Date(visit.visit_date).toLocaleDateString('en-US', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
            visitDates.push(formattedDate);
            
            // For each unique insect, check if it was found in this visit
            uniqueInsects.forEach(insect => {
                const found = visit.target_insects.includes(insect) ? 1 : 0;
                seriesData[insect].data.push(found);
            });
        });
        
        // Convert to array for ApexCharts
        const series = Object.values(seriesData);
        
        // Only show top 5 insects for readability
        const topInsects = series.slice(0, 5);
        
        // Insect Trends Chart
        const trendsOptions = {
            series: topInsects,
            chart: {
                height: 350,
                type: 'line',
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
            xaxis: {
                categories: visitDates
            },
            yaxis: {
                min: 0,
                max: 1,
                tickAmount: 1,
                labels: {
                    formatter: function(val) {
                        return val === 1 ? 'Present' : 'Absent';
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value === 1 ? 'Present' : 'Absent';
                    }
                }
            },
            legend: {
                position: 'top'
            }
        };
        
        if (visitData.length > 0) {
            const trendsChart = new ApexCharts(document.querySelector("#insectTrendsChart"), trendsOptions);
            trendsChart.render();
        } else {
            document.querySelector("#insectTrendsChart").innerHTML = '<div class="py-5 text-center">No trend data available</div>';
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
                        return value + " visits";
                    }
                }
            }
        };
        
        if (distributionData.length > 0) {
            const distributionChart = new ApexCharts(document.querySelector("#insectDistributionChart"), distributionOptions);
            distributionChart.render();
        } else {
            document.querySelector("#insectDistributionChart").innerHTML = '<div class="py-5 text-center">No distribution data available</div>';
        }
    });
</script>
@endpush
