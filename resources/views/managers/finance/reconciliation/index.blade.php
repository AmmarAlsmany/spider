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
                    <h6 class="mb-0">Payment Reconciliation</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('finance.payments') }}" class="btn btn-secondary">Back to Payments</a>
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
                                <th>Invoice #</th>
                                <th>Contract</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                                <th>Payment Method</th>
                                <th>Reference</th>
                                <th>Actions</th>
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
                                    <td>{{ number_format($payment->payment_amount, 2) }} SAR</td>
                                    <td>{{ $payment->paid_at }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'N/A')) }}</td>
                                    <td>{{ $payment->payment_reference ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('finance.payments.show', $payment->id) }}" class="btn btn-primary btn-sm">
                                            <i class="bx bx-show"></i> View
                                        </a>
                                        @if($payment->receipt_path)
                                        <a href="{{ asset($payment->receipt_path) }}" target="_blank" class="btn btn-info btn-sm">
                                            <i class="bx bx-file"></i> Receipt
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center">No unreconciled payments found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                @if($unreconciled->count() > 0)
                <div class="mt-3 d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-success" id="reconcileBtn">
                            <i class="bx bx-check-double"></i> Mark Selected as Reconciled
                        </button>
                    </div>
                    <div>
                        {{ $unreconciled->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
                @endif
            </form>
            
            <div class="mt-4">
                <div class="border shadow-none card radius-10">
                    <div class="card-body">
                        <h5 class="card-title">Reconciliation Guide</h5>
                        <p>Follow these steps to reconcile payments:</p>
                        <ol>
                            <li>Verify the payment details against your bank statement or payment gateway records</li>
                            <li>Check that the payment amount matches the expected amount</li>
                            <li>Confirm the payment reference number matches your records</li>
                            <li>View the payment receipt if available</li>
                            <li>Select the payments that have been verified and click "Mark Selected as Reconciled"</li>
                        </ol>
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-1"></i>
                            Reconciled payments will be removed from this list but will still be available in the payment history.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
@endsection
