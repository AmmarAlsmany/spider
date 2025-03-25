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
                    <h3 class="card-title">Cancelled Visits</h3>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('technical.cancelled-visits') }}" class="mb-4">
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
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                                {{ $team->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="contract_number">Contract Number</label>
                                    <input type="text" class="form-control" id="contract_number" name="contract_number"
                                           value="{{ request('contract_number') }}" placeholder="Enter contract #">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="mb-0 form-group">
                                    <button type="submit" class="mr-2 btn btn-primary">Filter</button>
                                    <a href="{{ route('technical.cancelled-visits') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Visits Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Original Date</th>
                                    <th>Original Time</th>
                                    <th>Contract Number</th>
                                    <th>Customer</th>
                                    <th>Previous Team</th>
                                    <th>Visit Number</th>
                                    <th>Cancellation Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $visit)
                                    <tr>
                                        <td>{{ $visit->visit_date }}</td>
                                        <td>{{ $visit->visit_time }}</td>
                                        <td>{{ $visit->contract->contract_number }}</td>
                                        <td>{{ $visit->contract->customer->name }}</td>
                                        <td>{{ $visit->team->name }}</td>
                                        <td>Visit {{ $visit->visit_number }} of {{ $visit->total_visits }}</td>
                                        <td>{{ $visit->updated_at }}</td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" 
                                                    onclick="openRescheduleModal({{ $visit->id }})">
                                                Reschedule
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No cancelled visits found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $visits->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" role="dialog" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rescheduleModalLabel">Reschedule Visit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rescheduleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_visit_date">New Visit Date</label>
                        <input type="date" class="form-control" id="new_visit_date" name="visit_date" required
                               min="{{ date('Y-m-d', strtotime('today')) }}">
                    </div>

                    <div class="form-group">
                        <label for="new_visit_time">New Visit Time</label>
                        <input type="time" class="form-control" id="new_visit_time" name="visit_time" required>
                    </div>

                    <div class="form-group">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Reschedule Visit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openRescheduleModal(visitId) {
    const form = document.getElementById('rescheduleForm');
    form.action = `/technical/visits/${visitId}/reschedule`;
    $('#rescheduleModal').modal('show');
}

// Prevent scheduling on Fridays
document.getElementById('new_visit_date').addEventListener('change', function(e) {
    const date = new Date(this.value);
    if (date.getDay() === 5) { // 5 is Friday
        alert('Visits cannot be scheduled on Fridays. Please select another day.');
        this.value = '';
    }
});
</script>
@endpush
@endsection
