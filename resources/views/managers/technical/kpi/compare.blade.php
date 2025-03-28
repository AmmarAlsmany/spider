@extends('shared.dashboard')

@section('title', 'Compare Team Performance')

@section('styles')
<style>
    .comparison-card {
        transition: all 0.3s ease;
    }
    .comparison-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .team-badge {
        width: 15px;
        height: 15px;
        display: inline-block;
        margin-right: 5px;
        border-radius: 50%;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Compare Team Performance</h4>
                    <div class="page-title-right">
                        <a href="{{ route('technical.team.kpi') }}" class="btn btn-secondary btn-sm">
                            <i class="mdi mdi-arrow-left me-1"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('technical.team.kpi.compare') }}" method="GET">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="team_ids" class="form-label">Select Teams to Compare (max 4)</label>
                                        <select class="select2 form-control select2-multiple" id="team_ids" name="team_ids[]" multiple="multiple" data-placeholder="Choose teams...">
                                            @foreach($teams as $team)
                                                <option value="{{ $team->id }}" 
                                                    {{ in_array($team->id, request('team_ids', [])) ? 'selected' : '' }}>
                                                    {{ $team->name }} ({{ $team->leader->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                            value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                            value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <div class="mb-3 w-100">
                                        <button type="submit" class="btn btn-primary w-100">Compare Teams</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(count($selectedTeams) > 0)
            <!-- Comparison Summary -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-4 card-title">Performance Comparison</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-4">
                                        <h5 class="font-size-15">Total Visits</h5>
                                        <div id="total-visits-chart" style="height: 250px;"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-4">
                                        <h5 class="font-size-15">Completion Rate (%)</h5>
                                        <div id="completion-rate-chart" style="height: 250px;"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-4">
                                        <h5 class="font-size-15">Report Submission (%)</h5>
                                        <div id="report-rate-chart" style="height: 250px;"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-4">
                                        <h5 class="font-size-15">Customer Satisfaction</h5>
                                        <div id="satisfaction-chart" style="height: 250px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Comparison -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-4 card-title">Detailed Metrics Comparison</h4>
                            <div class="table-responsive">
                                <table class="table mb-0 table-centered table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Metric</th>
                                            @foreach($selectedTeams as $team)
                                                <th>{{ $team->name }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Total Visits</strong></td>
                                            @foreach($selectedTeams as $team)
                                                <td>{{ $kpiData[$team->id]['total_visits'] }}</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td><strong>Completed Visits</strong></td>
                                            @foreach($selectedTeams as $team)
                                                <td>{{ $kpiData[$team->id]['completed_visits'] }}</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td><strong>Completion Rate</strong></td>
                                            @foreach($selectedTeams as $team)
                                                <td>{{ $kpiData[$team->id]['completion_rate'] }}%</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td><strong>Reports Created</strong></td>
                                            @foreach($selectedTeams as $team)
                                                <td>{{ $kpiData[$team->id]['reports_created'] }}</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td><strong>Report Submission Rate</strong></td>
                                            @foreach($selectedTeams as $team)
                                                <td>{{ $kpiData[$team->id]['report_rate'] }}%</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td><strong>Avg. Visit Duration</strong></td>
                                            @foreach($selectedTeams as $team)
                                                <td>{{ $kpiData[$team->id]['avg_visit_duration'] }} min</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td><strong>Avg. Report Submission Time</strong></td>
                                            @foreach($selectedTeams as $team)
                                                <td>{{ $kpiData[$team->id]['avg_report_time'] }} hrs</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td><strong>Customer Satisfaction</strong></td>
                                            @foreach($selectedTeams as $team)
                                                <td>
                                                    {{ $kpiData[$team->id]['avg_satisfaction'] }}
                                                    <div class="mt-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= round($kpiData[$team->id]['avg_satisfaction']))
                                                                <i class="mdi mdi-star text-warning"></i>
                                                            @else
                                                                <i class="mdi mdi-star-outline text-warning"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Cards -->
            <div class="row">
                @foreach($selectedTeams as $index => $team)
                    <div class="col-md-6 col-xl-3">
                        <div class="card comparison-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 avatar-sm me-3">
                                        <span class="avatar-title bg-soft-primary text-primary rounded-circle font-size-18">
                                            <i class="mdi mdi-account-group"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1 font-size-14">{{ $team->name }}</h5>
                                        <p class="mb-0 text-muted">{{ $team->leader->name }}</p>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="text-center row">
                                    <div class="col-6">
                                        <div>
                                            <p class="mb-2 text-muted">Completion</p>
                                            <h5>{{ $kpiData[$team->id]['completion_rate'] }}%</h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div>
                                            <p class="mb-2 text-muted">Satisfaction</p>
                                            <h5>{{ $kpiData[$team->id]['avg_satisfaction'] }}/5</h5>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <a href="{{ route('technical.team.kpi.detail', $team->id) }}" class="btn btn-primary btn-sm w-100">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="py-5 text-center card-body">
                            <div class="mx-auto mb-4 avatar-lg">
                                <div class="m-0 avatar-title bg-soft-primary text-primary display-4 rounded-circle">
                                    <i class="mdi mdi-chart-bar-stacked"></i>
                                </div>
                            </div>
                            <h5>Select Teams to Compare</h5>
                            <p class="text-muted">Choose at least two teams from the dropdown above to see a detailed performance comparison.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
@if(count($selectedTeams) > 0)
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Define team colors
        const teamColors = [
            '#556ee6', '#34c38f', '#f1b44c', '#50a5f1', '#f46a6a', '#74788d'
        ];
        
        // Total Visits Chart
        const totalVisitsOptions = {
            series: [
                @foreach($selectedTeams as $index => $team)
                {
                    name: '{{ $team->name }}',
                    data: [{{ $kpiData[$team->id]['total_visits'] }}]
                },
                @endforeach
            ],
            chart: {
                type: 'bar',
                height: 250,
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
                enabled: true
            },
            colors: teamColors.slice(0, {{ count($selectedTeams) }}),
            xaxis: {
                categories: ['Total Visits'],
            },
            legend: {
                show: false
            }
        };
        
        const totalVisitsChart = new ApexCharts(document.querySelector("#total-visits-chart"), totalVisitsOptions);
        totalVisitsChart.render();
        
        // Completion Rate Chart
        const completionRateOptions = {
            series: [
                @foreach($selectedTeams as $index => $team)
                {
                    name: '{{ $team->name }}',
                    data: [{{ $kpiData[$team->id]['completion_rate'] }}]
                },
                @endforeach
            ],
            chart: {
                type: 'bar',
                height: 250,
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
                enabled: true,
                formatter: function (val) {
                    return val + "%";
                }
            },
            colors: teamColors.slice(0, {{ count($selectedTeams) }}),
            xaxis: {
                categories: ['Completion Rate'],
            },
            yaxis: {
                max: 100
            },
            legend: {
                show: false
            }
        };
        
        const completionRateChart = new ApexCharts(document.querySelector("#completion-rate-chart"), completionRateOptions);
        completionRateChart.render();
        
        // Report Rate Chart
        const reportRateOptions = {
            series: [
                @foreach($selectedTeams as $index => $team)
                {
                    name: '{{ $team->name }}',
                    data: [{{ $kpiData[$team->id]['report_rate'] }}]
                },
                @endforeach
            ],
            chart: {
                type: 'bar',
                height: 250,
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
                enabled: true,
                formatter: function (val) {
                    return val + "%";
                }
            },
            colors: teamColors.slice(0, {{ count($selectedTeams) }}),
            xaxis: {
                categories: ['Report Submission'],
            },
            yaxis: {
                max: 100
            },
            legend: {
                show: false
            }
        };
        
        const reportRateChart = new ApexCharts(document.querySelector("#report-rate-chart"), reportRateOptions);
        reportRateChart.render();
        
        // Satisfaction Chart
        const satisfactionOptions = {
            series: [
                @foreach($selectedTeams as $index => $team)
                {
                    name: '{{ $team->name }}',
                    data: [{{ $kpiData[$team->id]['avg_satisfaction'] }}]
                },
                @endforeach
            ],
            chart: {
                type: 'bar',
                height: 250,
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
                enabled: true
            },
            colors: teamColors.slice(0, {{ count($selectedTeams) }}),
            xaxis: {
                categories: ['Customer Satisfaction'],
            },
            yaxis: {
                max: 5
            },
            legend: {
                show: false
            }
        };
        
        const satisfactionChart = new ApexCharts(document.querySelector("#satisfaction-chart"), satisfactionOptions);
        satisfactionChart.render();
        
        // Add legend below charts
        const legendContainer = document.createElement('div');
        legendContainer.className = 'mt-3 d-flex justify-content-center flex-wrap';
        
        @foreach($selectedTeams as $index => $team)
        legendContainer.innerHTML += `
            <div class="mb-2 me-4">
                <span class="team-badge" style="background-color: ${teamColors[{{ $index }}]};"></span>
                <span>{{ $team->name }}</span>
            </div>
        `;
        @endforeach
        
        document.querySelector('.card-body').appendChild(legendContainer);
    });
</script>
@endif
@endsection
