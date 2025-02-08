@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('sales.dashboard') }}" class="text-decoration-none">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('contract.show') }}" class="text-decoration-none">
                            Active Contracts
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-muted" aria-current="page">Visit Schedule</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Contract Information Card -->
        <div class="mb-4 col-12">
            <div class="border-0 shadow-sm card">
                <div class="bg-transparent card-header">
                    <h5 class="mb-0"><i class="bx bx-info-circle me-2"></i>Contract Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Contract Number:</strong> {{ $contract->contract_number }}</p>
                            <p><strong>Client Name:</strong> {{ $contract->customer->name }}</p>
                            <p><strong>Client Email:</strong> {{ $contract->customer->email }}</p>
                            <p><strong>Client Phone:</strong> {{ $contract->customer->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Contract Start:</strong> {{ $contract->contract_start_date }}</p>
                            <p><strong>Contract End:</strong> {{ $contract->contract_end_date }}</p>
                            <p><strong>Number of Visits:</strong> {{ $contract->number_of_visits }} visits for each branch</p>
                            <p><strong>Status:</strong> <span class="badge bg-success">Active</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visit Schedule Card -->
        <div class="col-12">
            <div class="border-0 shadow-sm card">
                <div class="bg-transparent card-header">
                    <h5 class="mb-0"><i class="bx bx-calendar me-2"></i>Visit Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Visit Date</th>
                                    <th>Visit Time</th>
                                    <th>Team</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $visit)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($visit->visit_date)->format('d M, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($visit->visit_time)->format('h:i A') }}</td>
                                    <td>{{ $visit->team->name ?? 'Not Assigned' }}</td>
                                    <td>
                                        @switch($visit->status)
                                        @case('scheduled')
                                        <span class="badge bg-info">Scheduled</span>
                                        @break
                                        @case('completed')
                                        <span class="badge bg-success">Completed</span>
                                        @break
                                        @case('cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                        @break
                                        @default
                                        <span class="badge bg-secondary">{{ $visit->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($visit->status === 'completed')
                                            <a href="{{ route('contract.visit.report', $visit->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bx bx-file me-1"></i>View Report
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center">
                                        <div class="text-muted">
                                            <i class="bx bx-calendar-x fs-1"></i>
                                            <p class="mt-2">No visits scheduled yet</p>
                                        </div>
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
</div>
@endsection