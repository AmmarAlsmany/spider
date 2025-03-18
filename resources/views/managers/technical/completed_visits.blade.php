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
                    <h3 class="card-title">Completed Visits</h3>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('technical.completed-visits') }}" class="mb-4">
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
                                    <a href="{{ route('technical.completed-visits') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Visits Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Visit Date</th>
                                    <th>Visit Time</th>
                                    <th>Contract Number</th>
                                    <th>Customer</th>
                                    <th>Team</th>
                                    <th>Visit Number</th>
                                    <th>Completion Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $visit)
                                    <tr>
                                        <td>{{ $visit->visit_date }}</td>
                                        <td>{{ $visit->visit_time}}</td>
                                        <td>{{ $visit->contract->contract_number }}</td>
                                        <td>{{ $visit->contract->customer->name }}</td>
                                        <td>{{ $visit->team->name }}</td>
                                        <td>Visit {{ $visit->visit_number }} of {{ $visit->total_visits }}</td>
                                        <td>{{ $visit->updated_at}}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('technical.visit.report.view', $visit->id) }}"
                                                    class="btn btn-sm btn-info"
                                                    data-bs-toggle="tooltip"
                                                    title="View Details">
                                                    <i class="bx bx-show"></i> View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No completed visits found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $visits->links("vendor.pagination.custom") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
