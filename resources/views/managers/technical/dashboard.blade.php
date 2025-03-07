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
                            <div class="overflow-hidden bg-white border-0 transition-all card rounded-4 hover:transform hover:scale-102"
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
                    <div class="overflow-hidden bg-white border-0 transition-all card rounded-4 hover:transform hover:scale-102"
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
                    <div class="overflow-hidden bg-white border-0 transition-all card rounded-4 hover:transform hover:scale-102"
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
                    <div class="overflow-hidden bg-white border-0 transition-all card rounded-4 hover:transform hover:scale-102"
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
                    <div class="overflow-hidden bg-white border-0 card rounded-4"
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
                    <div class="overflow-hidden bg-white border-0 card rounded-4"
                        style="box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%);">
                        <div
                            class="p-4 bg-transparent card-header border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 card-title fw-bold">Today's Schedule</h5>
                        </div>
                        <div class="p-4 card-body">
                            <div class="table-responsive">
                                <table class="table mb-0 table-centered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-3 border-0">Time</th>
                                            <th class="py-3 border-0">Contract #</th>
                                            <th class="py-3 border-0">Customer</th>
                                            <th class="py-3 border-0">Team</th>
                                            <th class="py-3 border-0">Location</th>
                                            <th class="py-3 border-0">Status</th>
                                            <th class="py-3 border-0">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($teamSchedules as $schedule)
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
                                                @if($schedule->contract && $schedule->contract->customer)
                                                <div class="fw-medium">{{ $schedule->contract->customer->name }}</div>
                                                @else
                                                <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                <div class="fw-medium">{{ $schedule->team->name }}</div>
                                            </td>
                                            <td class="py-3">
                                                <div class="text-wrap" style="max-width: 200px;">
                                                    {{ $schedule->branch_id ? $schedule->branch->branch_address :
                                                    $schedule->contract->customer->address }}
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                @if($schedule->status == 'completed')
                                                <span
                                                    class="px-3 py-1 badge rounded-pill bg-success-subtle text-success">Completed</span>
                                                @elseif($schedule->status == 'in_progress')
                                                <span
                                                    class="px-3 py-1 badge rounded-pill bg-warning-subtle text-warning">In
                                                    Progress</span>
                                                @else
                                                <span
                                                    class="px-3 py-1 badge rounded-pill bg-info-subtle text-info">Scheduled</span>
                                                @endif
                                            </td>
                                            @if($schedule->status == 'completed')
                                            <td class="py-3">
                                                <a href="{{ route('technical.visit.report.view', $schedule->id) }}"
                                                    class="px-3 transition-colors btn btn-sm rounded-pill btn-soft-info hover:bg-info hover:text-white">
                                                    <i class="bx bx-show me-1"></i> View
                                                </a>
                                            </td>
                                            @else
                                            <td class="py-3">
                                                <a href="javascript:void(0);"
                                                    class="px-3 transition-colors btn btn-sm rounded-pill btn-soft-danger hover:bg-danger hover:text-white"
                                                    onclick="openEditModal({{ $schedule->id }}, '{{ $schedule->visit_date }}', '{{ $schedule->visit_time }}', {{ $schedule->team_id }})">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                            </td>
                                            @endif
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="py-4 text-center text-muted">No schedules for today
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Contracts with Upcoming Visits -->
            <div class="mb-4 row">
                <div class="col-12">
                    <div class="overflow-hidden bg-white border-0 card rounded-4"
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
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Time</th>
                                                            <th>Team</th>
                                                            <th>Branch</th>
                                                            <th>Status</th>
                                                            <th>Edit</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {{-- upcoming visits where visit date is not today --}}

                                                        @foreach($contract->visitSchedules->where('visit_date', '!=',
                                                        \Carbon\Carbon::today()->toDateString()) as $visit)
                                                        <tr>
                                                            <td class="py-3">
                                                                <div class="text-wrap" style="max-width: 200px;">
                                                                    {{
                                                                    \Carbon\Carbon::parse($visit->visit_date)->format('Y-m-d')
                                                                    }}
                                                                </div>
                                                            </td>
                                                            <td class="py-3">
                                                                <div class="text-wrap" style="max-width: 200px;">
                                                                    {{ $visit->visit_time }}
                                                                </div>
                                                            </td>
                                                            <td class="py-3">{{ $visit->team->name }}</td>
                                                            <td class="py-3">
                                                                <div class="text-wrap" style="max-width: 200px;">
                                                                    {{ $visit->branch_id ?
                                                                    $visit->branch->branch_name :
                                                                    $visit->contract->customer->name }}
                                                                    <small class="d-block text-muted">
                                                                        {{ $visit->branch_id ?
                                                                        $visit->branch->branch_address :
                                                                        $visit->contract->customer->address }}
                                                                    </small>
                                                                </div>
                                                            </td>
                                                            <td class="py-3">
                                                                @if($visit->status == 'completed')
                                                                <span
                                                                    class="px-2 py-1 badge bg-success-subtle text-success">Completed</span>
                                                                @elseif($visit->status == 'cancelled')
                                                                <span
                                                                    class="px-2 py-1 badge bg-danger-subtle text-danger">Cancelled</span>
                                                                @else
                                                                <span
                                                                    class="px-2 py-1 badge bg-info-subtle text-info">Scheduled</span>
                                                                @endif
                                                            </td>
                                                            {{-- edit button --}}
                                                            <td class="py-3">
                                                                <a href="javascript:void(0);"
                                                                    class="px-3 transition-colors btn btn-sm rounded-pill btn-soft-danger hover:bg-danger hover:text-white"
                                                                    onclick="openEditModal({{ $visit->id }}, '{{ $visit->visit_date }}', '{{ $visit->visit_time }}', {{ $visit->team_id }})">
                                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
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
                    <div class="overflow-hidden bg-white border-0 card rounded-4"
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
            <form id="editForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3 form-group">
                        <label for="edit_visit_date">Visit Date</label>
                        <input type="date" class="form-control" id="edit_visit_date" name="visit_date" required
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}">
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
@push('scripts')
<script>
    function openEditModal(visitId, visitDate, visitTime, teamId) {
            const form = document.getElementById('editForm');
            form.action = `/technical/appointments/${visitId}/edit`;
            document.getElementById('edit_visit_date').value = visitDate;
            document.getElementById('edit_visit_time').value = visitTime;
            document.getElementById('edit_team_id').value = teamId;
            $('#editModal').modal('show');
        }
</script>
@endpush
@endsection