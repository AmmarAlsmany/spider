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
    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Contract Details</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('messages.contract_details') }}</h5>
                <div>
                    <a href="{{ route('contract.insect-analytics', $contract->id) }}" class="btn btn-primary">
                        <i class="bx bx-line-chart"></i> Insect Analytics
                    </a>
                </div>
            </div>
            <!-- Basic Information -->
            <div class="mb-4 row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contract Number</label>
                        <p>{{ $contract->contract_number }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Customer</label>
                        <p>{{ $contract->customer ? $contract->customer->name : 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sales Representative</label>
                        <p>{{ $contract->salesRepresentative ? $contract->salesRepresentative->name : 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Start Date</label>
                        <p>{{ $contract->contract_start_date }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Visit Start Date</label>
                        <p>{{ $contract->visit_start_date ?? 'Same as contract start date' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">End Date</label>
                        <p>{{ $contract->contract_end_date ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <p>
                            <span class="badge {{ 
                                $contract->contract_status == 'active' ? 'bg-success' : 
                                ($contract->contract_status == 'expired' ? 'bg-danger' : 
                                ($contract->contract_status == 'pending' ? 'bg-warning' : 'bg-info')) 
                            }}">
                                {{ ucfirst($contract->contract_status) }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contract Type</label>
                        <p>{{ $contract->type->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contract Price</label>
                        <p>${{ number_format($contract->contract_price, 2) }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Type</label>
                        <p>{{ $contract->Payment_type }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Number of Payments</label>
                        <p>{{ $contract->number_Payments ? $contract->number_Payments : '1' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Property Type</label>
                        <p>{{ $contract->Property_type }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Number of Visits</label>
                        <p>{{ $contract->number_of_visits }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Warranty</label>
                        <p>{{ $contract->warranty }}</p>
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($contract->contract_description)
            <div class="mb-4 card">
                <div class="card-header">
                    <h6 class="mb-0">Description</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $contract->contract_description }}</p>
                </div>
            </div>
            @endif

            <!-- Payment Schedule -->
            <div class="mb-4 card">
                <div class="card-header">
                    <h6 class="mb-0">Payment Schedule</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Payment #</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Paid Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contract->payments as $payment)
                                <tr>
                                    <td>{{ $payment->invoice_number }}</td>
                                    <td>{{ $payment->due_date }}</td>
                                    <td>${{ number_format($payment->payment_amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $payment->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($payment->payment_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->paid_at ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No payment schedule available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $payments->links("vendor.pagination.custom") }}
                    </div>
                </div>
            </div>

            <!-- Visit Schedules -->
            <div class="mb-4 card">
                <div class="card-header">
                    <h6 class="mb-0">Visit Schedules</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Visit Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $visitSchedules = $contract->visitSchedules()->paginate(10);
                                @endphp
                                @forelse($visitSchedules as $visit)
                                <tr>
                                    <td>{{ $visit->visit_date }}</td>
                                    <td>
                                        <span class="badge {{ $visit->status == 'completed' ? 'bg-success' : 
                                            ($visit->status == 'scheduled' ? 'bg-info' : 'bg-warning') }}">
                                            {{ ucfirst($visit->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">No visit schedules available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $visitSchedules->links("vendor.pagination.custom") }}
                    </div>
                    @if($visitSchedules->hasPages())
                    <div class="mt-4 d-flex justify-content-end">
                        <nav>
                            <ul class="mb-0 pagination pagination-sm">
                                {{-- Previous Page Link --}}
                                @if ($visitSchedules->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">Previous</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $visitSchedules->previousPageUrl() }}" rel="prev">Previous</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($visitSchedules->getUrlRange(1, $visitSchedules->lastPage()) as $page => $url)
                                    @if ($page == $visitSchedules->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($visitSchedules->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $visitSchedules->nextPageUrl() }}" rel="next">Next</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Next</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contract History -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Contract History</h6>
                </div>
                <div class="card-body">
                    <div class="timeline-vertical">
                        @forelse($contract->history as $history)
                        <div class="timeline-item">
                            <div class="timeline-point"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ $history->action }}</h6>
                                <p class="mb-0">{{ $history->description }}</p>
                                <small class="text-muted">
                                    By {{ $history->user->name }} on {{ $history->created_at->format('M d, Y H:i') }}
                                </small>
                            </div>
                        </div>
                        @empty
                        <p class="text-center">No history available</p>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($contract->is_multi_branch)
            <!-- Branches -->
            <div class="mt-4 card">
                <div class="card-header">
                    <h6 class="mb-0">Contract Branches</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Branch Name</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contract->branchs as $branch)
                                <tr>
                                    <td>{{ $branch->branch_name }}</td>
                                    <td>{{ $branch->branch_address }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">No branches found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
