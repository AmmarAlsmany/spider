@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Contract Details</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contract Number</label>
                        <p>{{ $contract->contract_number }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Customer</label>
                        <p>{{ $contract->customer ? $contract->customer->name : 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Start Date</label>
                        <p>{{ $contract->contract_start_date }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <p>
                            <span class="badge {{ 
                                $contract->contract_status == 'active' ? 'bg-success' : 
                                ($contract->contract_status == 'expired' ? 'bg-danger' : 
                                ($contract->contract_status == 'pending' ? 'bg-warning' : 'bg-info')) 
                            }}">
                                {{ ucfirst($contract->contract_status) }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contract Type</label>
                        <p>{{ $contract->type->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contract Price</label>
                        <p>${{ number_format($contract->contract_price, 2) }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Type</label>
                        <p>{{ $contract->Payment_type }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Number of Payments</label>
                        <p>{{ $contract->number_Payments ? $contract->number_Payments : '1' }}</p>
                    </div>
                </div>
            </div>

            @if($contract->contract_description)
            <div class="mt-3 row">
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <p>{{ $contract->contract_description }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
