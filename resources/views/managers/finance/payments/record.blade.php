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
                    <h6 class="mb-0">Record Payment</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('finance.payments') }}" class="btn btn-secondary">Back to Payments</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border shadow-none radius-10">
                        <div class="card-body">
                            <h5 class="card-title">Invoice Details</h5>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Invoice Number:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $payment->invoice_number }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Contract:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $payment->contract->contract_number }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Amount Due:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ number_format($payment->payment_amount, 2) }} SAR</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Due Date:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $payment->due_date }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Status:</label>
                                <div class="col-sm-8">
                                    <span class="badge bg-{{ $payment->payment_status == 'paid' ? 'success' : ($payment->payment_status == 'overdue' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($payment->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border shadow-none radius-10">
                        <div class="card-body">
                            <h5 class="card-title">Customer Information</h5>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Name:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $payment->customer->name }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Email:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $payment->customer->email }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Phone:</label>
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
                        <h5 class="card-title">Payment Information</h5>
                        
                        <div class="mb-3 row">
                            <label for="payment_amount" class="col-sm-3 col-form-label">Payment Amount</label>
                            <div class="col-sm-9">
                                <input type="number" step="0.01" class="form-control @error('payment_amount') is-invalid @enderror" 
                                    id="payment_amount" name="payment_amount" value="{{ old('payment_amount', $payment->payment_amount) }}" required>
                                @error('payment_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="payment_date" class="col-sm-3 col-form-label">Payment Date</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                    id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="payment_method" class="col-sm-3 col-form-label">Payment Method</label>
                            <div class="col-sm-9">
                                <select class="form-select @error('payment_method') is-invalid @enderror" 
                                    id="payment_method" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                    <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="payment_reference" class="col-sm-3 col-form-label">Reference Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('payment_reference') is-invalid @enderror" 
                                    id="payment_reference" name="payment_reference" value="{{ old('payment_reference') }}" 
                                    placeholder="Transaction ID, Check Number, etc." required>
                                @error('payment_reference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="payment_receipt" class="col-sm-3 col-form-label">Receipt Upload</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control @error('payment_receipt') is-invalid @enderror" 
                                    id="payment_receipt" name="payment_receipt">
                                <small class="form-text text-muted">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                @error('payment_receipt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="payment_notes" class="col-sm-3 col-form-label">Notes</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="payment_notes" name="payment_notes" rows="3">{{ old('payment_notes') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">Record Payment</button>
                                <a href="{{ route('finance.payments') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
