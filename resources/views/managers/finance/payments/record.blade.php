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
                    <h6 class="mb-0">{{ __('finance_views.record_payment') }}</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('finance.payments') }}" class="btn btn-secondary">{{ __('finance_views.back_to_payments') }}</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border shadow-none radius-10">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('finance_views.invoice_details') }}</h5>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">{{ __('finance_views.invoice_number_label') }}</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $payment->invoice_number }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">{{ __('finance_views.contract_label') }}</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $payment->contract->contract_number }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">{{ __('finance_views.amount_due') }}</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ number_format($payment->payment_amount, 2) }} {{ __('finance_views.currency_sar') }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">{{ __('finance_views.due_date') }}:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $payment->due_date }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">{{ __('finance_views.status') }}:</label>
                                <div class="col-sm-8">
                                    <span class="badge bg-{{ $payment->payment_status == 'paid' ? 'success' : ($payment->payment_status == 'overdue' ? 'danger' : 'warning') }}">
                                        {{ __('finance_views.' . $payment->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border shadow-none radius-10">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('finance_views.customer_information') }}</h5>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">{{ __('finance_views.name') }}</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $payment->customer->name }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">{{ __('finance_views.email') }}</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $payment->customer->email }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">{{ __('finance_views.phone') }}</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $payment->customer->phone }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('finance.payments.record', $payment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card border shadow-none radius-10">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('finance_views.payment_information') }}</h5>
                        
                        <div class="mb-3 row">
                            <label for="payment_amount" class="col-sm-3 col-form-label">{{ __('finance_views.payment_amount') }}</label>
                            <div class="col-sm-9">
                                <input type="number" step="0.01" class="form-control @error('payment_amount') is-invalid @enderror" 
                                    id="payment_amount" name="payment_amount" value="{{ old('payment_amount', $payment->payment_amount) }}" required>
                                @error('payment_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="payment_date" class="col-sm-3 col-form-label">{{ __('finance_views.payment_date') }}</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                    id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="payment_method" class="col-sm-3 col-form-label">{{ __('finance_views.payment_method') }}</label>
                            <div class="col-sm-9">
                                <select class="form-select @error('payment_method') is-invalid @enderror" 
                                    id="payment_method" name="payment_method" required>
                                    <option value="">{{ __('finance_views.select_payment_method') }}</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>{{ __('finance_views.bank_transfer') }}</option>
                                    <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>{{ __('finance_views.credit_card') }}</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>{{ __('finance_views.cash') }}</option>
                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>{{ __('finance_views.check') }}</option>
                                    <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>{{ __('finance_views.other') }}</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="payment_reference" class="col-sm-3 col-form-label">{{ __('finance_views.reference_number') }}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('payment_reference') is-invalid @enderror" 
                                    id="payment_reference" name="payment_reference" value="{{ old('payment_reference') }}" 
                                    placeholder="{{ __('finance_views.reference_placeholder') }}" required>
                                @error('payment_reference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="payment_receipt" class="col-sm-3 col-form-label">{{ __('finance_views.receipt_upload') }}</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control @error('payment_receipt') is-invalid @enderror" 
                                    id="payment_receipt" name="payment_receipt">
                                <small class="form-text text-muted">{{ __('finance_views.receipt_formats') }}</small>
                                @error('payment_receipt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="payment_notes" class="col-sm-3 col-form-label">{{ __('finance_views.notes') }}</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="payment_notes" name="payment_notes" rows="3">{{ old('payment_notes') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">{{ __('finance_views.record_payment') }}</button>
                                <a href="{{ route('finance.payments') }}" class="btn btn-outline-secondary">{{ __('finance_views.cancel') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
