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
                            <li class="breadcrumb-item"><a href="{{ route('contract.show') }}">Contracts</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('contract.show') }}">{{ $contract->contract_number }}</a></li>
                            <li class="breadcrumb-item active">
                                @if (isset($branch) && $branch != 'main')
                                    {{ $branch->branch_name }} Visits
                                @elseif (isset($branch) && $branch == 'main')
                                    Main Location Visits
                                @else
                                    All Visits
                                @endif
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="flex-wrap gap-2 d-flex">
                    <a href="{{ route('view.contract.visit', $contract->id) }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Back to Contract
                    </a>
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
                        action="{{ route('contract.branch.visits', [$contract->id, isset($branch) && $branch != 'main' ? $branch->id : 'main']) }}"
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
                        <!-- Submit/Reset -->
                        <div class="gap-2 col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ route('contract.branch.visits', [$contract->id, isset($branch) && $branch != 'main' ? $branch->id : 'main']) }}"
                                class="btn btn-secondary">
                                Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Visits Grid -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 card-title">
                        <i class="bx bx-list-ul me-1"></i>
                        @if (isset($branch) && $branch != 'main')
                            {{ $branch->branch_name }} Visits
                        @elseif (isset($branch) && $branch == 'main')
                            Main Location Visits
                        @else
                            All Contract Visits
                        @endif
                        <span class="ms-1 badge rounded-pill bg-info">{{ $visits->total() }}</span>
                    </h5>
                    <div>
                        <select id="sortVisits" class="form-select form-select-sm"
                            onchange="changeSortOrder(this.value)">
                            <option value="desc" {{ request('sort_direction') != 'asc' ? 'selected' : '' }}>Newest First
                            </option>
                            <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Oldest First
                            </option>
                        </select>
                    </div>
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
                                                            Visit #{{ $visit->visit_number ?? $loop->iteration }}
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

                                                <!-- Branch information if not a branch-specific page -->
                                                @if ((!isset($branch) || $branch == 'main') && isset($visit->branch))
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
                                                    @if ($visit->status == 'completed')
                                                        <a href="{{ route('contract.visit.report', $visit->id) }}"
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
                                </div>
                            </div>
                        @endif
                    </div>
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

        /* Soft background colors for status badges */
        .bg-soft-primary {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }

        .bg-soft-success {
            background-color: rgba(var(--bs-success-rgb), 0.1);
        }

        .bg-soft-warning {
            background-color: rgba(var(--bs-warning-rgb), 0.1);
        }

        .bg-soft-danger {
            background-color: rgba(var(--bs-danger-rgb), 0.1);
        }

        .bg-soft-info {
            background-color: rgba(var(--bs-info-rgb), 0.1);
        }

        /* Text colors for status badges */
        .text-primary {
            color: var(--bs-primary) !important;
        }

        .text-success {
            color: var(--bs-success) !important;
        }

        .text-warning {
            color: var(--bs-warning) !important;
        }

        .text-danger {
            color: var(--bs-danger) !important;
        }

        .text-info {
            color: var(--bs-info) !important;
        }

        /* Button styles */
        .btn-soft-primary {
            color: var(--bs-primary);
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            border-color: transparent;
        }

        .btn-soft-success {
            color: var(--bs-success);
            background-color: rgba(var(--bs-success-rgb), 0.1);
            border-color: transparent;
        }

        .btn-soft-primary:hover {
            color: #fff;
            background-color: var(--bs-primary);
        }

        .btn-soft-success:hover {
            color: #fff;
            background-color: var(--bs-success);
        }

        /* Avatar styles */
        .avatar-md {
            width: 50px;
            height: 50px;
        }

        .font-size-24 {
            font-size: 24px;
        }

        .font-size-13 {
            font-size: 13px;
        }

        /* Hover effects */
        .hover-shadow:hover {
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
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
        });

        // Sort function
        function changeSortOrder(direction) {
            // Get current URL
            let url = new URL(window.location);

            // Set or update the sort_direction parameter
            url.searchParams.set('sort_direction', direction);

            // Redirect to the updated URL
            window.location.href = url.toString();
        }
    </script>
@endpush
