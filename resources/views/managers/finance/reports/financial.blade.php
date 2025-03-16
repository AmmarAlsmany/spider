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
                    <h6 class="mb-0">Financial Report</h6>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Date Range Filter -->
            <div class="mb-4">
                <form action="{{ route('finance.reports.financial') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Contract Number</label>
                        <input type="text" name="contract_number" class="form-control" value="{{ request('contract_number') }}" placeholder="Enter contract number">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="gap-2 d-flex">
                            <button type="submit" class="btn btn-primary flex-grow-1">Generate Report</button>
                            @if(request('contract_number') || request('start_date') || request('end_date'))
                                <a href="{{ route('finance.reports.financial') }}" class="btn btn-outline-secondary">
                                    <i class='bx bx-reset'></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="mb-4 row">
                <div class="col-md-3">
                    <div class="border card radius-10 bg-light-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Total Revenue</p>
                                    <h4 class="my-1 text-success">{{ number_format($report['total_revenue'], 2) }} ASR</h4>
                                    <p class="mb-0 font-13">For selected period</p>
                                </div>
                                <div class="text-success ms-auto font-35">
                                    <i class='bx bx-dollar'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border card radius-10 bg-light-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Pending Payments</p>
                                    <h4 class="my-1 text-warning">{{ number_format($report['pending_payments'], 2) }} ASR</h4>
                                    <p class="mb-0 font-13">Awaiting payment</p>
                                </div>
                                <div class="text-warning ms-auto font-35">
                                    <i class='bx bx-time'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border card radius-10 bg-light-danger">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Overdue Amount</p>
                                    <h4 class="my-1 text-danger">{{ number_format($report['overdue_amount'], 2) }} ASR</h4>
                                    <p class="mb-0 font-13">Past due date</p>
                                </div>
                                <div class="text-danger ms-auto font-35">
                                    <i class='bx bx-error'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border card radius-10 bg-light-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Monthly Revenue</p>
                                    <h4 class="my-1 text-info">{{ number_format($report['monthly_revenue'], 2) }} ASR</h4>
                                    <p class="mb-0 font-13">Current month</p>
                                </div>
                                <div class="text-info ms-auto font-35">
                                    <i class='bx bx-calendar'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="card radius-10">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Payment History</h6>
                        </div>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                                <i class='bx bx-printer'></i> Print Report
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Created Date</th>
                                    <th>Invoice #</th>
                                    <th>Contract</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['payment_history'] as $payment)
                                <tr>
                                    <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $payment->invoice_number }}</td>
                                    <td>{{ $payment->contract->contract_number ?? 'N/A' }}</td>
                                    <td>{{ number_format($payment->payment_amount, 2) }} ASR</td>
                                    <td>{{ $payment->due_date }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->payment_status == 'paid' ? 'success' : ($payment->payment_status == 'overdue' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($payment->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('finance.payments.show', $payment->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class='bx bx-show'></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $report['payment_history']->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
