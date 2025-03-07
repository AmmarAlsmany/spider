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
                    <h6 class="mb-0">Invoice Details</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('finance.invoices') }}" class="btn btn-secondary">Back to Invoices</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-4 row">
                <div class="col-sm-6">
                    <h6 class="mb-3">From:</h6>
                    <div>
                        <strong>Your Company Name</strong>
                    </div>
                    <div>Company Address</div>
                    <div>Email: company@email.com</div>
                    <div>Phone: (123) 456-7890</div>
                </div>

                <div class="col-sm-6">
                    <h6 class="mb-3">To:</h6>
                    <div>
                        <strong>{{ $invoice->customer->name }}</strong>
                    </div>
                    <div>{{ $invoice->customer->address }}</div>
                    <div>Email: {{ $invoice->customer->email }}</div>
                    <div>Phone: {{ $invoice->customer->phone }}</div>
                </div>
            </div>

            <div class="mb-4 row">
                <div class="col-sm-6">
                    <h6 class="mb-3">Invoice Details:</h6>
                    <div>Invoice #: <strong>{{ $invoice->invoice_number }}</strong></div>
                    <div>Date: {{ $invoice->created_at->format('Y-m-d') }}</div>
                    <div>Due Date: {{ $invoice->due_date }}</div>
                    <div>Status: 
                        <span class="badge bg-{{ $invoice->payment_status == 'paid' ? 'success' : ($invoice->payment_status == 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($invoice->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Contract Service: {{ $invoice->contract->contract_number }}</td>
                            <td class="text-end">{{ number_format($invoice->payment_amount, 2) }} ASR</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-end"><strong>Total:</strong></td>
                            <td class="text-end"><strong>{{ number_format($invoice->payment_amount, 2) }} ASR</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4">
                <h6>Payment Details</h6>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <td>Created Date</td>
                                <td>Amount</td>
                                <td>Payment Method</td>
                                <td>Status</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                <td>{{ number_format($invoice->payment_amount, 2) }} ASR</td>
                                <td>{{ $invoice->payment_method ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $invoice->payment_status == 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($invoice->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
