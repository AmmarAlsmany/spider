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
                    <h6 class="mb-0">{{ __('finance_views.financial_report') }}</h6>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Date Range Filter -->
            <div class="mb-4">
                <form action="{{ route('finance.reports.financial') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('finance_views.start_date') }}</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('finance_views.end_date') }}</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('finance_views.contract_number') }}</label>
                        <input type="text" name="contract_number" class="form-control" value="{{ request('contract_number') }}" placeholder="{{ __('finance_views.contract_number') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="gap-2 d-flex">
                            <button type="submit" class="btn btn-primary flex-grow-1">{{ __('finance_views.generate_report') }}</button>
                            @if(request('contract_number') || request('start_date') || request('end_date'))
                                <a href="{{ route('finance.reports.financial') }}" class="btn btn-outline-secondary">
                                    <i class='bx bx-reset'></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="mb-4 row">
                <div class="col-md-3">
                    <div class="border card radius-10 bg-light-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{ __('finance_views.total_revenue') }}</p>
                                    <h4 class="my-1 text-success">{{ number_format($report['total_revenue'], 2) }} {{ __('finance_views.currency_sar') }}</h4>
                                    <p class="mb-0 font-13">{{ __('finance_views.for_selected_period') }}</p>
                                </div>
                                <div class="text-success ms-auto font-35">
                                    <i class='bx bx-dollar'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border card radius-10 bg-light-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{ __('finance_views.pending_payments') }}</p>
                                    <h4 class="my-1 text-warning">{{ number_format($report['pending_payments'], 2) }} {{ __('finance_views.currency_sar') }}</h4>
                                    <p class="mb-0 font-13">{{ __('finance_views.awaiting_payment') }}</p>
                                </div>
                                <div class="text-warning ms-auto font-35">
                                    <i class='bx bx-time'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border card radius-10 bg-light-danger">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{ __('finance_views.overdue_amount') }}</p>
                                    <h4 class="my-1 text-danger">{{ number_format($report['overdue_amount'], 2) }} {{ __('finance_views.currency_sar') }}</h4>
                                    <p class="mb-0 font-13">{{ __('finance_views.past_due_date') }}</p>
                                </div>
                                <div class="text-danger ms-auto font-35">
                                    <i class='bx bx-error'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border card radius-10 bg-light-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{ __('finance_views.monthly_revenue') }}</p>
                                    <h4 class="my-1 text-info">{{ number_format($report['monthly_revenue'], 2) }} {{ __('finance_views.currency_sar') }}</h4>
                                    <p class="mb-0 font-13">{{ __('finance_views.current_month') }}</p>
                                </div>
                                <div class="text-info ms-auto font-35">
                                    <i class='bx bx-calendar'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="card radius-10">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">{{ __('finance_views.payment_history') }}</h6>
                        </div>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printFinancialReport()">
                                <i class='bx bx-printer'></i> {{ __('finance_views.print_report') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="printable-area">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('finance_views.created_date') }}</th>
                                        <th>{{ __('finance_views.invoice_number') }}</th>
                                        <th>{{ __('finance_views.contract') }}</th>
                                        <th>{{ __('finance_views.amount') }}</th>
                                        <th>{{ __('finance_views.due_date') }}</th>
                                        <th>{{ __('finance_views.status') }}</th>
                                        <th class="actions-column">{{ __('finance_views.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report['payment_history'] as $payment)
                                    <tr>
                                        <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $payment->invoice_number }}</td>
                                        <td>{{ $payment->contract->contract_number ?? __('finance_views.na') }}</td>
                                        <td>{{ number_format($payment->payment_amount, 2) }} {{ __('finance_views.currency_sar') }}</td>
                                        <td>{{ $payment->due_date }}</td>
                                        <td>
                                            <span class="badge bg-{{ $payment->payment_status == 'paid' ? 'success' : ($payment->payment_status == 'overdue' ? 'danger' : 'warning') }}">
                                                {{ __('finance_views.' . $payment->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="actions-column">
                                            <a href="{{ route('finance.payments.show', $payment->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class='bx bx-show'></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $report['payment_history']->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    function printFinancialReport() {
        // Create an iframe element
        var printFrame = document.createElement('iframe');
        
        // Make it invisible
        printFrame.style.position = 'fixed';
        printFrame.style.right = '0';
        printFrame.style.bottom = '0';
        printFrame.style.width = '0';
        printFrame.style.height = '0';
        printFrame.style.border = '0';
        
        document.body.appendChild(printFrame);
        
        // Get the iframe document
        var frameDoc = printFrame.contentWindow || printFrame.contentDocument.document || printFrame.contentDocument;
        
        // Write the HTML content to the iframe
        frameDoc.document.open();
        frameDoc.document.write('<html><head><title>{{ __('finance_views.financial_report') }}</title>');
        frameDoc.document.write('<link rel="stylesheet" href="{{ asset("assets/css/bootstrap.min.css") }}" type="text/css" />');
        frameDoc.document.write('<style>body { padding: 20px; } .actions-column { display: none; }</style>');
        frameDoc.document.write('</head><body>');
        frameDoc.document.write('<h3 class="mb-4 text-center">{{ __('finance_views.financial_report') }}</h3>');
        
        // Add the table HTML
        var tableHtml = document.querySelector('#printable-area .table-responsive').innerHTML;
        frameDoc.document.write('<div class="table-responsive">' + tableHtml + '</div>');
        
        frameDoc.document.write('</body></html>');
        frameDoc.document.close();
        
        // Use setTimeout to ensure the content is loaded before printing
        setTimeout(function () {
            frameDoc.focus();
            frameDoc.print();
            
            // Remove the iframe after printing
            setTimeout(function() {
                document.body.removeChild(printFrame);
            }, 1000);
        }, 500);
    }
</script>
@endpush