<div class="payment-details">
    <div class="mb-4">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <span class="badge bg-{{ 
                $payment->payment_status == 'paid' ? 'success' : 
                ($payment->payment_status == 'pending' ? 'warning' : 
                ($payment->payment_status == 'overdue' ? 'danger' : 'secondary')) 
            }} px-3 py-2">
                {{ __('payments.status.' . $payment->payment_status) }}
            </span>
            <h6 class="mb-0">{{ number_format($payment->payment_amount, 2) }} SAR</h6>
        </div>
    </div>

    <div class="mb-3">
        <h6 class="mb-2 text-muted">{{ __('payments.payment_information') }}</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th class="bg-light" width="40%">{{ __('payments.payment_date') }}</th>
                    <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}</td>
                </tr>
                @if($payment->payment_method)
                <tr>
                    <th class="bg-light">{{ __('payments.payment_method') }}</th>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                </tr>
                @endif
                @if($payment->paid_at)
                <tr>
                    <th class="bg-light">{{ __('payments.paid_at') }}</th>
                    <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y H:i A') }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    @if($payment->payment_description)
    <div class="mb-3">
        <h6 class="mb-2 text-muted">{{ __('payments.description') }}</h6>
        <div class="p-3 rounded bg-light">
            {{ $payment->payment_description }}
        </div>
    </div>
    @endif

    @if($payment->payment_status == 'paid')
    <div class="mt-4 text-center">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="bx bx-printer me-1"></i> {{ __('payments.print_receipt') }}
        </button>
    </div>
    @endif
</div>
