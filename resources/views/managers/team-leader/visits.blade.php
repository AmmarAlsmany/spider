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
    <div class="card">
        <div class="card-body">
            <div class="mb-4 d-flex align-items-center">
                <h5 class="mb-0">Team Visits</h5>
                <div class="ms-auto">
                    <a href="{{ route('team-leader.dashboard') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i>Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-4 row">
                <div class="col-12">
                    <form action="{{ route('team-leader.visits') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Contract Number</label>
                            <input type="text" name="contract_number" class="form-control" 
                                value="{{ request('contract_number') }}" placeholder="Search by contract #">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" 
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" 
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-filter-alt"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Filters -->
            <div class="mb-4 row">
                <div class="col-12">
                    <a href="{{ route('team-leader.visits') }}?date={{ date('Y-m-d') }}" 
                        class="btn {{ request('date') == date('Y-m-d') ? 'btn-primary' : 'btn-outline-primary' }} me-2">
                        Today
                    </a>
                    <a href="{{ route('team-leader.visits') }}?start_date={{ date('Y-m-d') }}&end_date={{ date('Y-m-d', strtotime('+7 days')) }}" 
                        class="btn {{ request('start_date') == date('Y-m-d') ? 'btn-primary' : 'btn-outline-primary' }} me-2">
                        Next 7 Days
                    </a>
                    <a href="{{ route('team-leader.visits') }}?start_date={{ date('Y-m-01') }}&end_date={{ date('Y-m-t') }}" 
                        class="btn {{ request('start_date') == date('Y-m-01') ? 'btn-primary' : 'btn-outline-primary' }}">
                        This Month
                    </a>
                </div>
            </div>

            <!-- Visits Table -->
            @if($visits->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Contract #</th>
                            <th>Customer</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visits as $visit)
                        <tr>
                            <td>
                                <span class="d-block">{{ date('M d, Y', strtotime($visit->visit_date)) }}</span>
                                <small class="text-muted">{{ date('h:i A', strtotime($visit->visit_time)) }}</small>
                            </td>
                            <td>
                                <a href="{{ route('team-leader.contract.show', $visit->contract->id) }}" class="text-primary">
                                    {{ $visit->contract->contract_number }}
                                </a>
                            </td>
                            <td>{{ $visit->contract->customer->name }}</td>
                            <td>
                                @if($visit->branch_id)
                                    {{ $visit->branch->branch_name }}
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($visit->branch->branch_address) }}"
                                        target="_blank" class="text-primary"><i class="bx bx-map me-1">{{ $visit->branch->branch_address }}</i></a>
                                @else
                                    Main Location
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($visit->contract->customer->address) }}"
                                        target="_blank" class="text-primary"><i class="bx bx-map me-1">{{ $visit->contract->customer->address }}</i></a>
                                @endif
                            </td>
                            <td>
                                @if($visit->status == 'completed')
                                    <span class="badge bg-success">
                                        <i class="bx bx-check-circle me-1"></i>Completed
                                    </span>
                                @elseif($visit->status == 'cancelled')
                                    <span class="badge bg-danger">
                                        <i class="bx bx-x-circle me-1"></i>Cancelled
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="bx bx-time me-1"></i>Pending
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('team-leader.visit.show', $visit->id) }}" 
                                        class="btn btn-sm btn-info" title="View Details">
                                        <i class="bx bx-show me-1"></i>View
                                    </a>
                                    @if($visit->status != 'completed' && date('Y-m-d') == $visit->visit_date || date('Y-m-d') > $visit->visit_date)
                                    <a href="{{ route('team-leader.visit.report.create', $visit->id) }}" 
                                        class="btn btn-sm btn-primary" title="Create Report">
                                        <i class="bx bx-file me-1"></i>Report
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-end">
                {{ $visits->links() }}
            </div>
            @else
            <div class="py-4 text-center">
                <i class="bx bx-calendar-x text-muted" style="font-size: 4rem;"></i>
                <p class="mt-2 text-muted">No visits found</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
