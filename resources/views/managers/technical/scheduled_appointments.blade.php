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
            <div class="mb-4 row">
                <div class="col-md-4">
                    <div class="border-0 shadow-sm card hover-shadow">
                        <div class="p-4 card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div
                                        class="avatar-md rounded-circle bg-soft-primary d-flex align-items-center justify-content-center">
                                        <i class="bx bx-time-five text-primary font-size-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="mb-1">{{ $pendingVisits }}</h4>
                                    <p class="mb-0 text-muted font-size-14">Pending Visits</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border-0 shadow-sm card hover-shadow">
                        <div class="p-4 card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div
                                        class="avatar-md rounded-circle bg-soft-success d-flex align-items-center justify-content-center">
                                        <i class="bx bx-calendar-check text-success font-size-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="mb-1">{{ $todayVisits }}</h4>
                                    <p class="mb-0 text-muted font-size-14">Today's Visits</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border-0 shadow-sm card hover-shadow">
                        <div class="p-4 card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div
                                        class="avatar-md rounded-circle bg-soft-info d-flex align-items-center justify-content-center">
                                        <i class="bx bx-check-circle text-info font-size-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="mb-1">{{ $todayCompletedVisits }}</h4>
                                    <p class="mb-0 text-muted font-size-14">Completed Today</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-0">Scheduled Appointments</h6>
                                </div>
                                <div class="gap-2 d-flex align-items-center">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#createVisitModal">
                                        <i class="bx bx-plus me-1"></i> Create New Visit
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <form method="GET" action="{{ route('technical.scheduled-appointments') }}" class="mb-4"
                                id="filterForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date"
                                                value="{{ request('start_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="end_date">End Date</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date"
                                                value="{{ request('end_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="team_id">Team</label>
                                            <select class="form-control" id="team_id" name="team_id">
                                                <option value="">All Teams</option>
                                                @foreach ($teams as $team)
                                                    <option value="{{ $team->id }}"
                                                        {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                                        {{ $team->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="contract_number">Contract Number</label>
                                            <input type="text" class="form-control" id="contract_number"
                                                name="contract_number" value="{{ request('contract_number') }}"
                                                placeholder="Enter contract #">
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="mb-0 form-group">
                                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                                            <a href="{{ route('technical.scheduled-appointments') }}"
                                                class="btn btn-secondary">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="border-0 shadow-sm card">
                        <div class="p-0 card-body">
                            <div class="accordion custom-accordion" id="contractAccordion">
                                @foreach ($contracts as $contract)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#contract-{{ $contract->id }}">
                                                <div class="contract-info w-100">
                                                    <div
                                                        class="flex-wrap d-flex justify-content-between align-items-start w-100">
                                                        <div class="contract-main-info">
                                                            <h5 class="gap-2 contract-title d-flex align-items-center">
                                                                <i class="bx bx-buildings me-1 text-primary"></i>
                                                                {{ $contract->customer->name }}
                                                                <a href="{{ route('technical.contract.show', $contract->id) }}"
                                                                    class="p-0 btn btn-link btn-sm text-muted view-contract-btn"
                                                                    data-bs-toggle="tooltip"
                                                                    title="View Contract Details">
                                                                    <i class="bx bx-info-circle"></i>
                                                                </a>
                                                            </h5>
                                                            <div class="contract-details">
                                                                <span class="contract-detail-item">
                                                                    <i class="bx bx-hash"></i>
                                                                    {{ $contract->contract_number }}
                                                                </span>
                                                                <span class="contract-detail-item">
                                                                    <i class="bx bx-git-branch"></i>
                                                                    {{ $contract->branchs->count() }}
                                                                    {{ Str::plural('Branch', $contract->branchs->count()) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-wrap gap-2 visit-stats d-flex">
                                                            @php
                                                                $totalVisits = $contract->visitSchedules->count();
                                                                $completedVisits = $contract->visitSchedules
                                                                    ->where('status', 'completed')
                                                                    ->count();
                                                                $pendingVisits = $contract->visitSchedules
                                                                    ->where('status', 'scheduled')
                                                                    ->count();
                                                                $cancelledVisits = $contract->visitSchedules
                                                                    ->where('status', 'cancelled')
                                                                    ->count();
                                                            @endphp
                                                            <div class="visit-stat-item">
                                                                <span class="stat-label">Total Visits</span>
                                                                <span
                                                                    class="stat-value text-primary">{{ $totalVisits }}</span>
                                                            </div>
                                                            <div class="visit-stat-item">
                                                                <span class="stat-label">Completed</span>
                                                                <span
                                                                    class="stat-value text-success">{{ $completedVisits }}</span>
                                                            </div>
                                                            <div class="visit-stat-item">
                                                                <span class="stat-label">Pending</span>
                                                                <span
                                                                    class="stat-value text-warning">{{ $pendingVisits }}</span>
                                                            </div>
                                                            <div class="visit-stat-item">
                                                                <span class="stat-label">Cancelled</span>
                                                                <span
                                                                    class="stat-value text-danger">{{ $cancelledVisits }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="contract-{{ $contract->id }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading{{ $contract->id }}"
                                            data-bs-parent="#contractAccordion">
                                            <div class="accordion-body">
                                                <!-- Branch Links Instead of Tabs -->
                                                <div class="mb-4">
                                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 fw-semibold">
                                                            <i class="bx bx-map-pin text-primary me-1"></i>
                                                            Contract Locations
                                                        </h6>
                                                        <a href="{{ route('technical.contract.visits', $contract->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="bx bx-calendar me-1"></i> View All Visits
                                                        </a>
                                                    </div>

                                                    <div class="row g-3">
                                                        <!-- Main Location -->
                                                        @php
                                                            $mainLocationVisits = $contract->visitSchedules
                                                                ->whereNull('branch_id')
                                                                ->count();
                                                        @endphp

                                                        @if ($mainLocationVisits > 0)
                                                            <div class="col-md-6 col-lg-4">
                                                                <a href="{{ route('technical.contract.branch.visits', [$contract->id, 'main']) }}"
                                                                    class="text-decoration-none">
                                                                    <div
                                                                        class="border branch-card card h-100 hover-shadow">
                                                                        <div class="p-3 card-body">
                                                                            <div class="d-flex align-items-center">
                                                                                <div
                                                                                    class="avatar-md rounded-circle bg-soft-primary me-3 d-flex align-items-center justify-content-center">
                                                                                    <i
                                                                                        class="bx bx-building text-primary font-size-20"></i>
                                                                                </div>
                                                                                <div>
                                                                                    <h6 class="mb-1">Main Location</h6>
                                                                                    <div
                                                                                        class="text-muted small d-flex align-items-center">
                                                                                        <i class="bx bx-calendar me-1"></i>
                                                                                        {{ $mainLocationVisits }} Visits
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mt-2 d-flex justify-content-end">
                                                                                <span
                                                                                    class="px-2 py-1 badge bg-soft-primary text-primary rounded-pill">
                                                                                    <i class="bx bx-chevron-right"></i>
                                                                                    View Visits
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        @endif

                                                        <!-- Contract Branches -->
                                                        @foreach ($contract->branchs as $branch)
                                                            <div class="col-md-6 col-lg-4">
                                                                <a href="{{ route('technical.contract.branch.visits', [$contract->id, $branch->id]) }}"
                                                                    class="text-decoration-none">
                                                                    <div
                                                                        class="border branch-card card h-100 hover-shadow">
                                                                        <div class="p-3 card-body">
                                                                            <div class="d-flex align-items-center">
                                                                                <div
                                                                                    class="avatar-md rounded-circle bg-soft-info me-3 d-flex align-items-center justify-content-center">
                                                                                    <i
                                                                                        class="bx bx-map-pin text-info font-size-20"></i>
                                                                                </div>
                                                                                <div>
                                                                                    <h6 class="mb-1">
                                                                                        {{ $branch->branch_name }}</h6>
                                                                                    <div
                                                                                        class="text-muted small d-flex align-items-center">
                                                                                        <i class="bx bx-calendar me-1"></i>
                                                                                        {{ $contract->visitSchedules->where('branch_id', $branch->id)->count() }}
                                                                                        Visits
                                                                                    </div>
                                                                                    <small class="ms-1 badge rounded-pill bg-info">{{ $branch->branch_address }}</small>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mt-2 d-flex justify-content-end">
                                                                                <span
                                                                                    class="px-2 py-1 badge bg-soft-info text-info rounded-pill">
                                                                                    <i class="bx bx-chevron-right"></i>
                                                                                    View Visits
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        @endforeach

                                                        <!-- New Visit Button -->
                                                        <div class="col-md-6 col-lg-4">
                                                            <div class="border border-dashed new-visit-card card h-100">
                                                                <div
                                                                    class="p-3 card-body d-flex flex-column align-items-center justify-content-center">
                                                                    <div class="mb-2 text-center">
                                                                        <i class="bx bx-plus-circle text-primary"
                                                                            style="font-size: 2rem;"></i>
                                                                        <h6 class="mt-2 mb-1">Schedule New Visit</h6>
                                                                        <p class="mb-0 text-muted small">Add a new
                                                                            appointment for this contract</p>
                                                                    </div>
                                                                    <button type="button"
                                                                        class="mt-2 btn btn-sm btn-primary"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#createVisitModal"
                                                                        data-contract-id="{{ $contract->id }}">
                                                                        <i class="bx bx-plus me-1"></i> Create Visit
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
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
                                    <select class="form-select" name="contract_id" id="contract_select" required
                                        disabled>
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

        <!-- Edit Appointment Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3 form-group">
                                <label for="edit_visit_date">Visit Date</label>
                                <input type="date" class="form-control" id="edit_visit_date" name="visit_date"
                                    required min="{{ date('Y-m-d', strtotime('today')) }}">
                            </div>
                            <div class="mb-3 form-group">
                                <label for="edit_visit_time">Visit Time</label>
                                <input type="time" class="form-control" id="edit_visit_time" name="visit_time"
                                    required>
                                <small class="form-text text-muted">Select a time between 8:00 AM and 4:00 PM (visits take
                                    2
                                    hours)</small>
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
                            <button type="submit" class="btn btn-primary">Update Appointment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('styles')
            <style>
                /* Mobile Responsiveness */
                @media (max-width: 767.98px) {

                    /* Table Adjustments */
                    .table-responsive {
                        margin: 0 -1rem;
                    }

                    .table td,
                    .table th {
                        padding: 0.75rem 0.5rem;
                        white-space: normal;
                    }

                    /* Status Badge Adjustments */
                    .d-inline-flex {
                        font-size: 0.75rem;
                    }

                    /* Action Buttons */
                    .btn-group {
                        flex-direction: column;
                        gap: 0.5rem;
                    }

                    .btn-group .btn {
                        width: 100%;
                        justify-content: center;
                    }

                    /* Accordion Header */
                    .accordion-button {
                        padding: 0.75rem;
                    }

                    .accordion-button h5 {
                        font-size: 0.9rem;
                        margin-bottom: 0.25rem !important;
                    }

                    .accordion-button .font-size-13 {
                        font-size: 0.75rem;
                    }

                    .badge {
                        font-size: 0.75rem;
                    }

                    /* Visit Details */
                    .d-flex.flex-column .text-dark {
                        font-size: 0.875rem;
                    }

                    .d-flex.flex-column .text-muted {
                        font-size: 0.75rem;
                    }

                    /* Pagination */
                    .pagination {
                        flex-wrap: wrap;
                        justify-content: center;
                    }

                    .page-link {
                        padding: 0.25rem 0.5rem;
                        min-width: 24px;
                        height: 24px;
                        font-size: 0.75rem;
                    }

                    /* Table Headers */
                    thead th {
                        font-size: 0.8rem;
                    }

                    /* Hide Less Important Columns on Small Screens */
                    @media (max-width: 575.98px) {

                        .table td:nth-child(3),
                        .table th:nth-child(3) {
                            display: none;
                        }

                        .accordion-button .d-flex.align-items-center>div:last-child {
                            display: none;
                        }
                    }
                }

                /* Existing Styles */
                .accordion-button:not(.collapsed) .bx-chevron-down {
                    transform: rotate(-180deg);
                }

                .transition-transform {
                    transition: transform 0.2s ease-in-out;
                }

                .table> :not(caption)>*>* {
                    background-color: transparent;
                }

                .pagination {
                    margin: 0;
                    gap: 4px;
                }

                .page-item {
                    margin: 0;
                }

                .page-item:first-child .page-link,
                .page-item:last-child .page-link {
                    border-radius: 4px;
                }

                .page-link {
                    padding: 0.3rem 0.6rem;
                    font-size: 0.8125rem;
                    min-width: 28px;
                    height: 28px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--bs-primary);
                    border: 1px solid #dee2e6;
                    margin: 0;
                    border-radius: 4px;
                    background-color: #fff;
                }

                .page-item.active .page-link {
                    background-color: var(--bs-primary);
                    border-color: var(--bs-primary);
                    color: #fff;
                    font-weight: 500;
                }

                .page-item.disabled .page-link {
                    background-color: #f8f9fa;
                    border-color: #dee2e6;
                    color: #6c757d;
                }

                .page-link:hover {
                    background-color: #e9ecef;
                    border-color: #dee2e6;
                    color: var(--bs-primary);
                }

                .page-item.active .page-link:hover {
                    background-color: var(--bs-primary);
                    color: #fff;
                }

                .btn-soft-primary {
                    color: var(--bs-primary);
                    background-color: rgba(var(--bs-primary-rgb), 0.1);
                    border-color: transparent;
                }

                .btn-soft-danger {
                    color: var(--bs-danger);
                    background-color: rgba(var(--bs-danger-rgb), 0.1);
                    border-color: transparent;
                }

                .btn-soft-info {
                    color: var(--bs-info);
                    background-color: rgba(var(--bs-info-rgb), 0.1);
                    border-color: transparent;
                }

                .btn-soft-primary:hover {
                    color: #fff;
                    background-color: var(--bs-primary);
                }

                .btn-soft-danger:hover {
                    color: #fff;
                    background-color: var(--bs-danger);
                }

                .btn-soft-info:hover {
                    color: #fff;
                    background-color: var(--bs-info);
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

                .page-link.dots {
                    border: none;
                    background: transparent;
                    color: #64748b;
                    pointer-events: none;
                    padding: 0 4px;
                    min-width: 24px;
                }

                /* Mobile Responsiveness */
                @media (max-width: 767.98px) {
                    .pagination {
                        flex-wrap: wrap;
                        justify-content: center;
                    }

                    .page-link {
                        min-width: 28px;
                        height: 28px;
                        font-size: 0.8125rem;
                    }

                    .page-link.dots {
                        min-width: 20px;
                    }
                }
            </style>
            <style>
                /* Contract Info Styles */
                .contract-info {
                    display: flex;
                    flex-direction: column;
                    gap: 1rem;
                    width: 100%;
                }

                .contract-main-info {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }

                .contract-title {
                    font-size: 1.1rem;
                    font-weight: 600;
                    color: #1e293b;
                    margin: 0;
                    display: flex;
                    align-items: center;
                }

                .contract-details {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 1rem;
                    align-items: center;
                }

                .contract-detail-item {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    font-size: 0.875rem;
                    color: #64748b;
                }

                /* Visit Stats Styles */
                .visit-stats {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 1rem;
                    padding: 0.5rem 0;
                }

                .visit-stat-item {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 0.25rem;
                    padding: 0.5rem 1rem;
                    background-color: #f8fafc;
                    border-radius: 8px;
                    min-width: 100px;
                }

                .stat-label {
                    font-size: 0.75rem;
                    color: #64748b;
                    font-weight: 500;
                }

                .stat-value {
                    font-size: 1.125rem;
                    font-weight: 600;
                }

                /* Mobile Responsiveness */
                @media (max-width: 767.98px) {
                    .contract-info {
                        gap: 0.75rem;
                    }

                    .visit-stats {
                        width: 100%;
                        justify-content: space-between;
                        gap: 0.5rem;
                    }

                    .visit-stat-item {
                        flex: 1;
                        min-width: calc(50% - 0.5rem);
                        padding: 0.375rem 0.5rem;
                    }

                    .stat-label {
                        font-size: 0.7rem;
                    }

                    .stat-value {
                        font-size: 1rem;
                    }
                }

                @media (max-width: 575.98px) {
                    .visit-stat-item {
                        min-width: calc(50% - 0.25rem);
                    }
                }
            </style>
            <style>
                /* Contract Header Styles */
                .view-contract-btn {
                    font-size: 1.1rem;
                    line-height: 1;
                    transition: all 0.2s ease-in-out;
                    display: inline-flex;
                    align-items: center;
                    text-decoration: none !important;
                }

                .view-contract-btn:hover {
                    color: var(--bs-primary) !important;
                    transform: scale(1.1);
                }

                .contract-title {
                    position: relative;
                    padding-right: 2rem;
                }

                /* Prevent button click from triggering accordion */
                .view-contract-btn {
                    position: relative;
                    z-index: 2;
                }

                /* Mobile adjustments */
                @media (max-width: 767.98px) {
                    .view-contract-btn {
                        font-size: 1rem;
                    }

                    .contract-title {
                        padding-right: 1.5rem;
                    }
                }
            </style>
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

                /* Branch pagination styles */
                .branch-pagination {
                    padding: 0.5rem;
                    background-color: #f8f9fa;
                    border-radius: 0 0 8px 8px;
                    border-top: 1px solid #e9ecef;
                }

                .branch-pagination .pagination {
                    margin: 0;
                }

                .branch-pagination .page-item.active .page-link {
                    background-color: var(--bs-primary);
                    border-color: var(--bs-primary);
                    color: #fff;
                    font-weight: 500;
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

                    .branch-pagination {
                        padding: 0.375rem;
                    }

                    .branch-pagination .pagination {
                        justify-content: center;
                    }
                }
            </style>
            <style>
                /* Visits Card Grid Styles */
                .branch-card {
                    transition: all 0.2s ease-in-out;
                }

                .branch-card:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 8px 24px rgba(149, 157, 165, 0.15) !important;
                    border-color: var(--bs-primary) !important;
                }

                .new-visit-card {
                    transition: all 0.2s ease-in-out;
                    border-style: dashed !important;
                    border-width: 2px !important;
                    border-color: #e2e8f0 !important;
                }

                .new-visit-card:hover {
                    border-color: var(--bs-primary) !important;
                    background-color: rgba(var(--bs-primary-rgb), 0.05);
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

                    // Branch tab activation on page load
                    $('.nav-tabs').each(function() {
                        // Check if there's an active tab already
                        if ($(this).find('.active').length === 0) {
                            // Activate the first tab
                            $(this).find('button:first').tab('show');
                        }
                    });

                    // Edit modal data loading
                    $('#editModal').on('show.bs.modal', function(event) {
                        var button = $(event.relatedTarget);
                        var visitId = button.data('visit-id');
                        var visitDate = button.data('visit-date');
                        var visitTime = button.data('visit-time');
                        var teamId = button.data('team-id');
                        var contractId = button.data('contract-id');
                        var branchId = button.data('branch-id');

                        var modal = $(this);

                        // Set data in the modal
                        modal.find('#edit_visit_date').val(visitDate);
                        modal.find('#edit_visit_time').val(visitTime);
                        modal.find('#edit_team_id').val(teamId);

                        // Update the form action
                        var formAction = "{{ route('technical.appointment.edit', ':id') }}";
                        formAction = formAction.replace(':id', visitId);

                        modal.find('#editForm').attr('action', formAction);

                        // Store additional data for reference if needed
                        modal.find('#editForm').data('contract-id', contractId);
                        modal.find('#editForm').data('branch-id', branchId);
                    });

                    // Pagination for branch tabs 
                    // Handle branch pagination clicks to stay on the same tab
                    $('.branch-pagination .page-link').on('click', function(e) {
                        e.preventDefault();
                        var url = $(this).attr('href');
                        var tabId = $(this).closest('.tab-pane').attr('id');

                        // Make AJAX request to load new paginated content
                        $.ajax({
                            url: url,
                            type: 'GET',
                            success: function(response) {
                                // Parse the HTML response
                                var parser = new DOMParser();
                                var doc = parser.parseFromString(response, 'text/html');

                                // Extract the new tab content
                                var newContent = doc.querySelector('#' + tabId + ' .visits-card-grid');
                                var newPagination = doc.querySelector('#' + tabId +
                                    ' .branch-pagination');

                                // Replace current content
                                $('#' + tabId + ' .visits-card-grid').html($(newContent).html());
                                $('#' + tabId + ' .branch-pagination').html($(newPagination).html());

                                // Re-bind click events
                                bindBranchPaginationEvents();
                            }
                        });
                    });

                    function bindBranchPaginationEvents() {
                        $('.branch-pagination .page-link').on('click', function(e) {
                            e.preventDefault();
                            var url = $(this).attr('href');
                            var tabId = $(this).closest('.tab-pane').attr('id');

                            // Make AJAX request to load new paginated content
                            $.ajax({
                                url: url,
                                type: 'GET',
                                success: function(response) {
                                    // Parse the HTML response
                                    var parser = new DOMParser();
                                    var doc = parser.parseFromString(response, 'text/html');

                                    // Extract the new tab content
                                    var newContent = doc.querySelector('#' + tabId +
                                        ' .visits-card-grid');
                                    var newPagination = doc.querySelector('#' + tabId +
                                        ' .branch-pagination');

                                    // Replace current content
                                    $('#' + tabId + ' .visits-card-grid').html($(newContent).html());
                                    $('#' + tabId + ' .branch-pagination').html($(newPagination)
                                        .html());

                                    // Re-bind click events
                                    bindBranchPaginationEvents();
                                }
                            });
                        });
                    }

                    // Initial binding
                    bindBranchPaginationEvents();

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

                    // Add branch_id to the create visit form when opened from a branch tab
                    $('#createVisitModal').on('show.bs.modal', function(event) {
                        var button = $(event.relatedTarget);
                        var contractId = button.data('contract-id');
                        var branchId = button.data('branch-id');

                        if (contractId) {
                            $(this).find('select[name="contract_id"]').val(contractId).trigger('change');
                        }

                        if (branchId) {
                            // Set timeout to allow contract_id change to process first
                            setTimeout(function() {
                                $(this).find('select[name="branch_id"]').val(branchId);
                            }, 300);
                        }
                    });

                    // Client selection change handler
                    document.getElementById('client_select').addEventListener('change', function() {
                        const clientId = this.value;
                        const contractSelect = document.getElementById('contract_select');
                        const branchSelect = document.getElementById('branch_select');

                        // Reset and disable branch dropdown
                        branchSelect.innerHTML = '<option value="">Select Branch</option>';
                        branchSelect.disabled = true;

                        if (clientId) {
                            // Enable contract dropdown and fetch contracts
                            contractSelect.disabled = false;
                            fetch(`/api/clients/${clientId}/contracts`)
                                .then(response => response.json())
                                .then(contracts => {
                                    let options = '<option value="">Select Contract</option>';
                                    contracts.forEach(contract => {
                                        options +=
                                            `<option value="${contract.id}">${contract.contract_number}</option>`;
                                    });
                                    contractSelect.innerHTML = options;
                                    contractSelect.disabled = false;
                                })
                                .catch(error => {
                                    console.error('Error fetching contracts:', error);
                                    contractSelect.innerHTML =
                                        '<option value="">Error loading contracts</option>';
                                    contractSelect.disabled = true;
                                });
                        } else {
                            // Reset and disable contract dropdown
                            contractSelect.innerHTML = '<option value="">Select Contract</option>';
                            contractSelect.disabled = true;
                        }
                    });

                    // Contract selection change handler
                    document.getElementById('contract_select').addEventListener('change', function() {
                        const contractId = this.value;
                        const branchSelect = document.getElementById('branch_select');

                        if (contractId) {
                            // Enable branch dropdown and fetch branches
                            branchSelect.disabled = false;
                            fetch(`/api/contracts/${contractId}/branches`)
                                .then(response => response.json())
                                .then(branches => {
                                    let options = '<option value="">Select Branch</option>';
                                    if (branches && branches.length > 0) {
                                        branches.forEach(branch => {
                                            options +=
                                                `<option value="${branch.id}">${branch.name}</option>`;
                                        });
                                    }
                                    branchSelect.innerHTML = options;
                                    branchSelect.disabled = false;
                                })
                                .catch(error => {
                                    console.error('Error fetching branches:', error);
                                    branchSelect.innerHTML = '<option value="">Error loading branches</option>';
                                    branchSelect.disabled = true;
                                });
                        } else {
                            // Reset and disable branch dropdown
                            branchSelect.innerHTML = '<option value="">Select Branch</option>';
                            branchSelect.disabled = true;
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

                // Edit Appointment Functions
                function openEditModal(appointmentId, currentDate, currentTime, currentTeamId) {
                    // Set form action
                    document.getElementById('editForm').action = `/technical/appointments/${appointmentId}/edit`;

                    // Set current values
                    document.getElementById('edit_visit_date').value = currentDate;
                    document.getElementById('edit_visit_time').value = currentTime;
                    document.getElementById('edit_team_id').value = currentTeamId;

                    // Show modal
                    new bootstrap.Modal(document.getElementById('editModal')).show();
                }

                // Cancel Appointment Function
                function cancelAppointment(appointmentId) {
                    if (confirm('Are you sure you want to cancel this appointment?')) {
                        window.location.href = "{{ route('technical.appointment.cancel', ['appointment' => ':id']) }}".replace(
                            ':id', appointmentId);
                    }
                }

                // Contract change handler in create modal
                $('select[name="contract_id"]').change(function() {
                    var contractId = $(this).val();
                    if (contractId) {
                        fetchBranches(contractId);
                    } else {
                        $('select[name="branch_id"]').html('<option value="">Select Branch</option>');
                    }
                });

                // Function to fetch branches
                function fetchBranches(contractId) {
                    $.ajax({
                        url: "/api/contracts/" + contractId + "/branches",
                        type: "GET",
                        success: function(response) {
                            if (response.success) {
                                var options = '<option value="">Select Branch</option>';
                                $.each(response.branches, function(index, branch) {
                                    options += '<option value="' + branch.id + '">' + branch.name + '</option>';
                                });
                                $('select[name="branch_id"]').html(options);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error fetching branches:', xhr);
                        }
                    });
                }
            </script>
        @endpush
    </div>
@endsection
