@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Invoices List</h6>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Client</th>
                            <th>Sales Agent</th>
                            <th>Contract</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->customer->name }}</td>
                            <td>{{ $invoice->contract->salesRepresentative->name }}</td>
                            <td>{{ $invoice->contract->contract_number }}</td>
                            <td>{{ number_format($invoice->payment_amount, 2) }} SAR</td>
                            <td>
                                <span class="badge bg-{{ $invoice->payment_status == 'paid' ? 'success' : ($invoice->payment_status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($invoice->payment_status) }}
                                </span>
                            </td>
                            <td>{{ $invoice->due_date }}</td>
                            <td>
                                <a href="{{ route('payment.show', $invoice->id) }}" class="btn btn-primary btn-sm">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
