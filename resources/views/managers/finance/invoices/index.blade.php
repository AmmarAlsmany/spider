@extends('shared.dashboard')
@section('content')
    <div class="page-content">
        @if (session('error'))
            <div class="mb-3 alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-1"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
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
                        <h6 class="mb-0">{{ __('finance_views.invoices_list') }}</h6>
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
                                <th>{{ __('finance_views.status') }}</th>
                                <th>{{ __('finance_views.due_date') }}</th>
                                <th>{{ __('finance_views.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->customer->name }}</td>
                                    <td>{{ $invoice->contract->salesRepresentative->name }}</td>
                                    <td>{{ $invoice->contract->contract_number }}</td>
                                    <td>{{ number_format($invoice->payment_amount, 2) }} {{ __('finance_views.currency_sar') }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $invoice->payment_status == 'paid' ? 'success' : ($invoice->payment_status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ __('finance_views.' . $invoice->payment_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $invoice->due_date }}</td>
                                    <td>
                                        <a href="{{ route('payment.show', $invoice->id) }}"
                                            class="btn btn-primary btn-sm">{{ __('finance_views.view') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $invoices->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    </div>
@endsection
