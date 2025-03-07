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
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">{{ __('payment_details.title') }}</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}"><i
                                class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('client.show') }}">{{
                            __('payment_details.breadcrumb.contracts') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('client.contract.details', $contract->id) }}">{{
                            __('payment_details.breadcrumb.contract_number', ['number' => $contract->contract_number])
                            }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('payment_details.breadcrumb.payments')
                        }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Payment Summary Card -->
    <div class="row">
        <div class="col-12">
            <div class="border-0 border-4 card border-start border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">{{ __('payment_details.summary.total_value') }}</p>
                            <h4 class="my-1">{{ number_format($contract->contract_price, 2) }} {{
                                __('payment_details.summary.currency') }}</h4>
                            <p class="mb-0 font-13">{{ __('payment_details.summary.contract_number', ['number' =>
                                $contract->contract_number]) }}</p>
                        </div>
                        <div class="ms-auto">
                            <div class="row g-3">
                                <div class="col-auto">
                                    <div class="text-end">
                                        <p class="mb-0 text-success">{{ __('payment_details.summary.paid_amount') }}</p>
                                        <h5 class="my-1 text-success">{{
                                            number_format($contract->payments->where('payment_status',
                                            'paid')->sum('payment_amount'), 2) }} {{
                                            __('payment_details.summary.currency') }}</h5>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="text-end">
                                        <p class="mb-0 text-warning">{{ __('payment_details.summary.pending_amount') }}
                                        </p>
                                        <h5 class="my-1 text-warning">{{
                                            number_format($contract->payments->where('payment_status',
                                            'unpaid')->sum('payment_amount'), 2) }} {{
                                            __('payment_details.summary.currency') }}</h5>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="text-end">
                                        <p class="mb-0 text-danger">{{ __('payment_details.summary.overdue_amount') }}
                                        </p>
                                        <h5 class="my-1 text-danger">{{
                                            number_format($contract->payments->where('payment_status',
                                            'unpaid')->where('due_date', '<', now())->sum('payment_amount'), 2) }} {{
                                                __('payment_details.summary.currency') }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Progress -->
    <div class="mb-4 row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('payment_details.payment_progress.title') }}</h5>
                    @php
                    $totalAmount = $contract->payments->sum('payment_amount');
                    $paidAmount = $contract->payments->where('payment_status', 'paid')->sum('payment_amount');
                    $progressPercentage = $totalAmount > 0 ? ($paidAmount / $totalAmount) * 100 : 0;
                    @endphp
                    <div class="progress" style="height: 24px;">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $progressPercentage }}%;" aria-valuenow="{{ $progressPercentage }}"
                            aria-valuemin="0" aria-valuemax="100">
                            {{ number_format($progressPercentage, 1) }}%
                        </div>
                    </div>
                    <div class="mt-2 text-muted">
                        {{ number_format($paidAmount, 2) }} {{ __('payment_details.summary.currency') }} {{
                        __('payment_details.payment_progress.paid_out_of') }} {{ number_format($totalAmount, 2) }} {{
                        __('payment_details.summary.currency') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Schedule -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="bg-transparent card-header">
                    <h5 class="mb-0">{{ __('payment_details.payment_schedule.title') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('payment_details.payment_schedule.columns.payment_date') }}</th>
                                    <th>{{ __('payment_details.payment_schedule.columns.amount') }}</th>
                                    <th>{{ __('payment_details.payment_schedule.columns.status') }}</th>
                                    <th>{{ __('payment_details.payment_schedule.columns.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contract->payments->sortBy('due_date') as $payment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}</td>
                                    <td>{{ number_format($payment->payment_amount, 2) }} {{
                                        __('payment_details.summary.currency') }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $payment->payment_status == 'paid' ? 'success' : 
                                            ($payment->payment_status == 'pending' ? 'warning' : 
                                            ($payment->payment_status == 'overdue' ? 'danger' : 'secondary')) 
                                        }}">
                                            {{ __('payment_details.payment_schedule.status.' . $payment->payment_status)
                                            }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($payment->payment_status != 'paid')
                                        @php
                                        $hasPendingRequest = $payment->postponementRequests()->where('status',
                                        'pending')->exists();
                                        $lastRequest = $payment->postponementRequests()->latest()->first();
                                        @endphp
                                        <div class="gap-2 d-flex align-items-center">
                                            @if($lastRequest)
                                            <div class="small text-muted">
                                                <i class="bx bx-info-circle"></i>
                                                {{ __('payment_details.payment_schedule.last_request') }}: {{
                                                $lastRequest->status }}
                                                @if($lastRequest->status === 'approved')
                                                {{ __('payment_details.payment_schedule.approved_on') }} {{
                                                \Carbon\Carbon::parse($lastRequest->approved_at)->format('M d, Y') }}
                                                @elseif($lastRequest->status === 'rejected')
                                                ({{ $lastRequest->reason }})
                                                @endif
                                            </div>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#postponePaymentModal"
                                                data-payment-id="{{ $payment->id }}"
                                                data-current-date="{{ $payment->due_date }}"
                                                data-payment-amount="{{ $payment->payment_amount }}" {{
                                                $hasPendingRequest ? 'disabled' : '' }}
                                                title="{{ $hasPendingRequest ? __('payment_details.payment_schedule.request_pending') : __('payment_details.payment_schedule.request_postpone') }}">
                                                <i class="bx bx-time"></i> {{ $hasPendingRequest ?
                                                __('payment_details.payment_schedule.request_pending') :
                                                __('payment_details.payment_schedule.request_postpone') }}
                                            </button>
                                        </div>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                            data-bs-target="#paymentDetailsModal" data-payment-id="{{ $payment->id }}">
                                            <i class="bx bx-info-circle"></i> {{
                                            __('payment_details.payment_schedule.details') }}
                                        </button>
                                        {{-- view the invoice --}}
                                        <button class="gap-1 btn btn-sm btn-warning d-flex align-items-center"
                                            onclick="viewInvoice({{ $payment->id }})">
                                            <i class="bx bx-receipt"></i> <span class="d-none d-sm-inline">
                                                {{ __('contract_details_new.button_view_invoice') }}
                                            </span>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center">
                                        <i class="bx bx-money text-muted" style="font-size: 48px;"></i>
                                        <p class="mt-2">{{ __('payment_details.payment_schedule.no_payments') }}</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Postpone Payment Modal -->
<div class="modal fade" id="postponePaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('payment_details.postpone_modal.title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('client.payment.postpone', $contract->id) }}" method="POST">
                @csrf
                <input type="hidden" name="payment_id" id="postpone_payment_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('payment_details.postpone_modal.current_date') }}</label>
                        <input type="text" class="form-control" id="current_payment_date" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('payment_details.postpone_modal.payment_amount') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ __('payment_details.summary.currency') }}</span>
                            <input type="text" class="form-control" id="payment_amount" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('payment_details.postpone_modal.new_date') }}</label>
                        <input type="date" class="form-control" name="requested_date" required
                            min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('payment_details.postpone_modal.reason') }}</label>
                        <textarea class="form-control" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{
                        __('payment_details.postpone_modal.cancel') }}</button>
                    <button type="submit" class="btn btn-warning">{{ __('payment_details.postpone_modal.submit')
                        }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('payment_details.payment_details_modal.title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="paymentDetailsContent">
                    {{ __('payment_details.payment_details_modal.loading') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{
                    __('payment_details.payment_details_modal.close') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Handle Postpone Payment Modal
    const postponePaymentModal = document.getElementById('postponePaymentModal');
    if (postponePaymentModal) {
        postponePaymentModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const paymentId = button.getAttribute('data-payment-id');
            const currentDate = button.getAttribute('data-current-date');
            const paymentAmount = button.getAttribute('data-payment-amount');
            
            this.querySelector('#postpone_payment_id').value = paymentId;
            this.querySelector('#current_payment_date').value = new Date(currentDate).toLocaleDateString();
            this.querySelector('#payment_amount').value = Number(paymentAmount).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        });
    }

    // Handle Payment Details Modal
    const paymentDetailsModal = document.getElementById('paymentDetailsModal');
    if (paymentDetailsModal) {
        paymentDetailsModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const paymentId = button.getAttribute('data-payment-id');
            const contentDiv = this.querySelector('#paymentDetailsContent');
            
            fetch(`/client/payment/${paymentId}/details`)
                .then(response => response.text())
                .then(html => {
                    contentDiv.innerHTML = html;
                })
                .catch(error => {
                    contentDiv.innerHTML = '{{ __('payment_details.payment_details_modal.error') }}';
                });
        });
    }
});

function viewInvoice(paymentId) {
        window.location.href = `/Payment/view Payment Details/${paymentId}`;
    }
</script>
@endpush
@endsection