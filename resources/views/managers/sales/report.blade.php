@extends('shared.dashboard')
@push('style')
    <style>
        @media print {
            .badge {
                border: none !important;
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }

            .table {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            .table th,
            .table td {
                background-color: #fff !important;
                border: 1px solid #dee2e6 !important;
                padding: 0.5rem !important;
            }

            .alert-info {
                background-color: #cff4fc !important;
                border-color: #b6effb !important;
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }
        }
    </style>
@endpush
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
        <div class="row">
            <div class="col-12">
                <div class="mb-4 d-flex align-items-center">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
                        <i class="bx bx-arrow-back"></i> {{ __('sales_views.back') }}
                    </a>
                    <h4 class="mb-0 text-primary"><i class="bx bx-bar-chart"></i> {{ __('sales_views.sales_report') }}</h4>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="mb-4 alert alert-info flex-grow-1 me-3">
                        <h5 class="mb-2"><i class="bi bi-calendar-check me-2"></i>{{ $periodInfo['period_label'] }}</h5>
                        <p class="mb-0">
                            <small>
                                {{ __('sales_views.report_period') }}: {{ \Carbon\Carbon::parse($periodInfo['start_date'])->format('F d, Y') }} -
                                {{ \Carbon\Carbon::parse($periodInfo['end_date'])->format('F d, Y') }}
                            </small>
                        </p>
                    </div>
                    <div>
                        <button onclick="downloadPDF()" class="gap-2 btn btn-success d-flex align-items-center">
                            <i class="bx bx-download"></i> {{ __('sales_views.download_pdf') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contract Statistics -->
        <div class="mb-4 row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0 text-primary"><i class="bx bx-bar-chart"></i> {{ __('sales_views.contract_statistics') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $contractStats['total_contracts'] }}</h3>
                                        <p>{{ __('sales_views.total_contracts') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $contractStats['active_contracts'] }}</h3>
                                        <p>{{ __('sales_views.active_contracts') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $contractStats['pending_contracts'] }}</h3>
                                        <p>{{ __('sales_views.pending_contracts') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3>{{ $contractStats['completed_contracts'] }}</h3>
                                        <p>{{ __('sales_views.completed_contracts') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ $contractStats['cancelled_contracts'] }}</h3>
                                        <p>{{ __('sales_views.cancelled_contracts') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="mb-4 row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0 text-primary"><i class="bx bx-bar-chart"></i> {{ __('sales_views.financial_summary') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ __('sales_views.total_contract_value') }}</span>
                                        <span
                                            class="info-box-number">{{ number_format($financialSummary['total_contract_value'], 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ __('sales_views.total_paid') }}</span>
                                        <span
                                            class="info-box-number text-success">{{ number_format($financialSummary['total_paid'], 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ __('sales_views.total_pending') }}</span>
                                        <span
                                            class="info-box-number text-warning">{{ number_format($financialSummary['total_pending'], 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ __('sales_views.total_overdue') }}</span>
                                        <span
                                            class="info-box-number text-danger">{{ number_format($financialSummary['total_overdue'], 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contracts List -->
        <div class="mb-4 row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0 text-primary"><i class="bx bx-bar-chart"></i> {{ __('sales_views.contracts_list') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('sales_views.contract_id') }}</th>
                                        <th>{{ __('sales_views.start_date') }}</th>
                                        <th>{{ __('sales_views.end_date') }}</th>
                                        <th>{{ __('sales_views.client') }}</th>
                                        <th>{{ __('sales_views.type') }}</th>
                                        <th>{{ __('sales_views.value') }}</th>
                                        <th>{{ __('sales_views.status') }}</th>
                                        <th>{{ __('sales_views.created_date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contracts->flatten() as $contract)
                                        <tr>
                                            <td>{{ $contract->contract_number ?? 'N/A' }}</td>
                                            <td>{{ date('Y-m-d', strtotime($contract->contract_start_date)) }}</td>
                                            <td>{{ date('Y-m-d', strtotime($contract->contract_end_date)) }}</td>
                                            <td>{{ $contract->customer->name ?? 'N/A' }}</td>
                                            <td>{{ $contract->type->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($contract->contract_price, 2) }}</td>
                                            <td>
                                                <span
                                                    class="badge rounded-pill bg-{{ $contract->contract_status == 'pending'
                                                        ? 'warning'
                                                        : ($contract->contract_status == 'completed'
                                                            ? 'primary'
                                                            : ($contract->contract_status == 'approved'
                                                                ? 'success'
                                                                : 'danger')) }} px-3 py-2">
                                                    <i
                                                        class="bx {{ $contract->contract_status == 'pending'
                                                            ? 'bx-time'
                                                            : ($contract->contract_status == 'completed'
                                                                ? 'bx-check-double'
                                                                : ($contract->contract_status == 'approved'
                                                                    ? 'bx-check'
                                                                    : 'bx-x')) }} me-1"></i>
                                                    {{ ucfirst($contract->contract_status) }}
                                                </span>
                                            </td>
                                            <td>{{ $contract->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0 text-primary"><i class="bx bx-bar-chart"></i> {{ __('sales_views.payments_list') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('sales_views.payment_id') }}</th>
                                        <th>{{ __('sales_views.contract') }}</th>
                                        <th>{{ __('sales_views.amount_without_vat') }}</th>
                                        <th>{{ __('sales_views.vat') }}</th>
                                        <th>{{ __('sales_views.amount_with_vat') }}</th>
                                        <th>{{ __('sales_views.status') }}</th>
                                        <th>{{ __('sales_views.due_date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments->flatten() as $payment)
                                        <tr>
                                            <td>{{ $payment->id }}</td>
                                            <td>{{ $payment->contract->contract_number }}</td>
                                            <td>{{ number_format($payment->payment_amount / 1.15, 2) }}</td>
                                            <td>{{ number_format(($payment->payment_amount / 1.15) * 0.15, 2) }}</td>
                                            <td>{{ number_format($payment->payment_amount, 2) }}</td>
                                            <td>
                                                <span
                                                    class="badge rounded-pill bg-{{ $payment->payment_status == 'paid'
                                                        ? 'success'
                                                        : ($payment->payment_status == 'unpaid'
                                                            ? 'warning'
                                                            : ($payment->payment_status == 'overdue'
                                                                ? 'danger'
                                                                : 'secondary')) }} px-3 py-2">
                                                    <i
                                                        class="bx {{ $payment->payment_status == 'paid'
                                                            ? 'bx-check'
                                                            : ($payment->payment_status == 'unpaid'
                                                                ? 'bx-time'
                                                                : ($payment->payment_status == 'overdue'
                                                                    ? 'bx-x'
                                                                    : 'bx-question-mark')) }} me-1"></i>
                                                    {{ ucfirst($payment->payment_status) }}
                                                </span>
                                            </td>
                                            <td>{{ $payment->due_date }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function downloadPDF() {
            // Show loading indicator
            const btn = document.querySelector('button[onclick="downloadPDF()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> {{ __('sales_views.generating_pdf') }}';
            btn.disabled = true;

            // Get the current URL parameters
            const urlParams = new URLSearchParams(window.location.search);

            // Make request to generate PDF
            fetch(`/reports/sales/pdf?${urlParams.toString()}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.blob();
                })
                .then(blob => {
                    // Create download link
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = '{{ $periodInfo['period_label'] }}.pdf';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);

                    // Restore button state
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                })
                .catch(error => {
                    console.error('PDF generation failed:', error);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    alert('{{ __('sales_views.pdf_generation_failed') }}');
                });
        }
    </script>
@endpush
