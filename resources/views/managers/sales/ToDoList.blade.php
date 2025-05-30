@extends('shared.dashboard')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        <div class="mb-4 page-breadcrumb d-flex align-items-center">
            <div class="pe-3 breadcrumb-title d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
                    <i class="bx bx-arrow-back"></i> {{ __('sales_views.back') }}
                </a>
            </div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('sales.dashboard') }}" class="text-decoration-none"><i
                                    class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active text-muted" aria-current="page">{{ __('sales_views.to_do_list') }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="shadow-sm card">
            <div class="card-header">
                <h4 class="mb-0 text-primary"><i class="bx bx-store-alt"></i> {{ __('sales_views.sales_profile') }}</h4>
            </div>
            <div class="card-body">
                <div class="mb-4 row g-3">
                    <div class="col-md-4">
                        <form method="GET" action="{{ route('sales.todo') }}" class="h-100">
                            @csrf
                            <div class="p-3 rounded h-100 bg-light">
                                <label for="filter" class="form-label text-muted"><i class="bi bi-funnel"></i> {{ __('sales_views.filter_by') }}</label>
                                <select name="filter" id="filter" class="border-0 shadow-sm form-select"
                                    onchange="this.form.submit()">
                                    <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>{{ __('sales_views.all_time') }}</option>
                                    <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>{{ __('sales_views.today') }}</option>
                                    <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>{{ __('sales_views.this_month') }}</option>
                                    <option value="year" {{ $filter == 'year' ? 'selected' : '' }}>{{ __('sales_views.this_year') }}</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 rounded h-100 d-flex align-items-center justify-content-center bg-light">
                            <a href="{{ route('contract.index') }}" class="shadow-sm btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle me-2"></i> {{ __('sales_views.create_new_contract') }}
                            </a>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 rounded h-100 d-flex align-items-center justify-content-center bg-light">
                            <a href="#" class="shadow-sm btn btn-success btn-lg" data-bs-toggle="modal"
                                data-bs-target="#salesReportModal">
                                <i class="bi bi-graph-up me-2"></i> {{ __('sales_views.sales_reports') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Display Filtered Payments -->
                <div class="table-responsive">
                    <table class="table border table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('sales_views.client_name') }}</th>
                                <th>{{ __('sales_views.client_phone') }}</th>
                                <th>{{ __('sales_views.contract_number') }}</th>
                                <th>{{ __('sales_views.payment_date') }}</th>
                                <th>{{ __('sales_views.amount') }}</th>
                                <th>{{ __('sales_views.payment_status') }}</th>
                                <th>{{ __('sales_views.payment_method') }}</th>
                                <th>{{ __('sales_views.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr>
                                    <td><a
                                            href="{{ route('view.my.clients.details', ['id' => $payment->customer->id]) }}">{{ $payment->customer->name }}</a>
                                    </td>
                                    <td> <a href="tel:{{ $payment->customer->phone }}">{{ $payment->customer->phone }}</a>
                                    </td>
                                    <td><a href="{{ route('contract.show.details', ['id' => $payment->contract->id]) }}"
                                            {{-- chnage color depending on the contract status if approved or pending --}}
                                            @if ($payment->contract->contract_status == 'approved' || $payment->contract->contract_status == 'pending') rel="noopener noreferrer" class="text-primary">{{ $payment->contract->contract_number }}</a></td>
                                @else
                                    rel="noopener noreferrer" class="text-danger">{{ $payment->contract->contract_number }}</a></td> @endif
                                            <td>{{ $payment->due_date }}</td>
                                    <td>{{ $payment->payment_amount }}</td>
                                    <td class="{{ $payment->payment_status == 'paid' ? 'text-success' : 'text-danger' }}">
                                        {{ $payment->payment_status }}</td>
                                    <td>{{ $payment->payment_method ? ucfirst($payment->payment_method) : 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <!-- View Invoice Button -->
                                            <a href="{{ route('payment.show', $payment->id) }}"
                                                class="btn btn-info btn-sm me-2 d-flex align-items-center">
                                                <i class="bx bx-file"></i> <span class="d-none d-sm-inline">{{ __('sales_views.view_invoice') }}</span>
                                            </a>

                                            <!-- Mark as Paid Button -->
                                            @if (
                                                ($payment->payment_status != 'paid' && $payment->contract->contract_status == 'approved') ||
                                                    $payment->contract->contract_status == 'pending')
                                                <button type="button"
                                                    class="btn btn-success btn-sm me-2 d-flex align-items-center"
                                                    onclick="markAsPaid({{ $payment->id }})">
                                                    <i class="bx bx-check-circle"></i> <span class="d-none d-sm-inline">{{ __('sales_views.mark_as_paid') }}</span>
                                                </button>
                                            @endif
                                            @if (Carbon\Carbon::parse($payment->due_date)->lte(now()) &&
                                                    $payment->payment_status != 'paid' &&
                                                    $payment->contract->contract_status == 'approved' &&
                                                    $payment->contract->contract_status == 'pending')
                                                <button type="button" onclick="stopContract({{ $payment->contract->id }})"
                                                    class="gap-2 btn btn-danger d-flex align-items-center">
                                                    <i class="bx bx-stop-circle"></i> <span class="d-none d-sm-inline">{{ __('sales_views.stop_contract') }}</span>
                                                </button>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">{{ __('sales_views.no_payments_available') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $payments->appends(['filter' => $filter])->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark as Paid Modal -->
    <div class="modal fade" id="markAsPaidModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('sales_views.mark_as_paid') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="markAsPaidForm">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" id="paymentId" name="paymentId">
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">{{ __('sales_views.payment_method') }}</label>
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                <option value="bank transfer">{{ __('sales_views.bank_transfer') }}</option>
                                <option value="cash">{{ __('sales_views.cash') }}</option>
                            </select>
                            <div class="invalid-feedback">{{ __('sales_views.select_payment_method_error') }}</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('sales_views.close') }}</button>
                    <button type="submit" form="markAsPaidForm" class="btn btn-success">{{ __('sales_views.mark_as_paid') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Report Modal -->
    <div class="modal fade" id="salesReportModal" tabindex="-1" aria-labelledby="salesReportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="text-white modal-header bg-success">
                    <h5 class="modal-title" id="salesReportModalLabel">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>{{ __('sales_views.generate_sales_report') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('sales.generate-report') }}" method="GET">
                        @csrf
                        <div class="mb-4">
                            <label for="report_type" class="form-label text-muted">
                                <i class="bi bi-file-text me-2"></i>Report Type
                            </label>
                            <select class="shadow-sm form-select" id="report_type" name="report_type" required
                                onchange="toggleDateInputs()">
                                <option value="">Select Report Type</option>
                                <option value="daily">Daily Sales Report</option>
                                <option value="monthly">Monthly Sales Report</option>
                                <option value="quarterly">Quarterly Sales Report</option>
                                <option value="annual">Annual Sales Report</option>
                                <option value="custom">Custom Period Report</option>
                            </select>
                        </div>

                        <div id="date-inputs" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label text-muted">
                                            <i class="bi bi-calendar-event me-2"></i>Start Date
                                        </label>
                                        <input type="date" class="shadow-sm form-control" id="start_date"
                                            name="start_date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label text-muted">
                                            <i class="bi bi-calendar-event me-2"></i>End Date
                                        </label>
                                        <input type="date" class="shadow-sm form-control" id="end_date"
                                            name="end_date">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-0 pb-0 border-0 modal-footer">
                            <button type="button" class="shadow-sm btn btn-light" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i>Close
                            </button>
                            <button type="submit" class="shadow-sm btn btn-success">
                                <i class="bi bi-file-earmark-arrow-down me-2"></i>Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .card {
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .form-select,
        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
        }

        .modal-content {
            border: none;
            border-radius: 15px;
        }

        .modal-header {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background-color: #f8f9fa;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>
@endpush
@push('scripts')
    <script>
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]').content;
        }

        function toggleDateInputs() {
            const reportType = document.getElementById('report_type').value;
            const dateInputs = document.getElementById('date-inputs');
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            if (reportType === 'custom') {
                dateInputs.style.display = 'block';
                startDate.required = true;
                endDate.required = true;
            } else {
                dateInputs.style.display = 'none';
                startDate.required = false;
                endDate.required = false;
            }
        }

        function markAsPaid(paymentId) {
            const modal = new bootstrap.Modal(document.getElementById('markAsPaidModal'));
            document.getElementById('paymentId').value = paymentId;
            modal.show();
        }

        document.getElementById('markAsPaidForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const paymentId = document.getElementById('paymentId').value;
            const paymentMethod = document.getElementById('payment_method').value;

            fetch(`/Payments/${paymentId}/mark-as-paid`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        payment_method: paymentMethod
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Reload the page to show updated status
                        window.location.reload();
                    } else {
                        alert(data.message || 'Error updating payment status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating payment status. Please try again.');
                });
        });

        function stopContract(contractId) {
            if (confirm('Are you sure you want to stop this contract?')) {
                fetch(`/sales/stop-Contract/${contractId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Failed to stop contract. Please try again.');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        alert(data.message || 'Contract stopped successfully');
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'An error occurred while stopping the contract. Please try again.');
                    });
            }
        }
    </script>
@endpush
