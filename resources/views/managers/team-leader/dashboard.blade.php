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
    <!-- Welcome Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h4 class="mb-0">Welcome, {{ Auth::user()->name }}</h4>
                            <p class="mb-0 text-muted">Team Leader - {{ $team->name }}</p>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-primary">
                                <i class="bx bx-calendar me-1"></i>
                                {{ now()->format('l, F j, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="mt-4 row">
        <div class="col-md-3">
            <div class="card radius-10 bg-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h4 class="mb-0 text-white">{{ $statistics['today_visits'] }}</h4>
                            <p class="mb-0 text-white">Today's Visits</p>
                        </div>
                        <div class="text-white ms-auto fs-2">
                            <i class="bx bx-calendar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card radius-10 bg-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h4 class="mb-0 text-white">{{ $statistics['today_completed'] }}</h4>
                            <p class="mb-0 text-white">Completed Today</p>
                        </div>
                        <div class="text-white ms-auto fs-2">
                            <i class="bx bx-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card radius-10 bg-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h4 class="mb-0 text-white">{{ $statistics['upcoming_visits'] }}</h4>
                            <p class="mb-0 text-white">Upcoming (7 Days)</p>
                        </div>
                        <div class="text-white ms-auto fs-2">
                            <i class="bx bx-time"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card radius-10 bg-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h4 class="mb-0 text-white">{{ $statistics['total_completed'] }}</h4>
                            <p class="mb-0 text-white">Total Completed</p>
                        </div>
                        <div class="text-white ms-auto fs-2">
                            <i class="bx bx-trophy"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Visits -->
    <div class="mt-4 row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h5 class="mb-0">Today's Visits</h5>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('team-leader.visits') }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-calendar me-1"></i>View All Visits
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($todayVisits->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Contract #</th>
                                    <th>Type</th>
                                    <th>Customer</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayVisits as $visit)
                                <tr>
                                    <td>{{ date('h:i A', strtotime($visit->visit_time)) }}</td>
                                    <td>
                                        <a href="{{ route('team-leader.contract.show', $visit->contract->id) }}"
                                            class="text-primary">
                                            {{ $visit->contract->contract_number }}
                                        </a>
                                    </td>
                                    <td>{{ $visit->contract->type->name }}</td>
                                    <td>{{ $visit->contract->customer->name }}</td>
                                    <td>
                                        @if($visit->branch_id)
                                        {{ $visit->branch->branch_name }}
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($visit->branch->branch_address) }}"
                                            target="_blank" class="text-primary"><i class="bx bx-map me-1">{{
                                                $visit->branch->branch_address }}</i></a>
                                        @else
                                        Main Location
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($visit->contract->customer->address) }}"
                                            target="_blank" class="text-primary"><i class="bx bx-map me-1">{{
                                                $visit->contract->customer->address }}</i></a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($visit->status == 'completed')
                                        <span class="badge bg-success">
                                            <i class="bx bx-check-circle me-1"></i>Completed
                                        </span>
                                        @elseif($visit->status == 'scheduled')
                                        <span class="badge bg-info">
                                            <i class="bx bx-time me-1"></i>Scheduled
                                        </span>
                                        @elseif($visit->status == 'in_progress')
                                        <span class="badge bg-warning">
                                            <i class="bx bx-time me-1"></i>In Progress
                                        </span>
                                        @else
                                        <span class="badge bg-danger">
                                            <i class="bx bx-x me-1"></i>Cancelled
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if ($visit->status == 'completed')
                                                <a href="{{ route('team-leader.visit.show', $visit->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bx bx-show me-1"></i>View Report
                                                </a>
                                            @elseif($visit->status == 'scheduled' && $visit->visit_date <= now()->format('Y-m-d') || $visit->status == 'in_progress')
                                                <a href="{{ route('team-leader.visit.report.create', $visit->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bx bx-file me-1"></i>Create Report
                                                </a>
                                            @else
                                                <a href="{{ route('team-leader.visit.show', $visit->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bx bx-show me-1"></i>View Details
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="py-4 text-center">
                        <i class="bx bx-calendar-x text-muted" style="font-size: 4rem;"></i>
                        <p class="mt-2 text-muted">No visits scheduled for today</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Visits -->
    <div class="mt-4 row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Upcoming Visits (Next 7 Days)</h5>
                </div>
                <div class="card-body">
                    @if($upcomingVisits->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Contract #</th>
                                    <th>Type</th>
                                    <th>Customer</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingVisits as $visit)
                                <tr>
                                    <td>
                                        <span class="d-block">{{ date('M d, Y', strtotime($visit->visit_date)) }}</span>
                                        <small class="text-muted">{{ date('h:i A', strtotime($visit->visit_time))
                                            }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('team-leader.contract.show', $visit->contract->id) }}"
                                            class="text-primary">
                                            {{ $visit->contract->contract_number }}
                                        </a>
                                    </td>
                                    <td>{{ $visit->contract->type->name }}</td>
                                    <td>{{ $visit->contract->customer->name }}</td>
                                    <td>
                                        @if($visit->branch_id)
                                        {{ $visit->branch->branch_name }}
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($visit->branch->branch_address) }}"
                                            target="_blank" class="text-primary"><i class="bx bx-map me-1">{{
                                                $visit->branch->branch_address }}</i></a>
                                        @else
                                        Main Location
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($visit->contract->customer->address) }}"
                                            target="_blank" class="text-primary"><i class="bx bx-map me-1">{{
                                                $visit->contract->customer->address }}</i></a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($visit->status == 'scheduled')
                                        <span class="badge bg-primary">
                                            <i class="bx bx-calendar me-1"></i>Scheduled
                                        </span>
                                        @elseif($visit->status == 'completed')
                                        <span class="badge bg-success">
                                            <i class="bx bx-check me-1"></i>Completed
                                        </span>
                                        @elseif($visit->status == 'in_progress')
                                        <span class="badge bg-warning">
                                            <i class="bx bx-time me-1"></i>In Progress
                                        </span>
                                        @else
                                        <span class="badge bg-danger">
                                            <i class="bx bx-x me-1"></i>Cancelled
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if ($visit->status == 'completed')
                                                <a href="{{ route('team-leader.visit.show', $visit->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bx bx-show me-1"></i>View Report
                                                </a>
                                            @elseif($visit->status == 'scheduled' && $visit->visit_date <= now()->format('Y-m-d') || $visit->status == 'in_progress')
                                                <a href="{{ route('team-leader.visit.report.create', $visit->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bx bx-file me-1"></i>Create Report
                                                </a>
                                            @else
                                                <a href="{{ route('team-leader.visit.show', $visit->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bx bx-show me-1"></i>View Details
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="py-4 text-center">
                        <i class="bx bx-calendar-x text-muted" style="font-size: 4rem;"></i>
                        <p class="mt-2 text-muted">No upcoming visits scheduled</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection