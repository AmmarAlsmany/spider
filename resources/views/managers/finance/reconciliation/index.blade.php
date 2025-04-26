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
                    <h6 class="mb-0">{{ __('finance_views.payment_reconciliation') }}</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('finance.payments') }}" class="btn btn-secondary">{{ __('finance_views.back_to_payments') }}</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('finance.reconciliation.mark') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="50px">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th>{{ __('finance_views.invoice_number') }}</th>
                                <th>{{ __('finance_views.contract') }}</th>
                                <th>{{ __('finance_views.customer') }}</th>
                                <th>{{ __('finance_views.amount') }}</th>
                                <th>{{ __('finance_views.payment_date') }}</th>
                                <th>{{ __('finance_views.payment_method') }}</th>
                                <th>{{ __('finance_views.reference_number') }}</th>
                                <th>{{ __('finance_views.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($unreconciled->count() > 0)
                                @foreach($unreconciled as $payment)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input payment-checkbox" type="checkbox" 
                                                name="payment_ids[]" value="{{ $payment->id }}">
                                        </div>
                                    </td>
                                    <td>{{ $payment->invoice_number }}</td>
                                    <td>{{ $payment->contract->contract_number }}</td>
                                    <td>{{ $payment->customer->name }}</td>
                                    <td>{{ number_format($payment->payment_amount, 2) }} {{ __('finance_views.currency_sar') }}</td>
                                    <td>{{ $payment->paid_at }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? __('finance_views.na'))) }}</td>
                                    <td>{{ $payment->payment_reference ?? __('finance_views.na') }}</td>
                                    <td>
                                        <a href="{{ route('finance.payments.show', $payment->id) }}" class="btn btn-primary btn-sm">
                                            <i class="bx bx-show"></i> {{ __('finance_views.view') }}
                                        </a>
                                        @if($payment->receipt_path)
                                        <a href="{{ asset($payment->receipt_path) }}" target="_blank" class="btn btn-info btn-sm">
                                            <i class="bx bx-file"></i> {{ __('finance_views.receipt') }}
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center">{{ __('finance_views.no_unreconciled_payments') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                @if($unreconciled->count() > 0)
                <div class="mt-3 d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-success" id="reconcileBtn">
                            <i class="bx bx-check-double"></i> {{ __('finance_views.mark_as_reconciled') }}
                        </button>
                    </div>
                    <div>
                        {{ $unreconciled->links('vendor.pagination.custom') }}
                    </div>
                </div>
                @endif
            </form>
            
            <div class="mt-4">
                <div class="border shadow-none card radius-10">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('finance_views.reconciliation_guide') }}</h5>
                        <p>{{ __('finance_views.reconciliation_steps_intro') }}</p>
                        <ol>
                            <li>{{ __('finance_views.reconciliation_step_1') }}</li>
                            <li>{{ __('finance_views.reconciliation_step_2') }}</li>
                            <li>{{ __('finance_views.reconciliation_step_3') }}</li>
                            <li>{{ __('finance_views.reconciliation_step_4') }}</li>
                            <li>{{ __('finance_views.reconciliation_step_5') }}</li>
                        </ol>
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-1"></i>
                            {{ __('finance_views.reconciliation_note') }}
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
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const paymentCheckboxes = document.querySelectorAll('.payment-checkbox');
        const reconcileBtn = document.getElementById('reconcileBtn');
        
        // Handle "Select All" checkbox
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            
            paymentCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            
            updateReconcileButtonState();
        });
        
        // Handle individual checkboxes
        paymentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateReconcileButtonState();
                
                // Update "Select All" checkbox state
                const allChecked = Array.from(paymentCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            });
        });
        
        // Update reconcile button state based on selections
        function updateReconcileButtonState() {
            const anyChecked = Array.from(paymentCheckboxes).some(cb => cb.checked);
            reconcileBtn.disabled = !anyChecked;
        }
    });
</script>
@endpush
