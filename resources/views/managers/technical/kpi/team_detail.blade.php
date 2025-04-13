@extends('shared.dashboard')

@section('title', 'Team KPI Details')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">KPI Details: {{ $team->name }}</h4>
                        <div class="page-title-right">
                            <a href="{{ route('technical.team.kpi') }}" class="btn btn-secondary btn-sm">
                                <i class="mdi mdi-arrow-left me-1"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Info Card -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3 card-title">Team Information</h5>
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th scope="row" width="200">Team Name:</th>
                                                    <td>{{ $team->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Team Leader:</th>
                                                    <td>{{ $team->leader->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Team Members:</th>
                                                    <td>
                                                        @if ($team->members->count() > 0)
                                                            @foreach ($team->members as $member)
                                                                <span
                                                                    class="badge bg-primary me-1">{{ $member->name }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">No members assigned</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Date Range:</th>
                                                    <td>{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <form action="{{ route('technical.team.kpi.detail', $team->id) }}" method="GET"
                                        class="mt-3 mt-md-0">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="mb-3">
                                                    <label for="start_date" class="form-label">Start Date</label>
                                                    <input type="date" class="form-control" id="start_date"
                                                        name="start_date"
                                                        value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="mb-3">
                                                    <label for="end_date" class="form-label">End Date</label>
                                                    <input type="date" class="form-control" id="end_date"
                                                        name="end_date"
                                                        value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <div class="mb-3 w-100">
                                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="mt-3 text-end">
                                        <form action="{{ route('technical.team.kpi.pdf') }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <input type="hidden" name="team_id" value="{{ $team->id }}">
                                            <input type="hidden" name="start_date"
                                                value="{{ $startDate->format('Y-m-d') }}">
                                            <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                                            <button type="submit" class="btn btn-secondary">
                                                <i class="mdi mdi-file-pdf-outline me-1"></i> Export PDF
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Summary Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Visits</p>
                                    <h4 class="mb-0">{{ $kpiData[$team->id]['total_visits'] }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-calendar-check font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Completion Rate</p>
                                    <h4 class="mb-0">{{ $kpiData[$team->id]['completion_rate'] }}%</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success align-self-center">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-check-circle-outline font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Report Submission</p>
                                    <h4 class="mb-0">{{ $kpiData[$team->id]['report_rate'] }}%</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-info align-self-center">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-file-document-outline font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Customer Satisfaction</p>
                                    <h4 class="mb-0">
                                        {{ is_array($kpiData[$team->id]['avg_satisfaction']) ? number_format(array_sum($kpiData[$team->id]['avg_satisfaction']) / max(1, count($kpiData[$team->id]['avg_satisfaction'])), 1) : $kpiData[$team->id]['avg_satisfaction'] }}/5
                                    </h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-warning align-self-center">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-star font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-4 card-title">Visit Performance</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h5 class="font-size-15">Visit Completion</h5>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $kpiData[$team->id]['completion_rate'] }}%;"
                                                aria-valuenow="{{ $kpiData[$team->id]['completion_rate'] }}"
                                                aria-valuemin="0" aria-valuemax="100">
                                                {{ $kpiData[$team->id]['completion_rate'] }}%
                                            </div>
                                        </div>
                                        <div class="mt-2 text-muted small">
                                            Completed {{ $kpiData[$team->id]['completed_visits'] }} of
                                            {{ $kpiData[$team->id]['total_visits'] }} visits
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h5 class="font-size-15">Report Submission</h5>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" role="progressbar"
                                                style="width: {{ $kpiData[$team->id]['report_rate'] }}%;"
                                                aria-valuenow="{{ $kpiData[$team->id]['report_rate'] }}"
                                                aria-valuemin="0" aria-valuemax="100">
                                                {{ $kpiData[$team->id]['report_rate'] }}%
                                            </div>
                                        </div>
                                        <div class="mt-2 text-muted small">
                                            Submitted {{ $kpiData[$team->id]['reports_created'] }} of
                                            {{ $kpiData[$team->id]['completed_visits'] }} reports
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h5 class="font-size-15">Avg. Visit Duration</h5>
                                        <h4 class="mt-2">{{ $kpiData[$team->id]['avg_visit_duration'] }} min</h4>
                                        <div class="mt-2 text-muted small">
                                            Time spent at customer locations
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h5 class="font-size-15">Avg. Report Submission Time</h5>
                                        <h4 class="mt-2">{{ $kpiData[$team->id]['avg_report_time'] }} hrs</h4>
                                        <div class="mt-2 text-muted small">
                                            Time between visit completion and report submission
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-4 card-title">Technical Performance</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h5 class="font-size-15">Top Target Insects</h5>
                                        <ul class="mt-3 list-unstyled">
                                            @forelse($kpiData[$team->id]['top_insects'] as $insect => $count)
                                                <li class="mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="mdi mdi-circle-medium text-primary"></i>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-0">{{ $insect }}</p>
                                                            <div class="progress" style="height: 5px;">
                                                                <div class="progress-bar bg-primary" role="progressbar"
                                                                    style="width: {{ ((is_scalar($count) ? $count : 1) / max(1, is_array($kpiData[$team->id]['top_insects']) ? array_sum($kpiData[$team->id]['top_insects']) : 1)) * 100 }}%;"
                                                                    aria-valuenow="{{ is_scalar($count) ? $count : 1 }}"
                                                                    aria-valuemin="0"
                                                                    aria-valuemax="{{ is_array($kpiData[$team->id]['top_insects']) ? array_sum($kpiData[$team->id]['top_insects']) : 1 }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <span class="ms-2">{{ $count }}</span>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="text-muted">No data available</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <h5 class="font-size-15">Top Pesticides Used</h5>
                                        <ul class="mt-3 list-unstyled">
                                            @forelse($kpiData[$team->id]['top_pesticides'] as $pesticide => $count)
                                                <li class="mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="mdi mdi-circle-medium text-success"></i>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-0">{{ $pesticide }}</p>
                                                            <div class="progress" style="height: 5px;">
                                                                <div class="progress-bar bg-success" role="progressbar"
                                                                    style="width: {{ ((is_scalar($count) ? $count : 1) / max(1, is_array($kpiData[$team->id]['top_pesticides']) ? array_sum($kpiData[$team->id]['top_pesticides']) : 1)) * 100 }}%;"
                                                                    aria-valuenow="{{ is_scalar($count) ? $count : 1 }}"
                                                                    aria-valuemin="0"
                                                                    aria-valuemax="{{ is_array($kpiData[$team->id]['top_pesticides']) ? array_sum($kpiData[$team->id]['top_pesticides']) : 1 }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <span class="ms-2">{{ $count }}</span>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="text-muted">No data available</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-4">
                                        <h5 class="font-size-15">Customer Satisfaction</h5>
                                        <div class="mt-3 d-flex align-items-center">
                                            <h4 class="mb-0">
                                                {{ is_array($kpiData[$team->id]['avg_satisfaction']) ? number_format(array_sum($kpiData[$team->id]['avg_satisfaction']) / max(1, count($kpiData[$team->id]['avg_satisfaction'])), 1) : $kpiData[$team->id]['avg_satisfaction'] }}
                                            </h4>
                                            <div class="ms-2">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if (
                                                        $i <=
                                                            round(is_array($kpiData[$team->id]['avg_satisfaction'])
                                                                    ? array_sum($kpiData[$team->id]['avg_satisfaction']) /
                                                                        max(1, count($kpiData[$team->id]['avg_satisfaction']))
                                                                    : $kpiData[$team->id]['avg_satisfaction']))
                                                        <i class="mdi mdi-star text-warning font-size-20"></i>
                                                    @else
                                                        <i class="mdi mdi-star-outline text-warning font-size-20"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <div class="ms-3 text-muted small">
                                                Based on customer feedback
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visit Details Table -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-4 card-title">Visit Details</h4>
                            <div class="table-responsive">
                                <table class="table mb-0 table-centered table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Visit Date</th>
                                            <th>Contract</th>
                                            <th>Branch</th>
                                            <th>Status</th>
                                            <th>Report</th>
                                            <th>Duration</th>
                                            <th>Satisfaction</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($visitData as $visit)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($visit->visit_date)->format('d M Y') }}
                                                    {{ $visit->visit_time }}</td>
                                                <td>
                                                    <a href="{{ route('technical.contract.show', $visit->contract_id) }}">
                                                        {{ $visit->contract->contract_number }}
                                                    </a>
                                                    <div class="small text-muted">{{ $visit->contract->customer->name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($visit->branch)
                                                        {{ $visit->branch->branch_name ? $visit->branch->branch_name : 'N/A' }}
                                                        <span
                                                            class="text-muted">{{ $visit->branch->branch_address ? $visit->branch->branch_address : 'N/A' }}</span>
                                                    @else
                                                        <span>N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($visit->status == 'completed')
                                                        <span class="badge bg-success">Completed</span>
                                                    @elseif($visit->status == 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($visit->status == 'cancelled')
                                                        <span class="badge bg-danger">Cancelled</span>
                                                    @else
                                                        <span
                                                            class="badge bg-secondary">{{ ucfirst($visit->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($visit->report)
                                                        <a href="{{ route('technical.visit.report.view', $visit->id) }}"
                                                            class="btn btn-sm btn-soft-primary">
                                                            <i class="mdi mdi-file-document-outline"></i>
                                                            View
                                                        </a>
                                                    @else
                                                        <span class="text-muted">No report</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($visit->report && $visit->report->time_in && $visit->report->time_out)
                                                        {{ \Carbon\Carbon::parse($visit->report->time_in)->diffInMinutes(\Carbon\Carbon::parse($visit->report->time_out)) }}
                                                        min
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($visit->report && $visit->report->customer_satisfaction)
                                                        <div class="d-flex align-items-center">
                                                            {{ $visit->report->customer_satisfaction }}
                                                            <div class="ms-1">
                                                                <i class="mdi mdi-star text-warning"></i>
                                                            </div>
                                                        </div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No visits found for the selected
                                                    period</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
