@extends('shared.dashboard')
@section('content')
    <div class="page-content">
        @if (session('error'))
            <div class="mb-3 alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-1"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-3 alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0">Team Visit Schedules</h6>
                    </div>
                    <div class="gap-3 d-flex align-items-center">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary" id="todayBtn">Today</button>
                            <button type="button" class="btn btn-outline-primary" id="weekBtn">This Week</button>
                            <button type="button" class="btn btn-outline-primary" id="monthBtn">This Month</button>
                        </div>
                        <form action="{{ route('technical.team.schedules') }}" method="GET" class="gap-2 d-flex">
                            <div class="gap-2 d-flex">
                                <input type="date" name="from_date" class="form-control" placeholder="From Date"
                                    value="{{ request('from_date') }}" style="width: auto;">
                                <input type="date" name="to_date" class="form-control" placeholder="To Date"
                                    value="{{ request('to_date') }}" style="width: auto;">
                            </div>
                            <span class="text-muted">or</span>
                            <select name="month" class="form-select" style="width: auto;">
                                <option value="">Select Month</option>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}"
                                        {{ request('month', now()->month) == $month ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="year" class="form-select" style="width: auto;">
                                <option value="">Select Year</option>
                                @foreach (range(now()->year - 1, now()->year + 1) as $year)
                                    <option value="{{ $year }}"
                                        {{ request('year', now()->year) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            @if (request()->hasAny(['month', 'year', 'date', 'from_date', 'to_date']))
                                <a href="{{ route('technical.team.schedules') }}" class="btn btn-secondary">Reset</a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="card-body border-bottom">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="text-white card bg-primary">
                            <div class="card-body">
                                <h6 class="card-title">Today's Visits</h6>
                                <h3 class="mb-0 card-text">
                                    {{ $teams->sum(function ($team) {
                                        return $team->visitSchedules->where('visit_date', date('Y-m-d'))->count();
                                    }) }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-white card bg-success">
                            <div class="card-body">
                                <h6 class="card-title">Completed Today</h6>
                                <h3 class="mb-0 card-text">
                                    {{ $teams->sum(function ($team) {
                                        return $team->visitSchedules->where('visit_date', date('Y-m-d'))->where('status', 'completed')->count();
                                    }) }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-white card bg-warning">
                            <div class="card-body">
                                <h6 class="card-title">Pending Visits</h6>
                                <h3 class="mb-0 card-text">
                                    {{ $teams->sum(function ($team) {
                                        return $team->visitSchedules->where('status', 'scheduled')->count();
                                    }) }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-white card bg-info">
                            <div class="card-body">
                                <h6 class="card-title">Active Teams</h6>
                                <h3 class="mb-0 card-text">{{ $teams->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @foreach ($teams as $team)
                    <div class="mb-4 card">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">
                                        <i class="bx bx-group me-2"></i>
                                        {{ $team->name }}
                                        <span class="text-muted ms-2">|</span>
                                        <small class="text-muted ms-2">
                                            <i class="bx bx-user me-1"></i>Led by {{ $team->leader->name }}
                                        </small>
                                    </h6>
                                </div>
                                <div class="gap-2 d-flex align-items-center">
                                    <span class="badge bg-primary">
                                        <i class="bx bx-calendar me-1"></i>
                                        {{ $team->totalSchedules }} Visits
                                    </span>
                                    <span class="badge bg-success">
                                        <i class="bx bx-check-circle me-1"></i>
                                        {{ $team->visitSchedules->where('status', 'completed')->count() }} Completed
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($team->totalSchedules > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Visit Time</th>
                                                <th>Client</th>
                                                <th>Contract</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($team->paginatedSchedules as $schedule)
                                                <tr>
                                                    <td>{{ date('M d, Y', strtotime($schedule->visit_date)) }}
                                                        {{ date('h:i A', strtotime($schedule->visit_time)) }}</td>
                                                    <td>{{ $schedule->contract->customer->name }}</td>
                                                    <td><a
                                                            href="{{ route('technical.contract.show', ['id' => $schedule->contract->id]) }}">{{ $schedule->contract->contract_number }}</a>
                                                    </td>
                                                    <td>
                                                        @if ($schedule->status == 'completed')
                                                            <span class="badge bg-success">Completed</span>
                                                        @elseif($schedule->status == 'cancelled')
                                                            <span class="badge bg-danger">Cancelled</span>
                                                        @else
                                                            <span class="badge bg-warning">Scheduled</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (in_array($schedule->status, ['scheduled', 'cancelled']))
                                                            <button type="button" class="btn btn-sm btn-primary"
                                                                onclick="rescheduleVisit({{ $schedule->id }})">
                                                                <i class="bx bx-calendar-edit"></i> Reschedule
                                                            </button>
                                                        @elseif($schedule->status == 'completed')
                                                            <button type="button" class="btn btn-sm btn-outline-success">
                                                                <a href="{{ route('technical.visit.report.view', $schedule->id) }}">
                                                                    <i class="bx bx-calendar-check"></i> View Report
                                                                </a>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="mt-3 d-flex justify-content-end">
                                        {{ $team->paginatedSchedules->appends(['page_' . $team->id => request('page_' . $team->id)])->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            @else
                                <div class="py-4 text-center">
                                    <i class="bx bx-calendar-x text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-2 text-muted">No visits scheduled for this team.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Reschedule Visit Modal -->
    <div class="modal fade" id="rescheduleVisitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-calendar-edit me-1"></i>
                        Reschedule Visit
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rescheduleForm" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_date" class="form-label">New Date</label>
                            <input type="date" class="form-control" id="new_date" name="visit_date" required
                                min="{{ date('Y-m-d') }}">
                            <small class="text-muted">Select any date from today onwards</small>
                        </div>
                        <div class="mb-3">
                            <label for="new_time" class="form-label">New Time</label>
                            <input type="time" class="form-control" id="new_time" name="visit_time" required>
                            <small class="text-muted">Regular working hours are 8:00 AM to 2:00 PM</small>
                        </div>
                        <div id="schedule_warnings" class="alert alert-warning d-none">
                            <ul class="mb-0" id="warning_list"></ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check me-1"></i>Reschedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Visit Modal -->
    <div class="modal fade" id="createVisitModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-calendar-plus me-1"></i>
                        Create New Visit
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('technical.schedule_visit') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Client Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Client</label>
                                <select class="form-select" name="client_id" id="client_select" required>
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Contract Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Contract</label>
                                <select class="form-select" name="contract_id" id="contract_select" required disabled>
                                    <option value="">Select Contract</option>
                                </select>
                            </div>

                            <!-- Branch Selection (if applicable) -->
                            <div class="col-md-6">
                                <label class="form-label">Branch (Optional)</label>
                                <select class="form-select" name="branch_id" id="branch_select" disabled>
                                    <option value="">Select Branch</option>
                                </select>
                            </div>

                            <!-- Visit Type -->
                            <div class="col-md-6">
                                <label class="form-label">Visit Type</label>
                                <select class="form-select" name="visit_type" required>
                                    <option value="regular">Regular Visit</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>

                            <!-- Date Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Visit Date</label>
                                <input type="date" class="form-control" name="visit_date" required
                                    min="{{ date('Y-m-d') }}">
                                <small class="text-muted">Select any date from today onwards</small>
                            </div>

                            <!-- Time Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Visit Time</label>
                                <input type="time" class="form-control" name="visit_time" required>
                                <small class="text-muted">Regular working hours are 8:00 AM to 2:00 PM</small>
                            </div>

                            <!-- Team Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Assign Team</label>
                                <select class="form-select" name="team_id" required>
                                    <option value="">Select Team</option>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Warning Messages -->
                        <div id="schedule_warnings" class="mt-3 alert alert-warning d-none">
                            <ul class="mb-0" id="warning_list"></ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check me-1"></i>Create Visit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Quick date filter buttons
                document.getElementById('todayBtn').addEventListener('click', function() {
                    window.location.href = '{{ route('technical.team.schedules') }}?date=' + new Date()
                        .toISOString().split('T')[0];
                });

                document.getElementById('weekBtn').addEventListener('click', function() {
                    const today = new Date();
                    // Create a new date object for the start of week to avoid modifying the original date
                    const startOfWeek = new Date(today);
                    startOfWeek.setDate(today.getDate() - today.getDay()); // Sunday is 0, Monday is 1, etc.
                    
                    // Create a new date object for the end of week
                    const endOfWeek = new Date(today);
                    endOfWeek.setDate(today.getDate() + (6 - today.getDay())); // Set to Saturday
                    
                    window.location.href =
                        `{{ route('technical.team.schedules') }}?from_date=${startOfWeek.toISOString().split('T')[0]}&to_date=${endOfWeek.toISOString().split('T')[0]}`;
                });

                document.getElementById('monthBtn').addEventListener('click', function() {
                    const today = new Date();
                    window.location.href = '{{ route('technical.team.schedules') }}?month=' + (today
                        .getMonth() + 1) + '&year=' + today.getFullYear();
                });
                
                // Clear other filters when using date range
                const fromDateInput = document.querySelector('input[name="from_date"]');
                const toDateInput = document.querySelector('input[name="to_date"]');
                const monthSelect = document.querySelector('select[name="month"]');
                const yearSelect = document.querySelector('select[name="year"]');
                
                // When date range inputs change, clear month/year selections
                fromDateInput.addEventListener('change', function() {
                    if (this.value) {
                        monthSelect.value = '';
                        // Keep year as it might be relevant for the date range
                    }
                });
                
                toDateInput.addEventListener('change', function() {
                    if (this.value) {
                        monthSelect.value = '';
                        // Keep year as it might be relevant for the date range
                    }
                });
                
                // When month/year selections change, clear date range inputs
                monthSelect.addEventListener('change', function() {
                    if (this.value) {
                        fromDateInput.value = '';
                        toDateInput.value = '';
                    }
                });
                
                yearSelect.addEventListener('change', function() {
                    // Only clear date inputs if month is also selected
                    if (this.value && monthSelect.value) {
                        fromDateInput.value = '';
                        toDateInput.value = '';
                    }
                });
            });

            function rescheduleVisit(visitId) {
                const modal = new bootstrap.Modal(document.getElementById('rescheduleVisitModal'));
                const form = document.getElementById('rescheduleForm');
                form.action = `/technical/visits/${visitId}/reschedule`;

                // Reset form and warnings
                form.reset();
                document.getElementById('schedule_warnings').classList.add('d-none');
                document.getElementById('warning_list').innerHTML = '';

                // Set min date to today
                document.getElementById('new_date').min = new Date().toISOString().split('T')[0];

                modal.show();
            }

            function checkScheduleWarnings() {
                const warnings = [];
                const timeInput = document.getElementById('new_time');
                const dateInput = document.getElementById('new_date');
                const warningsList = document.getElementById('warning_list');
                const warningsContainer = document.getElementById('schedule_warnings');

                // Check time
                if (timeInput.value) {
                    const [hours, minutes] = timeInput.value.split(':').map(Number);
                    if (hours < 8 || hours >= 14 || (hours === 14 && minutes > 0)) {
                        warnings.push('This time is outside regular working hours (8:00 AM to 2:00 PM)');
                    }
                }

                // Check date
                if (dateInput.value) {
                    const date = new Date(dateInput.value);
                    if (date.getDay() === 5) {
                        warnings.push('This appointment is scheduled for a Friday');
                    }
                }

                // Update warnings display
                if (warnings.length > 0) {
                    warningsList.innerHTML = warnings.map(w => `<li>${w}</li>`).join('');
                    warningsContainer.classList.remove('d-none');
                } else {
                    warningsContainer.classList.add('d-none');
                }
            }

            // Add event listeners for validation
            document.getElementById('new_time').addEventListener('change', checkScheduleWarnings);
            document.getElementById('new_date').addEventListener('change', checkScheduleWarnings);
        </script>
    @endpush
@endsection
