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

        <div class="container-fluid">
            <!-- Header Section with Breadcrumbs and Actions -->
            <div class="flex-wrap mb-3 d-flex justify-content-between align-items-center">
                <div class="mb-2 mb-md-0">
                    <h4 class="mb-1 fw-bold">
                        <i class="bx bx-calendar me-1 text-primary"></i>
                        {{ $contract->customer->name }} - {{ $contract->contract_number }}
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="p-0 mb-0 breadcrumb breadcrumb-light">
                            <li class="breadcrumb-item"><a
                                    href="{{ route('technical.scheduled-appointments') }}">Appointments</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('technical.contract.show', $contract->id) }}">Contract</a></li>
                            <li class="breadcrumb-item active">
                                @if (isset($branch))
                                    {{ $branch->branch_name }} Visits
                                @else
                                    All Visits
                                @endif
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="flex-wrap gap-2 d-flex">
                    <a href="{{ route('technical.contract.show', $contract->id) }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Back to Contract
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#createVisitModal">
                        <i class="bx bx-plus me-1"></i> New Visit
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="mb-4 row">
                <div class="col-md-3">
                    <div class="border-0 shadow-sm card hover-shadow">
                        <div class="p-3 card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div
                                        class="avatar-md rounded-circle bg-soft-primary d-flex align-items-center justify-content-center">
                                        <i class="bx bx-calendar-check text-primary font-size-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="mb-1">{{ $totalVisits }}</h4>
                                    <p class="mb-0 text-muted font-size-14">Total Visits</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-0 shadow-sm card hover-shadow">
                        <div class="p-3 card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div
                                        class="avatar-md rounded-circle bg-soft-success d-flex align-items-center justify-content-center">
                                        <i class="bx bx-check-circle text-success font-size-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="mb-1">{{ $completedVisits }}</h4>
                                    <p class="mb-0 text-muted font-size-14">Completed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-0 shadow-sm card hover-shadow">
                        <div class="p-3 card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div
                                        class="avatar-md rounded-circle bg-soft-warning d-flex align-items-center justify-content-center">
                                        <i class="bx bx-time text-warning font-size-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="mb-1">{{ $pendingVisits }}</h4>
                                    <p class="mb-0 text-muted font-size-14">Scheduled</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-0 shadow-sm card hover-shadow">
                        <div class="p-3 card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div
                                        class="avatar-md rounded-circle bg-soft-danger d-flex align-items-center justify-content-center">
                                        <i class="bx bx-x-circle text-danger font-size-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="mb-1">{{ $cancelledVisits }}</h4>
                                    <p class="mb-0 text-muted font-size-14">Cancelled</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-4 card">
                <div class="card-body">
                    <h5 class="mb-3 card-title">Filter Visits</h5>
                    <form method="GET"
                        action="{{ isset($branch) ? route('technical.contract.branch.visits', [$contract->id, $branch->id]) : route('technical.contract.visits', $contract->id) }}"
                        class="row g-3">
                        <!-- Date Range -->
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                        </div>

                        <!-- Status Filter -->
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Statuses</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>
                                    Scheduled</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In
                                    Progress</option>
                            </select>
                        </div>

                        <!-- Branch Filter (if not already filtered by branch) -->
                        @if (!isset($branch))
                            <div class="col-md-3">
                                <label class="form-label">Branch</label>
                                <select class="form-select" name="branch_id" id="branch_filter">
                                    <option value="">All Locations</option>
                                    @php
                                        $mainLocationVisits = $contract->visitSchedules
                                            ->whereNull('branch_id')
                                            ->count();
                                    @endphp
                                    @if ($mainLocationVisits > 0)
                                        <option value="main" {{ request('branch_id') == 'main' ? 'selected' : '' }}>
                                            Main Location ({{ $mainLocationVisits }})
                                        </option>
                                    @endif
                                    @foreach ($contract->branchs as $branchItem)
                                        <option value="{{ $branchItem->id }}"
                                            {{ request('branch_id') == $branchItem->id ? 'selected' : '' }}>
                                            {{ $branchItem->branch_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Submit/Reset -->
                        <div class="gap-2 col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ isset($branch) ? route('technical.contract.branch.visits', [$contract->id, $branch->id]) : route('technical.contract.visits', $contract->id) }}"
                                class="btn btn-secondary">
                                Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Visits Grid -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 card-title">
                        <i class="bx bx-list-ul me-1"></i>
                        @if (isset($branch))
                            {{ $branch->branch_name }} Visits
                        @else
                            All Contract Visits
                        @endif
                        <span class="ms-1 badge rounded-pill bg-info">{{ $visits->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="visits-card-grid">
                        @if ($visits->count() > 0)
                            <div class="row g-3">
                                @foreach ($visits as $visit)
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="border shadow-sm visit-card h-100 rounded-3">
                                            <div class="p-3">
                                                <!-- Visit header with number and date -->
                                                <div class="mb-3 d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-0 d-flex align-items-center">
                                                            <i class="bx bx-calendar me-1 text-primary"></i>
                                                            Visit #{{ $visit->visit_number ?? 'N/A' }}
                                                        </h6>
                                                        <div class="mt-1 text-dark">
                                                            {{ \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') }}
                                                        </div>
                                                        <div class="text-muted font-size-13">
                                                            {{ \Carbon\Carbon::parse($visit->visit_time)->format('h:i A') }}
                                                        </div>
                                                    </div>

                                                    <!-- Status badge -->
                                                    <div>
                                                        @if ($visit->status == 'scheduled')
                                                            <div
                                                                class="px-2 py-1 d-inline-flex rounded-pill bg-soft-warning">
                                                                <i class="bx bx-time-five text-warning me-1"></i>
                                                                <span class="stat-label">Scheduled</span>
                                                            </div>
                                                        @elseif($visit->status == 'completed')
                                                            <div
                                                                class="px-2 py-1 d-inline-flex rounded-pill bg-soft-success">
                                                                <i class="bx bx-check-circle text-success me-1"></i>
                                                                <span class="stat-label">Completed</span>
                                                            </div>
                                                        @elseif($visit->status == 'cancelled')
                                                            <div
                                                                class="px-2 py-1 d-inline-flex rounded-pill bg-soft-danger">
                                                                <i class="bx bx-x-circle text-danger me-1"></i>
                                                                <span class="stat-label">Cancelled</span>
                                                            </div>
                                                        @elseif($visit->status == 'in_progress')
                                                            <div
                                                                class="px-2 py-1 d-inline-flex rounded-pill bg-soft-primary">
                                                                <i class="bx bx-loader-alt text-primary me-1"></i>
                                                                <span class="stat-label">In Progress</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Branch information -->
                                                @if ($visit->branch)
                                                    <div class="py-2 mb-3 border-top border-bottom">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bx bx-map-pin me-2 text-muted"></i>
                                                            <span>{{ $visit->branch->branch_name }}</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Team information -->
                                                <div class="py-2 mb-3 border-top border-bottom">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bx bx-group me-2 text-muted"></i>
                                                        <span>{{ $visit->team ? $visit->team->name : 'Not Assigned' }}</span>
                                                    </div>
                                                </div>

                                                <!-- Action buttons -->
                                                <div class="gap-2 d-flex">
                                                    @if ($visit->status == 'scheduled')
                                                        <button type="button" class="btn btn-soft-primary btn-sm w-100"
                                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                                            data-visit-id="{{ $visit->id }}"
                                                            data-visit-date="{{ $visit->visit_date }}"
                                                            data-visit-time="{{ $visit->visit_time }}"
                                                            data-team-id="{{ $visit->team_id }}"
                                                            data-contract-id="{{ $visit->contract_id }}"
                                                            data-branch-id="{{ $visit->branch_id }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-soft-danger btn-sm w-100 cancelVisit"
                                                            data-visit-id="{{ $visit->id }}">
                                                            <i class="bx bx-x-circle me-1"></i> Cancel
                                                        </button>
                                                    @elseif($visit->status == 'in_progress')
                                                        <a href="{{ route('technical.visit.report.view', $visit->id) }}"
                                                            class="btn btn-soft-primary btn-sm w-100">
                                                            <i class="bx bx-file me-1"></i> Report
                                                        </a>
                                                    @elseif($visit->status == 'completed')
                                                        <a href="{{ route('technical.visit.report.view', $visit->id) }}"
                                                            class="btn btn-soft-success btn-sm w-100">
                                                            <i class="bx bx-file me-1"></i> View Report
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4 d-flex justify-content-center">
                                {{ $visits->links('vendor.pagination.custom') }}
                            </div>
                        @else
                            <div class="py-5 text-center">
                                <div class="empty-state">
                                    <i class="mb-2 bx bx-calendar-x text-muted" style="font-size: 3rem;"></i>
                                    <h5>No Visits Found</h5>
                                    <p class="mb-3 text-muted">There are no visits matching your current filters.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#createVisitModal">
                                        <i class="bx bx-plus me-1"></i> Create New Visit
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
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
                                <!-- Hidden contract ID -->
                                <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                                <input type="hidden" name="client_id" value="{{ $contract->customer_id }}">

                                <!-- Branch Selection (if applicable) -->
                                <div class="col-md-6">
                                    <label class="form-label">Branch (Optional)</label>
                                    <select class="form-select" name="branch_id" id="branch_select">
                                        <option value="">Select Branch</option>
                                        @foreach ($contract->branchs as $branchItem)
                                            <option value="{{ $branchItem->id }}"
                                                {{ isset($branch) && $branch->id == $branchItem->id ? 'selected' : '' }}>
                                                {{ $branchItem->branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Visit Type -->
                                <div class="col-md-6">
                                    <label class="form-label">Visit Type</label>
                                    <select class="form-select" name="visit_type" required>
                                        <option value="regular">Regular</option>
                                        <option value="complementary">Complementary</option>
                                        <option value="free">Free</option>
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

        <!-- Edit Visit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Visit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3 form-group">
                                <label for="edit_visit_date">Visit Date</label>
                                <input type="date" class="form-control" id="edit_visit_date" name="visit_date"
                                    required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="mb-3 form-group">
                                <label for="edit_visit_time">Visit Time</label>
                                <input type="time" class="form-control" id="edit_visit_time" name="visit_time"
                                    required>
                                <small class="form-text text-muted">Select a time between 8:00 AM and 4:00 PM</small>
                            </div>
                            <div class="mb-3 form-group">
                                <label for="edit_team_id">Assign Team</label>
                                <select class="form-control" id="edit_team_id" name="team_id" required>
                                    <option value="">Select Team</option>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Visit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Visits Card Grid Styles */
        .visits-card-grid {
            margin-top: 1rem;
        }

        .visit-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            background-color: #fff;
        }

        .visit-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(149, 157, 165, 0.15) !important;
        }

        .visit-card .btn {
            transition: all 0.2s ease-in-out;
        }

        /* Status badges in cards */
        .visit-card .rounded-pill {
            font-size: 0.75rem;
            white-space: nowrap;
            padding: 0.25rem 0.75rem;
        }

        .visits-card-grid .empty-state {
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            gap: 4px;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .page-item {
            margin: 0;
            list-style: none;
        }

        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.5;
            color: var(--bs-primary);
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
        }

        .page-link:hover {
            color: var(--bs-primary);
            background-color: #f8fafc;
            border-color: #e2e8f0;
            z-index: 2;
        }

        .page-item.active .page-link {
            color: #fff;
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            box-shadow: 0 2px 4px rgba(var(--bs-primary-rgb), 0.15);
        }

        .page-item.disabled .page-link {
            color: #94a3b8;
            background-color: #f8fafc;
            border-color: #e2e8f0;
            cursor: not-allowed;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .visits-card-grid .row {
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }

            .visits-card-grid .col-md-6 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .visit-card {
                margin-bottom: 1rem;
            }

            .pagination {
                flex-wrap: wrap;
                justify-content: center;
            }

            .page-link {
                min-width: 28px;
                height: 28px;
                font-size: 0.8125rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Branch filter change handler (redirect to branch-specific URL)
            $('#branch_filter').on('change', function() {
                var branchId = $(this).val();
                var baseUrl = "{{ route('technical.contract.visits', $contract->id) }}";

                if (branchId) {
                    if (branchId === 'main') {
                        window.location.href =
                            "{{ route('technical.contract.branch.visits', [$contract->id, 'main']) }}";
                    } else {
                        window.location.href =
                            "{{ route('technical.contract.branch.visits', [$contract->id, ':branchId']) }}"
                            .replace(':branchId', branchId);
                    }
                } else {
                    window.location.href = baseUrl;
                }
            });

            // Edit modal data loading
            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var visitId = button.data('visit-id');
                var visitDate = button.data('visit-date');
                var visitTime = button.data('visit-time');
                var teamId = button.data('team-id');

                var modal = $(this);

                // Set data in the modal
                modal.find('#edit_visit_date').val(visitDate);
                modal.find('#edit_visit_time').val(visitTime);
                modal.find('#edit_team_id').val(teamId);

                // Update the form action
                var formAction = "{{ route('technical.appointment.edit', ':id') }}";
                formAction = formAction.replace(':id', visitId);

                modal.find('#editForm').attr('action', formAction);
            });

            // Cancel Visit confirmation
            $('.cancelVisit').on('click', function() {
                var visitId = $(this).data('visit-id');

                if (confirm('Are you sure you want to cancel this visit?')) {
                    var cancelUrl = "{{ route('technical.appointment.cancel', ':id') }}";
                    cancelUrl = cancelUrl.replace(':id', visitId);

                    // Create a form and submit it
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': cancelUrl
                    });

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': '{{ csrf_token() }}'
                    }));

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_method',
                        'value': 'PUT'
                    }));

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'status',
                        'value': 'cancelled'
                    }));

                    $('body').append(form);
                    form.submit();
                }
            });

            // Visit time and date change handlers for warnings
            document.querySelector('#createVisitModal input[name="visit_time"]').addEventListener('change',
                checkScheduleWarnings);
            document.querySelector('#createVisitModal input[name="visit_date"]').addEventListener('change',
                checkScheduleWarnings);
        });

        // Schedule warnings check
        function checkScheduleWarnings() {
            const visitDate = document.querySelector('#createVisitModal input[name="visit_date"]').value;
            const visitTime = document.querySelector('#createVisitModal input[name="visit_time"]').value;
            const warningsDiv = document.getElementById('schedule_warnings');
            const warningsList = document.getElementById('warning_list');
            const warnings = [];

            if (visitDate && visitTime) {
                const date = new Date(visitDate);
                const time = new Date(`2000-01-01T${visitTime}`);
                const hours = time.getHours();

                // Check if it's Friday
                if (date.getDay() === 5) {
                    warnings.push('This visit is scheduled for a Friday. Please confirm if this is intentional.');
                }

                // Check if it's outside regular hours (8 AM - 2 PM)
                if (hours < 8 || hours >= 14) {
                    warnings.push('This visit is scheduled outside regular working hours (8:00 AM - 2:00 PM).');
                }
            }

            if (warnings.length > 0) {
                warningsList.innerHTML = warnings.map(warning => `<li>${warning}</li>`).join('');
                warningsDiv.classList.remove('d-none');
            } else {
                warningsDiv.classList.add('d-none');
            }
        }
    </script>
@endpush
