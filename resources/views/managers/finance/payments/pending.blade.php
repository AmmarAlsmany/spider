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
                    <h6 class="mb-0">{{ __('finance_views.pending_payments') }}</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('finance.payments') }}" class="btn btn-secondary">{{ __('finance_views.back_to_payments') }}</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('finance_views.invoice_number') }}</th>
                            <th>{{ __('finance_views.client') }}</th>
                            <th>{{ __('finance_views.sales_agent') }}</th>
                            <th>{{ __('finance_views.contract') }}</th>
                            <th>{{ __('finance_views.amount') }}</th>
                            <th>{{ __('finance_views.due_date') }}</th>
                            <th>{{ __('finance_views.status') }}</th>
                            <th>{{ __('finance_views.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($pendingPayments->count() > 0)
                        @foreach($pendingPayments as $payment)
                        <tr>
                            <td>{{ $payment->invoice_number }}</td>
                            <td>{{ $payment->customer->name }}</td>
                            <td>{{ $payment->contract->salesRepresentative->name }} <small>({{ $payment->contract->salesRepresentative->email }})</small></td>
                            <td>{{ $payment->contract->contract_number }}</td>
                            <td>{{ number_format($payment->payment_amount, 2) }} {{ __('finance_views.currency_sar') }}</td>
                            <td>
                                <span class="{{ strtotime($payment->due_date) < time() ? 'text-danger' : '' }}">
                                    {{ $payment->due_date }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $payment->payment_status == 'overdue' ? 'danger' : 'warning' }}">
                                    {{ __('finance_views.' . $payment->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <div class="gap-2 d-flex">
                                    <a href="{{ route('payment.show', $payment->id) }}" class="btn btn-primary btn-sm">{{ __('finance_views.view_invoice') }}</a>
                                    {{-- <form action="{{ route('finance.payments.update-status', $payment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="paid">
                                        <button type="submit" class="btn btn-success btn-sm">{{ __('finance_views.mark_as_paid') }}</button>
                                    </form> --}}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="7" class="text-center">{{ __('finance_views.no_pending_payments') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pendingPayments->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection
