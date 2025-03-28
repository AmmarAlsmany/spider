@extends('shared.dashboard')

@section('title', 'Team KPI Dashboard')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Team KPI Dashboard</h4>
                </div>
            </div>
        </div>

        <!-- Filter form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('technical.team.kpi') }}" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="team_id" class="form-label">Select Team</label>
                                        <select class="form-select" id="team_id" name="team_id">
                                            <option value="">All Teams</option>
                                            @foreach($teams as $team)
                                                <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                                    {{ $team->name }} ({{ $team->leader->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                            value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                            value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <div class="mb-3 w-100">
                                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="row">
            @foreach($kpiData as $teamId => $data)
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-soft-primary">
                        <h5 class="mb-0 card-title">{{ $data['team']->name }}</h5>
                        <p class="mb-0 card-text">Team Leader: {{ $data['team']->leader->name }}</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <h5 class="font-size-15">Visit Completion</h5>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $data['completion_rate'] }}%;" 
                                            aria-valuenow="{{ $data['completion_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ $data['completion_rate'] }}%
                                        </div>
                                    </div>
                                    <div class="mt-2 text-muted small">
                                        Completed {{ $data['completed_visits'] }} of {{ $data['total_visits'] }} visits
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <h5 class="font-size-15">Report Submission</h5>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $data['report_rate'] }}%;" 
                                            aria-valuenow="{{ $data['report_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ $data['report_rate'] }}%
                                        </div>
                                    </div>
                                    <div class="mt-2 text-muted small">
                                        Submitted {{ $data['reports_created'] }} of {{ $data['completed_visits'] }} reports
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <h5 class="font-size-15">Avg. Visit Duration</h5>
                                    <h4 class="mt-2">{{ $data['avg_visit_duration'] }} min</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <h5 class="font-size-15">Avg. Report Time</h5>
                                    <h4 class="mt-2">{{ $data['avg_report_time'] }} hrs</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <h5 class="font-size-15">Customer Satisfaction</h5>
                                    <div class="mt-2 d-flex align-items-center">
                                        <h4 class="mb-0">{{ $data['avg_satisfaction'] }}</h4>
                                        <div class="ms-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= round($data['avg_satisfaction']))
                                                    <i class="mdi mdi-star text-warning"></i>
                                                @else
                                                    <i class="mdi mdi-star-outline text-warning"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <h5 class="font-size-15">Top Target Insects</h5>
                                    <ul class="mt-2 list-unstyled">
                                        @forelse($data['top_insects'] as $insect => $count)
                                            <li><i class="mdi mdi-circle-medium text-primary"></i> {{ $insect }} ({{ $count }})</li>
                                        @empty
                                            <li class="text-muted">No data available</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <h5 class="font-size-15">Top Pesticides Used</h5>
                                    <ul class="mt-2 list-unstyled">
                                        @forelse($data['top_pesticides'] as $pesticide => $count)
                                            <li><i class="mdi mdi-circle-medium text-success"></i> {{ $pesticide }} ({{ $count }})</li>
                                        @empty
                                            <li class="text-muted">No data available</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-center">
                            <a href="{{ route('technical.team.kpi.detail', $teamId) }}" class="btn btn-primary">
                                View Detailed KPIs
                            </a>
                            <form action="{{ route('technical.team.kpi.pdf') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="team_id" value="{{ $teamId }}">
                                <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                                <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                                <button type="submit" class="btn btn-secondary ms-2">
                                    <i class="mdi mdi-file-pdf-outline me-1"></i> Export PDF
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Compare Teams Button -->
        <div class="mb-4 row">
            <div class="text-center col-12">
                <a href="{{ route('technical.team.kpi.compare') }}" class="btn btn-lg btn-info">
                    <i class="mdi mdi-chart-bar me-1"></i> Compare Team Performance
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
