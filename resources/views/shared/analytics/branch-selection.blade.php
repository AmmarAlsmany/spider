@extends('shared.dashboard')

@section('title', 'Branch Selection for Analytics')

@push('styles')
<style>
    .branch-card {
        transition: all 0.3s;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .branch-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .branch-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: #3461ff;
        color: white;
        margin-right: 15px;
    }
</style>
@endpush

@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Select Branch for Insect Analytics</h5>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary">
                        <i class="bx bx-arrow-back me-1"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Contract #:</strong> {{ $contract->id }}</p>
                            <p><strong>Customer:</strong> {{ $contract->customer->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Contract Type:</strong> {{ $contract->type->name ?? 'N/A' }}</p>
                            <p><strong>Status:</strong> <span class="badge bg-{{ $contract->contract_status == 'approved' ? 'success' : 'danger' }}">{{ ucfirst($contract->contract_status) }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Available Branches</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($contract->branchs as $branch)
                            <div class="col-12 col-md-6 col-lg-4 mb-4">
                                <div class="card branch-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="branch-icon">
                                                <i class="bx bx-building fs-5"></i>
                                            </div>
                                            <h5 class="mb-0">{{ $branch->branch_name }}</h5>
                                        </div>
                                        <p class="mb-2"><strong>Manager:</strong> {{ $branch->branch_manager_name }}</p>
                                        <p class="mb-2"><strong>Phone:</strong> {{ $branch->branch_manager_phone }}</p>
                                        <p class="mb-2"><strong>Address:</strong> {{ $branch->branch_address }}</p>
                                        <p class="mb-0"><strong>City:</strong> {{ $branch->branch_city }}</p>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0">
                                        <a href="{{ route('analytics.branch', ['contractId' => $contract->id, 'branchId' => $branch->id]) }}" 
                                           class="btn btn-primary w-100">
                                            <i class="bx bx-bar-chart-alt-2 me-1"></i> View Analytics
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    No branches found for this contract.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
