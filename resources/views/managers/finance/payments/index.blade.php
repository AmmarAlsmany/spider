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
                    <h6 class="mb-0">{{ __('finance_views.payments_list') }}</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('finance.payments.pending') }}" class="btn btn-warning">{{ __('finance_views.view_pending_payments') }}</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('finance_views.payment_id') }}</th>
                            <th>{{ __('finance_views.invoice_number') }}</th>
                            <th>{{ __('finance_views.sales_agent') }}</th>
                            <th>{{ __('finance_views.contract') }}</th>
                            <th>{{ __('finance_views.amount') }}</th>
                            <th>{{ __('finance_views.created_date') }}</th>
                            <th>{{ __('finance_views.status') }}</th>
                            <th>{{ __('finance_views.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $count = 1;
                        @endphp
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $count++ }}</td>
                            <td>{{ $payment->invoice_number }}</td>
                            <td>{{ $payment->contract->salesRepresentative->name }} <small>({{ $payment->contract->salesRepresentative->email }})</small></td>
                            <td>{{ $payment->contract->contract_number }}</td>
                            <td>{{ number_format($payment->payment_amount, 2) }} {{ __('finance_views.currency_sar') }}</td>
                            <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge bg-{{ $payment->payment_status == 'paid' ? 'success' : 'warning' }}">
                                    {{ __('finance_views.' . $payment->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('payment.show', $payment->id) }}" class="btn btn-primary btn-sm">{{ __('finance_views.view') }}</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $payments->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection
