@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Payment Details</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('finance.payments') }}" class="btn btn-secondary">Back to Payments</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-4 row">
                <div class="col-md-6">
                    <h6 class="mb-3">Payment Information</h6>
                    <div class="mb-2">
                        <strong>Payment ID:</strong> {{ $payment->id }}
                    </div>
                    <div class="mb-2">
                        <strong>Amount:</strong> {{ number_format($payment->payment_amount, 2) }} SAR
                    </div>
                    <div class="mb-2">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $payment->payment_status == 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($payment->payment_status) }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <strong>Payment Date:</strong> {{ $payment->created_at->format('Y-m-d H:i:s') }}
                    </div>
                    <div class="mb-2">
                        <strong>Payment Method:</strong> {{ $payment->payment_method }}
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="mb-3">Related Information</h6>
                    <div class="mb-2">
                        <strong>Invoice Number:</strong> 
                        <a href="{{ route('finance.invoices.show', $payment->id) }}">
                            {{ $payment->invoice_number }}
                        </a>
                    </div>
                    <div class="mb-2">
                        <strong>Contract Number:</strong> {{ $payment->contract->contract_number }}
                    </div>
                    <div class="mb-2">
                        <strong>Client:</strong> {{ $payment->contract->customer->name }}
                    </div>
                </div>
            </div>

            {{-- @if($payment->payment_status != 'paid')
            <div class="mt-4">
                <form action="{{ route('finance.payments.update-status', $payment->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="paid">
                    <button type="submit" class="btn btn-success">Mark as Paid</button>
                </form>
            </div>
            @endif --}}
        </div>
    </div>
</div>
@endsection
