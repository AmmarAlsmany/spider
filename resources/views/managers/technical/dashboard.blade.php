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
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Technical Dashboard</h3>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="mb-4 row g-3">
                        <div class="col-xl-3 col-md-6">
                            <div class="overflow-hidden border-0 transition-all card rounded-4 hover:transform hover:scale-102"
                        style="box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%);">
                        <div class="p-4 card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 p-3 rounded-circle"
                                    style="background: linear-gradient(45deg, #727cf5, #a2abff);">
                                    <i class="text-white fas fa-users fs-3"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1 text-sm font-medium text-muted">Total Teams</p>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $teams->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="overflow-hidden border-0 transition-all card rounded-4 hover:transform hover:scale-102"
                        style="box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%);">
                        <div class="p-4 card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 p-3 rounded-circle"
                                    style="background: linear-gradient(45deg, #0acf97, #6fdebd);">
                                    <i class="text-white fas fa-calendar-check fs-3"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1 text-sm font-medium text-muted">Today's Appointments</p>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $todayAppointments ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="overflow-hidden border-0 transition-all card rounded-4 hover:transform hover:scale-102"
                        style="box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%);">
                        <div class="p-4 card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 p-3 rounded-circle"
                                    style="background: linear-gradient(45deg, #f9bc0b, #ffd66e);">
                                    <i class="text-white fas fa-tasks fs-3"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1 text-sm font-medium text-muted">Pending Tasks</p>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $pendingTasks ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="overflow-hidden border-0 transition-all card rounded-4 hover:transform hover:scale-102"
                        style="box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%);">
                        <div class="p-4 card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 p-3 rounded-circle"
                                    style="background: linear-gradient(45deg, #39afd1, #7bc9e6);">
                                    <i class="text-white fas fa-ticket-alt fs-3"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1 text-sm font-medium text-muted">Open Tickets</p>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $openTickets ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contract Statistics -->
            <div class="mb-4 row">
                <div class="col-12">
                    <div class="overflow-hidden border-0 card rounded-4"
                        style="box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%);">
                        <div class="p-4 bg-transparent card-header border-bottom">
                            <h5 class="mb-0 card-title fw-bold">Contract Statistics</h5>
                        </div>
                        <div class="p-4 card-body">
                            <div class="row g-0">
                                @foreach($contractStats as $stat)
                                <div class="col-md-3">
                                    <div class="p-4 border-end">
                                        <div class="text-center">
                                            <h5 class="mb-3 fw-bold text-dark">{{ $stat->type->name }}</h5>
                                            <p class="mb-0 text-muted">{{ $stat->total }} Contracts</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Schedule -->
            <div class="mb-4 row">
                <div class="col-12">
                    <div class="overflow-hidden border-0 card rounded-4"
                        style="box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%);">
                        <div
                            class="p-4 bg-transparent card-header border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 card-title fw-bold">Today's Schedule</h5>
                        </div>
                        <div class="p-4 card-body">
                            @php
                                // Group today's visits by branch - fixed to prevent missing visits
                                $todayBranchVisits = [];
                                
                                // Pre-check for empty schedule list
                                if(count($teamSchedules) > 0) {
                                    foreach($teamSchedules as $schedule) {
                                        $branchId = $schedule->branch_id ?? 0;
                                        $branchName = $schedule->branch_id ? ($schedule->branch ? $schedule->branch->branch_name : 'Unknown Branch') : 'Main Branch';
                                        
                                        if(!isset($todayBranchVisits[$branchId])) {
                                            $todayBranchVisits[$branchId] = [
                                                'name' => $branchName,
                                                'visits' => []
                                            ];
                                        }
                                        
                                        // Add to the visits array
                                        $todayBranchVisits[$branchId]['visits'][] = $schedule;
                                    }
                                }
                            @endphp
                            
                            @if(count($todayBranchVisits) > 0)
                                <!-- Branch Tabs -->
                                <ul class="mb-3 nav nav-tabs" id="todayBranchTabs" role="tablist">
                                    @php $firstBranch = true; @endphp
                                    @foreach($todayBranchVisits as $branchId => $branch)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $firstBranch ? 'active' : '' }}" 
                                                    id="today-branch-{{ $branchId }}-tab" 
                                                    data-bs-toggle="tab" 
                                                    data-bs-target="#today-branch-{{ $branchId }}-content" 
                                                    type="button" 
                                                    role="tab" 
                                                    aria-controls="today-branch-{{ $branchId }}-content" 
                                                    aria-selected="{{ $firstBranch ? 'true' : 'false' }}">
                                                {{ $branch['name'] }}
                                                <span class="badge bg-primary ms-2">{{ count($branch['visits']) }}</span>
                                            </button>
                                        </li>
                                        @php $firstBranch = false; @endphp
                                    @endforeach
                                </ul>
                                
                                <!-- Branch Content -->
                                <div class="tab-content" id="todayBranchTabsContent">
                                    @php $firstBranch = true; @endphp
                                    @foreach($todayBranchVisits as $branchId => $branch)
                                        <div class="tab-pane fade {{ $firstBranch ? 'show active' : '' }}" 
                                             id="today-branch-{{ $branchId }}-content" 
                                             role="tabpanel" 
                                             aria-labelledby="today-branch-{{ $branchId }}-tab">
                                            
                                            <!-- Debug info -->
                                            <div class="mb-2 alert alert-info">
                                                <strong>Debug:</strong> This branch has {{ count($branch['visits']) }} visit(s)
                                                <br>
                                                <strong>Visit IDs:</strong> 
                                                @foreach($branch['visits'] as $debugVisit)
                                                    #{{ $debugVisit->id }} ({{ $debugVisit->visit_date }}, {{ $debugVisit->visit_time }}), 
                                                @endforeach
                                            </div>
                                            
                                            <div class="table-responsive">
                                                <table class="table mb-0 table-centered">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="py-3 border-0">Time</th>
                                                            <th class="py-3 border-0">Contract #</th>
                                                            <th class="py-3 border-0">Contract Type</th>
                                                            <th class="py-3 border-0">Customer</th>
                                                            <th class="py-3 border-0">Team</th>
                                                            <th class="py-3 border-0">Status</th>
                                                            <th class="py-3 border-0">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($branch['visits'] as $schedule)
                                                        <tr class="hover:bg-light">
                                                            <td class="py-3">{{ $schedule->visit_time }}</td>
                                                            <td class="py-3">
                                                                @if($schedule->contract)
                                                                <a href="{{ route('technical.contract.show', $schedule->contract->id) }}"
                                                                    class="text-primary fw-medium hover:text-primary-dark">
                                                                    {{ $schedule->contract->contract_number }}
                                                                </a>
                                                                @else
                                                                <span class="text-muted">N/A</span>
                                                                @endif
                                                            </td>
                                                            <td class="py-3">
                                                                @if($schedule->contract && $schedule->contract->type)
                                                                <div class="fw-medium">{{ $schedule->contract->type->name }}</div>
                                                                @else
                                                                <span class="text-muted">N/A</span>
                                                                @endif
                                                            </td>
                                                            <td class="py-3">
                                                                @if($schedule->contract && $schedule->contract->customer)
                                                                <div class="fw-medium">{{ $schedule->contract->customer->name }}</div>
                                                                @else
                                                                <span class="text-muted">N/A</span>
                                                                @endif
                                                            </td>
                                                            <td class="py-3">
                                                                <span class="badge bg-soft-info text-info">{{ $schedule->team->name }}</span>
                                                            </td>
                                                            <td class="py-3">
                                                                @if($schedule->status == 'scheduled')
                                                                <span class="badge bg-soft-warning text-warning">Scheduled</span>
                                                                @elseif($schedule->status == 'in_progress')
                                                                <span class="badge bg-soft-primary text-primary">In Progress</span>
                                                                @elseif($schedule->status == 'completed')
                                                                <span class="badge bg-soft-success text-success">Completed</span>
                                                                @elseif($schedule->status == 'cancelled')
                                                                <span class="badge bg-soft-danger text-danger">Cancelled</span>
                                                                @else
                                                                <span class="badge bg-soft-secondary text-secondary">{{ $schedule->status }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="py-3">
                                                                <div class="gap-2 d-flex">
                                                                    @if($schedule->status == 'scheduled')
                                                                    <button type="button" class="btn btn-sm btn-outline-primary edit-visit-btn" 
                                                                    data-visit-id="{{ $schedule->id }}"
                                                                    data-visit-date="{{ $schedule->visit_date }}" 
                                                                    data-visit-time="{{ $schedule->visit_time }}" 
                                                                    data-team-id="{{ $schedule->team_id }}"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#editModal">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                                    <form action="{{ route('technical.appointment.cancel', $schedule->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                        onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                    </form>
                                                                    @elseif($schedule->status == 'completed')
                                                                    <a href="{{ route('technical.visit.report.view', $schedule->id) }}"
                                                                        class="btn btn-sm btn-outline-info">
                                                                        <i class="fas fa-file-alt"></i> Report
                                                                    </a>
                                                                    @elseif($schedule->status == 'cancelled')
                                                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                                                        onclick="openRescheduleModal({{ $schedule->id }})">
                                                                        <i class="fas fa-calendar-plus"></i> Reschedule
                                                                    </button>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="6" class="py-4 text-center text-muted">No schedules for this branch today</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @php $firstBranch = false; @endphp
                                    @endforeach
                                </div>
                            @else
                                <div class="py-4 text-center">
                                    <p class="text-muted">No schedules for today</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Contracts with Upcoming Visits -->
            <div class="mb-4 row">
                <div class="col-12">
                    <div class="overflow-hidden border-0 card rounded-4"
                        style="box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%);">
                        <div class="p-4 bg-transparent card-header border-bottom">
                            <h5 class="mb-0 card-title fw-bold">Active Contracts with Upcoming Visits</h5>
                        </div>
                        <div class="p-4 card-body">
                            <div class="accordion" id="contractAccordion">
                                @forelse($activeContracts as $contract)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $contract->id }}">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $contract->id }}">
                                            <div class="d-flex justify-content-between w-100">
                                                <span>Contract #{{ $contract->contract_number }} - {{
                                                    $contract->customer->name }}</span>
                                                <span class="badge bg-primary">{{ $contract->visitSchedules->count() }}
                                                    Upcoming Visits</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $contract->id }}" class="accordion-collapse collapse"
                                        data-bs-parent="#contractAccordion">
                                        <div class="accordion-body">
                                            @php
                                                // Group upcoming visits by branch for this contract - with improved error handling
                                                $contractBranchVisits = [];
                                                // Filter to only include visits after today
                                                $today = \Carbon\Carbon::today();
                                                $upcomingVisits = $contract->visitSchedules->filter(function($visit) use ($today) {
                                                    return \Carbon\Carbon::parse($visit->visit_date)->startOfDay()->gt($today);
                                                });
                                                
                                                if($upcomingVisits->count() > 0) {
                                                    foreach($upcomingVisits as $visit) {
                                                        $branchId = $visit->branch_id ?? 0;
                                                        $branchName = $visit->branch_id ? 
                                                            ($visit->branch ? $visit->branch->branch_name : 'Unknown Branch') : 
                                                            'Main Branch';
                                                        
                                                        if(!isset($contractBranchVisits[$branchId])) {
                                                            $contractBranchVisits[$branchId] = [
                                                                'name' => $branchName,
                                                                'visits' => []
                                                            ];
                                                        }
                                                        
                                                        // Ensure this visit is added to the appropriate branch
                                                        $contractBranchVisits[$branchId]['visits'][] = $visit;
                                                    }
                                                }
                                            @endphp
                                            
                                            @if(count($contractBranchVisits) > 0)
                                                <!-- Branch Tabs for This Contract -->
                                                <ul class="mb-3 nav nav-tabs" id="contract-{{ $contract->id }}-branch-tabs" role="tablist">
                                                    @php $firstBranch = true; @endphp
                                                    @foreach($contractBranchVisits as $branchId => $branch)
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link {{ $firstBranch ? 'active' : '' }}" 
                                                                    id="contract-{{ $contract->id }}-branch-{{ $branchId }}-tab" 
                                                                    data-bs-toggle="tab" 
                                                                    data-bs-target="#contract-{{ $contract->id }}-branch-{{ $branchId }}-content" 
                                                                    type="button" 
                                                                    role="tab" 
                                                                    aria-controls="contract-{{ $contract->id }}-branch-{{ $branchId }}-content" 
                                                                    aria-selected="{{ $firstBranch ? 'true' : 'false' }}">
                                                                {{ $branch['name'] }}
                                                                <span class="badge bg-primary ms-2">{{ count($branch['visits']) }}</span>
                                                            </button>
                                                        </li>
                                                        @php $firstBranch = false; @endphp
                                                    @endforeach
                                                </ul>
                                                
                                                <!-- Branch Content for This Contract -->
                                                <div class="tab-content" id="contract-{{ $contract->id }}-branch-tabs-content">
                                                    @php $firstBranch = true; @endphp
                                                    @foreach($contractBranchVisits as $branchId => $branch)
                                                        <div class="tab-pane fade {{ $firstBranch ? 'show active' : '' }}" 
                                                             id="contract-{{ $contract->id }}-branch-{{ $branchId }}-content" 
                                                             role="tabpanel" 
                                                             aria-labelledby="contract-{{ $contract->id }}-branch-{{ $branchId }}-tab">
                                                            
                                                            <!-- Debug info -->
                                                            <div class="mb-2 alert alert-info">
                                                                <strong>Debug:</strong> This branch has {{ count($branch['visits']) }} visit(s)
                                                            </div>
                                                            
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th>Time</th>
                                                                            <th>Contract #</th>
                                                                            <th>Contract Type</th>
                                                                            <th>Customer</th>
                                                                            <th>Team</th>
                                                                            <th>Status</th>
                                                                            <th>Actions</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($branch['visits'] as $visit)
                                                                        <tr>
                                                                            <td class="py-3">
                                                                                <div class="text-wrap" style="max-width: 200px;">
                                                                                    {{ \Carbon\Carbon::parse($visit->visit_date)->format('Y-m-d') }}
                                                                                </div>
                                                                            </td>
                                                                            <td class="py-3">
                                                                                <div class="text-wrap" style="max-width: 200px;">
                                                                                    {{ $visit->visit_time }}
                                                                                </div>
                                                                            </td>
                                                                            <td class="py-3">
                                                                                @if($visit->contract)
                                                                                <a href="{{ route('technical.contract.show', $visit->contract->id) }}"
                                                                                    class="text-primary fw-medium hover:text-primary-dark">
                                                                                    {{ $visit->contract->contract_number }}
                                                                                </a>
                                                                                @else
                                                                                    <span class="text-muted">N/A</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="py-3">
                                                                                @if($visit->contract && $visit->contract->type)
                                                                                    <div class="fw-medium">{{ $visit->contract->type->name }}</div>
                                                                                @else
                                                                                    <span class="text-muted">N/A</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="py-3">
                                                                                {{ $visit->contract->customer->name }}
                                                                            </td>
                                                                            <td class="py-3">
                                                                                <span class="badge bg-soft-info text-info">{{ $visit->team->name }}</span>
                                                                            </td>
                                                                            <td class="py-3">
                                                                                @if($visit->status == 'scheduled')
                                                                                <span class="badge bg-soft-warning text-warning">Scheduled</span>
                                                                                @elseif($visit->status == 'in_progress')
                                                                                <span class="badge bg-soft-primary text-primary">In Progress</span>
                                                                                @elseif($visit->status == 'completed')
                                                                                <span class="badge bg-soft-success text-success">Completed</span>
                                                                                @elseif($visit->status == 'cancelled')
                                                                                <span class="badge bg-soft-danger text-danger">Cancelled</span>
                                                                                @else
                                                                                <span class="badge bg-soft-secondary text-secondary">{{ $visit->status }}</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="py-3">
                                                                                <div class="gap-2 d-flex">
                                                                                    @if($visit->status == 'scheduled')
                                                                                        <button type="button" class="btn btn-sm btn-outline-primary edit-visit-btn" 
                                                                                            data-visit-id="{{ $visit->id }}"
                                                                                            data-visit-date="{{ $visit->visit_date }}" 
                                                                                            data-visit-time="{{ $visit->visit_time }}" 
                                                                                            data-team-id="{{ $visit->team_id }}"
                                                                                            data-bs-toggle="modal" 
                                                                                            data-bs-target="#editModal">
                                                                                            <i class="fas fa-edit"></i> Edit
                                                                                        </button>
                                                                                        <form action="{{ route('technical.appointment.cancel', $visit->id) }}" method="POST">
                                                                                            @csrf
                                                                                            @method('PUT')
                                                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                                                    onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                                                                    <i class="fas fa-times"></i> Cancel
                                                                                            </button>
                                                                                        </form>
                                                                                    @elseif($visit->status == 'completed')
                                                                                        <a href="{{ route('technical.visit.report.view', $visit->id) }}"
                                                                                            class="btn btn-sm btn-outline-info">
                                                                                            <i class="fas fa-file-alt"></i> View Report
                                                                                        </a>
                                                                                    @elseif($visit->status == 'cancelled')
                                                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                                            onclick="openRescheduleModal({{ $visit->id }})">
                                                                                            <i class="fas fa-calendar-plus"></i> Reschedule
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        @php $firstBranch = false; @endphp
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="py-3 text-center">
                                                    <p class="text-muted">No upcoming visits for this contract</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center">
                                    <p>No active contracts with upcoming visits</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teams Overview -->
            <div class="mb-4 row">
                <div class="col-12">
                    <div class="overflow-hidden border-0 card rounded-4"
                        style="box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%);">
                        <div class="p-4 bg-transparent card-header border-bottom">
                            <h5 class="mb-0 card-title fw-bold">Teams Overview</h5>
                        </div>
                        <div class="p-4 card-body">
                            <div class="table-responsive">
                                <table class="table mb-0 table-hover">
                                    <thead>
                                        <tr>
                                            <th>Team Name</th>
                                            <th>Leader</th>
                                            <th>Members</th>
                                            <th>Today's Tasks</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($teams as $team)
                                        <tr>
                                            <td>{{ $team->name }}</td>
                                            <td>{{ $team->leader->name }}</td>
                                            <td>{{ $team->members->count() }}</td>
                                            <td>
                                                {{ $teamSchedules->where('team_id', $team->id)->count() }}
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Active</span>
                                            </td>
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
    </div>
</div>
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3 form-group">
                        <label for="edit_visit_date">Visit Date</label>
                        <input type="date" class="form-control" id="edit_visit_date" name="visit_date" required
                            min="{{ date('Y-m-d', strtotime('today')) }}">
                    </div>
                    <div class="mb-3 form-group">
                        <label for="edit_visit_time">Visit Time</label>
                        <input type="time" class="form-control" id="edit_visit_time" name="visit_time" required>
                        <small class="form-text text-muted">Select a time between 8:00 AM and 4:00 PM (visits take 2
                            hours)</small>
                    </div>
                    <div class="mb-3 form-group">
                        <label for="edit_team_id">Assign Team</label>
                        <select class="form-control" id="edit_team_id" name="team_id" required>
                            <option value="">Select Team</option>
                            @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" role="dialog" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rescheduleModalLabel">Reschedule Cancelled Visit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rescheduleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3 form-group">
                        <label for="new_visit_date">New Visit Date</label>
                        <input type="date" class="form-control" id="new_visit_date" name="visit_date" required
                               min="{{ date('Y-m-d', strtotime('today')) }}">
                    </div>

                    <div class="mb-3 form-group">
                        <label for="new_visit_time">New Visit Time</label>
                        <input type="time" class="form-control" id="new_visit_time" name="visit_time" required>
                        <small class="form-text text-muted">Select a time between 8:00 AM and 4:00 PM (visits take 2 hours)</small>
                    </div>

                    <div class="mb-3 form-group">
                        <label for="new_team_id">Assign Team</label>
                        <select class="form-control" id="new_team_id" name="team_id" required>
                            <option value="">Select Team</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Reschedule Visit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        $('.edit-visit-btn').on('click', function() {
            const visitId = $(this).data('visit-id');
            const visitDate = $(this).data('visit-date');
            const visitTime = $(this).data('visit-time');
            const teamId = $(this).data('team-id');
            
            const form = document.getElementById('editForm');
            form.action = "{{ url('/technical/appointments') }}/" + visitId + "/edit";
            
            document.getElementById('edit_visit_date').value = visitDate;
            document.getElementById('edit_visit_time').value = visitTime;
            document.getElementById('edit_team_id').value = teamId;
        });
        
        // Function to open the reschedule modal
        window.openRescheduleModal = function(visitId) {
            const form = document.getElementById('rescheduleForm');
            form.action = "{{ url('/technical/visits') }}/" + visitId + "/reschedule";
            $('#rescheduleModal').modal('show');
        };
        
        // Prevent scheduling on Fridays
        document.getElementById('new_visit_date').addEventListener('change', function(e) {
            const date = new Date(this.value);
            if (date.getDay() === 5) { // 5 is Friday
                alert('Visits cannot be scheduled on Fridays. Please select another day.');
                this.value = '';
            }
        });
    });
</script>
@endpush
@endsection