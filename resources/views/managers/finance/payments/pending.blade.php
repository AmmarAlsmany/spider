@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Pending Payments</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('finance.payments') }}" class="btn btn-secondary">Back to All Payments</a>
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
                            <th>Contract</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($pendingPayments->count() > 0)
                        @foreach($pendingPayments as $payment)
                        <tr>
                            <td>{{ $payment->invoice_number }}</td>
                            <td>{{ $payment->customer->name }}</td>
                            <td>{{ $payment->contract->contract_number }}</td>
                            <td>{{ number_format($payment->payment_amount, 2) }} ASR</td>
                            <td>
                                <span class="{{ strtotime($payment->due_date) < time() ? 'text-danger' : '' }}">
                                    {{ $payment->due_date }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $payment->payment_status == 'overdue' ? 'danger' : 'warning' }}">
                                    {{ ucfirst($payment->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <div class="gap-2 d-flex">
                                    <a href="{{ route('finance.invoices.show', $payment->id) }}" class="btn btn-primary btn-sm">View Invoice</a>
                                    {{-- <form action="{{ route('finance.payments.update-status', $payment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="paid">
                                        <button type="submit" class="btn btn-success btn-sm">Mark as Paid</button>
                                    </form> --}}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="7" class="text-center">No pending payments found</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pendingPayments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
