@extends('shared.dashboard')

@section('title', 'Contracts Pending Team Assignment')

@section('content')
<div class="page-content">
    <div class="mb-4 row">
        <div class="col-12">
            <div class="shadow card">
                <div class="text-white card-header bg-primary">
                    <h5 class="mb-0">Contracts Awaiting Team Assignment</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($contracts->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> There are no contracts waiting for team assignment at this time.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Contract #</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Visit Start Date</th>
                                        <th>Branches</th>
                                        <th>Contract Type</th>
                                        <th>Number of Visits</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contracts as $contract)
                                        <tr>
                                            <td><a href="{{ route('technical.contract.show', $contract->id) }}" class="text-primary">{{ $contract->contract_number }}</a></td>
                                            <td>{{ $contract->customer->company_name ?? $contract->customer->name }}</td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i> Under Processing
                                                </span>
                                            </td>
                                            <td>{{ $contract->contract_start_date }}</td>
                                            <td>{{ $contract->contract_end_date }}</td>
                                            <td>{{ $contract->visit_start_date }}</td>
                                            <td>{{ $contract->branchs->count() }}</td>
                                            <td>{{ $contract->type->name }}</td>
                                            <td>{{ $contract->number_of_visits ?? 0 }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('technical.contract.show', $contract->id) }}" class="btn btn-sm btn-info me-2">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    
                                                    @if($teams->count() > 0)
                                                        <form action="{{ route('technical.process.contract', $contract->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="fas fa-check-circle"></i> Process & Schedule
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-sm btn-secondary" disabled title="No active teams available">
                                                            <i class="fas fa-users-slash"></i> Cannot Process
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $contracts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="shadow card">
                <div class="text-white card-header bg-info">
                    <h5 class="mb-0">Active Teams Available</h5>
                </div>
                <div class="card-body">
                    @if($teams->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> There are no active teams available.
                            <a href="{{ route('teams.create') }}" class="alert-link">Create a team</a> to process contracts.
                        </div>
                    @else
                        <ul class="list-group">
                            @foreach($teams as $team)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-bold">{{ $team->name }}</span>
                                        <span class="ms-2 text-muted">Leader: {{ $team->leader->name ?? 'Not assigned' }}</span>
                                    </div>
                                    <span class="badge bg-success rounded-pill">{{ $team->members->count() }} members</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="mt-4 col-12 col-lg-6 mt-lg-0">
            <div class="shadow card">
                <div class="text-white card-header bg-primary">
                    <h5 class="mb-0">Team Assignment Guidelines</h5>
                </div>
                <div class="card-body">
                    <div class="border alert alert-light">
                        <h6 class="mb-3 fw-bold"><i class="fas fa-info-circle me-2"></i> About Contract Processing</h6>
                        <p>When you process a contract:</p>
                        <ul>
                            <li>The contract status will change from <span class="badge bg-warning text-dark">Under Processing</span> to <span class="badge bg-success">Approved</span></li>
                            <li>Visits will be automatically scheduled for all branches</li>
                            <li>The client and sales team will be notified</li>
                        </ul>
                        <p class="mb-0"><strong>Note:</strong> Make sure you have sufficient active teams to handle the workload before processing multiple contracts.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Add confirmation for processing contracts using native browser confirm
        $('form').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            
            if (confirm('Process this contract? This will schedule all visits and notify the client. Continue?')) {
                form.off('submit').submit();
            }
        });
    });
</script>
@endpush
