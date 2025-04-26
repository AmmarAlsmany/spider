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
                    <h6 class="mb-0">{{ __('finance_views.invoice_details') }}</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('finance.invoices') }}" class="btn btn-secondary">{{ __('finance_views.back_to_invoices') }}</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-4 row">
                <div class="col-sm-6">
                    <h6 class="mb-3">{{ __('finance_views.from') }}</h6>
                    <div>
                        <strong>{{ __('finance_views.company_name') }}</strong>
                    </div>
                    <div>{{ __('finance_views.company_address') }}</div>
                    <div>{{ __('finance_views.company_email') }}</div>
                    <div>{{ __('finance_views.company_phone') }}</div>
                </div>

                <div class="col-sm-6">
                    <h6 class="mb-3">{{ __('finance_views.to') }}</h6>
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
                    <h6 class="mb-3">{{ __('finance_views.invoice_details_section') }}</h6>
                    <div>{{ __('finance_views.invoice_number') }}: <strong>{{ $invoice->invoice_number }}</strong></div>
                    <div>{{ __('finance_views.date') }}: {{ $invoice->created_at->format('Y-m-d') }}</div>
                    <div>{{ __('finance_views.due_date') }}: {{ $invoice->due_date }}</div>
                    <div>{{ __('finance_views.status') }}: 
                        <span class="badge bg-{{ $invoice->payment_status == 'paid' ? 'success' : ($invoice->payment_status == 'pending' ? 'warning' : 'danger') }}">
                            {{ __('finance_views.' . $invoice->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('finance_views.description') }}</th>
                            <th class="text-end">{{ __('finance_views.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ __('finance_views.contract_service') }}: {{ $invoice->contract->contract_number }}</td>
                            <td class="text-end">{{ number_format($invoice->payment_amount, 2) }} {{ __('finance_views.currency_sar') }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-end"><strong>{{ __('finance_views.total') }}:</strong></td>
                            <td class="text-end"><strong>{{ number_format($invoice->payment_amount, 2) }} {{ __('finance_views.currency_sar') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4">
                <h6>{{ __('finance_views.payment_details') }}</h6>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <td>{{ __('finance_views.created_date') }}</td>
                                <td>{{ __('finance_views.amount') }}</td>
                                <td>{{ __('finance_views.payment_method') }}</td>
                                <td>{{ __('finance_views.status') }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                <td>{{ number_format($invoice->payment_amount, 2) }} {{ __('finance_views.currency_sar') }}</td>
                                <td>{{ $invoice->payment_method ?? __('finance_views.na') }}</td>
                                <td>
                                    <span class="badge bg-{{ $invoice->payment_status == 'paid' ? 'success' : 'warning' }}">
                                        {{ __('finance_views.' . $invoice->payment_status) }}
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
