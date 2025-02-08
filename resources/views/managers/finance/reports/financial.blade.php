@extends('shared.dashboard')
@section('content')
<div class="page-content">
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
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">Generate Report</button>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="mb-4 row">
                <div class="col-md-4">
                    <div class="border card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Total Revenue</p>
                                    <h4 class="my-1">{{ number_format($report['total_revenue'], 2) }} ASR</h4>
                                </div>
                                <div class="text-primary ms-auto font-35">
                                    <i class='bx bx-dollar'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Pending Payments</p>
                                    <h4 class="my-1">{{ number_format($report['pending_payments'], 2) }} ASR</h4>
                                </div>
                                <div class="text-warning ms-auto font-35">
                                    <i class='bx bx-time'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Monthly Revenue</p>
                                    <h4 class="my-1">{{ number_format($report['monthly_revenue'], 2) }} ASR</h4>
                                </div>
                                <div class="text-success ms-auto font-35">
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
                    <h6 class="mb-0">Payment History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Created Date</th>
                                    <th>Invoice #</th>
                                    <th>Contract</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['payment_history'] as $payment)
                                <tr>    
                                    <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $payment->invoice_number }}</td>
                                    <td>{{ $payment->contract->contract_number }}</td>
                                    <td>{{ number_format($payment->payment_amount, 2) }} ASR</td>
                                    <td>{{ $payment->due_date }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->payment_status == 'paid' ? 'success' : 'warning' }}">
                                            {{ ucfirst($payment->payment_status) }}
                                        </span>
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
@endsection
