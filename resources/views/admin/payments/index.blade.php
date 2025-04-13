@extends('shared.dashboard')

@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">{{ __('admin.payments.title') }}</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.payments.all_payments') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <h5 class="mb-0">{{ __('admin.payments.title') }}</h5>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.payments.index') }}" class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ __('admin.payments.filter.all') }}
                        </a>
                        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" 
                           class="btn {{ request('status') === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ __('admin.payments.filter.pending') }}
                        </a>
                        <a href="{{ route('admin.payments.index', ['status' => 'paid']) }}"
                           class="btn {{ request('status') === 'paid' ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ __('admin.payments.filter.paid') }}
                        </a>
                        <a href="{{ route('admin.payments.index', ['status' => 'overdue']) }}"
                           class="btn {{ request('status') === 'overdue' ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ __('admin.payments.filter.overdue') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-3 table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('admin.payments.table.invoice') }}</th>
                            <th>{{ __('admin.payments.table.customer') }}</th>
                            <th>{{ __('admin.payments.table.contract') }}</th>
                            <th>{{ __('admin.payments.table.due_date') }}</th>
                            <th>{{ __('admin.payments.table.amount') }}</th>
                            <th>{{ __('admin.payments.table.method') }}</th>
                            <th>{{ __('admin.payments.table.status') }}</th>
                            <th>{{ __('admin.payments.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td><a href="{{ route("payment.show", $payment->id) }}" target="_blank">{{ $payment->invoice_number }}</a></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="ms-2">
                                        <h6 class="mb-0 font-14">{{ $payment->customer->name }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.contracts.show', $payment->contract->id) }}" class="text-primary">
                                    #{{ $payment->contract->contract_number }}
                                </a>
                            </td>
                            <td>{{ $payment->due_date instanceof \Carbon\Carbon ? $payment->due_date->format('Y-m-d') : $payment->due_date }}</td>
                            <td>{{ number_format($payment->payment_amount, 2) }}</td>
                            <td>
                                @if($payment->payment_method)
                                    <span class="badge bg-info">
                                        {{ str_replace('_', ' ', ucfirst($payment->payment_method)) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $payment->payment_status === 'paid' ? 'success' : ($payment->payment_status === 'pending' ? 'warning' : 'danger') }}">
                                    @if($payment->payment_status === 'paid')
                                        {{ __('admin.payments.table.status.paid') }}
                                    @elseif($payment->payment_status === 'pending')
                                        {{ __('admin.payments.table.status.pending') }}
                                    @else
                                        {{ __('admin.payments.table.status.overdue') }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="gap-2 d-flex">
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       data-bs-toggle="tooltip" 
                                       title="{{ __('admin.payments.table.view_details') }}">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-4 text-center">
                                <div class="text-center">
                                    <i class="bx bx-money fs-1 text-muted"></i>
                                    <p class="mt-2">{{ __('admin.payments.table.no_payments') }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                {{ $payments->links("vendor.pagination.custom") }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
