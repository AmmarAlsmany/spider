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
                    <h6 class="mb-0">{{ __('finance_views.payment_details') }}</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('finance.payments') }}" class="btn btn-secondary">{{ __('finance_views.back_to_payments') }}</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-4 row">
                <div class="col-md-6">
                    <h6 class="mb-3">{{ __('finance_views.payment_information') }}</h6>
                    <div class="mb-2">
                        <strong>{{ __('finance_views.amount') }}:</strong> {{ number_format($payment->payment_amount, 2) }} {{ __('finance_views.currency_sar') }}
                    </div>
                    <div class="mb-2">
                        <strong>{{ __('finance_views.status') }}:</strong>
                        <span class="badge bg-{{ $payment->payment_status == 'paid' ? 'success' : 'warning' }}">
                            {{ __('finance_views.' . $payment->payment_status) }}
                        </span>
                    </div>
                    @if ($payment->payment_status == 'paid')
                    <div class="mb-2">
                        <strong>{{ __('finance_views.paid_date') }}:</strong> {{ $payment->paid_at }}
                    </div>
                    @endif
                    <div class="mb-2">
                        <strong>{{ __('finance_views.created_date') }}:</strong> {{ $payment->created_at->format('Y-m-d H:i:s') }}
                    </div>
                    {{-- due date --}}
                    <div class="mb-2">
                        <strong>{{ __('finance_views.due_date') }}:</strong> {{ $payment->due_date}}
                    </div>
                    <div class="mb-2">
                        <strong>{{ __('finance_views.payment_method') }}:</strong> {{ $payment->payment_method }}
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="mb-3">{{ __('finance_views.related_information') }}</h6>
                    <div class="mb-2">
                        <strong>{{ __('finance_views.invoice_number') }}:</strong> 
                        <a href="{{ route('payment.show', $payment->id) }}">
                            {{ $payment->invoice_number }}
                        </a>
                    </div>
                    <div class="mb-2">
                        <strong>{{ __('finance_views.contract_number') }}:</strong> {{ $payment->contract->contract_number }}
                    </div>
                    <div class="mb-2">
                        <strong>{{ __('finance_views.client') }}:</strong> {{ $payment->contract->customer->name }}
                    </div>
                </div>
            </div>

            {{-- @if($payment->payment_status != 'paid')
            <div class="mt-4">
                <form action="{{ route('finance.payments.update-status', $payment->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="paid">
                    <button type="submit" class="btn btn-success">{{ __('finance_views.mark_as_paid') }}</button>
                </form>
            </div>
            @endif --}}
        </div>
    </div>
</div>
@endsection
