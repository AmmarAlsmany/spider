@extends('shared.dashboard')
@section('content')
<div class="container">
    <div class="page-content">
        <div class="mb-4 page-breadcrumb d-sm-flex align-items-center">
            <div class="breadcrumb-title pe-3 fw-bold text-primary">Contract Management</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none"><i
                                    class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/contracts') }}"
                                class="text-decoration-none">Contracts</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Annex</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="shadow-sm card">
                    <div class="py-3 bg-white card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-primary"><i class="bx bx-edit me-2"></i>Edit Contract Annex</h5>
                            <a href="{{ route('contract.show.details', $contract->id) }}"
                                class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-arrow-back me-1"></i> Back to Contract
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 alert alert-info">
                            <i class="bx bx-info-circle me-1"></i>
                            Editing annex <strong>#{{ $annex->annex_number }}</strong> for contract <strong>#{{ $contract->contract_number }}</strong>
                        </div>

                        <form action="{{ route('contracts.annex.update', $annex->id) }}" method="post" id="annexForm">
                            @csrf
                            @method('PUT')
                            <div class="row g-4">
                                <!-- Branch Information -->
                                <div class="col-12" id="branchesContainer">
                                    @foreach($branches->where('contracts_id', $contract->id) as $index => $branch)
                                    <div class="mb-4 branch-section" data-branch-index="{{ $index }}">
                                        <div class="col-md-12">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <div class="card-header bg-primary bg-gradient text-white d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0"><i class="bx bx-building me-2"></i>Branch Information</h6>
                                                    @if($index > 0)
                                                    <button type="button" class="btn btn-outline-light btn-sm remove-branch">
                                                        <i class="bx bx-trash me-1"></i>Remove Branch
                                                    </button>
                                                    @endif
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <input type="hidden" name="branches[{{ $index }}][id]" value="{{ $branch->id }}">
                                                        
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control @error('branches.'.$index.'.branch_name') is-invalid @enderror"
                                                                name="branches[{{ $index }}][branch_name]" placeholder="Enter Branch Name" required
                                                                value="{{ old('branches.'.$index.'.branch_name', $branch->branch_name) }}">
                                                            @error('branches.'.$index.'.branch_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Branch City <span class="text-danger">*</span></label>
                                                            <select class="form-select @error('branches.'.$index.'.branch_city') is-invalid @enderror"
                                                                name="branches[{{ $index }}][branch_city]" required>
                                                                <option value="">Select City</option>
                                                                @foreach($saudiCities as $city)
                                                                <option value="{{ $city }}" {{ old('branches.'.$index.'.branch_city', $branch->branch_city) == $city ? 'selected' : '' }}>
                                                                    {{ $city }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                            @error('branches.'.$index.'.branch_city')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Branch Manager Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control @error('branches.'.$index.'.branch_manager_name') is-invalid @enderror"
                                                                name="branches[{{ $index }}][branch_manager_name]" placeholder="Enter Branch Manager Name" required
                                                                value="{{ old('branches.'.$index.'.branch_manager_name', $branch->branch_manager_name) }}">
                                                            @error('branches.'.$index.'.branch_manager_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Branch Manager Phone <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-light">+966</span>
                                                                <input type="text" class="form-control @error('branches.'.$index.'.branch_manager_phone') is-invalid @enderror"
                                                                    name="branches[{{ $index }}][branch_manager_phone]" placeholder="5XXXXXXXX" required
                                                                    pattern="[0-9]{9}" value="{{ old('branches.'.$index.'.branch_manager_phone', substr($branch->branch_manager_phone, 3)) }}">
                                                                @error('branches.'.$index.'.branch_manager_phone')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <small class="text-muted">Enter 9 digits without the country code</small>
                                                        </div>

                                                        <div class="col-12 mb-3">
                                                            <label class="form-label">Branch Address <span class="text-danger">*</span></label>
                                                            <textarea class="form-control @error('branches.'.$index.'.branch_address') is-invalid @enderror"
                                                                name="branches[{{ $index }}][branch_address]" placeholder="Enter complete branch address" required
                                                                rows="2">{{ old('branches.'.$index.'.branch_address', $branch->branch_address) }}</textarea>
                                                            @error('branches.'.$index.'.branch_address')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Add Branch Button -->
                                <div class="mb-4 col-12 text-center">
                                    <button type="button" class="btn btn-success btn-lg px-4" id="addBranch">
                                        <i class="bx bx-plus-circle me-2"></i>Add Another Branch
                                    </button>
                                </div>

                                <!-- Payment Information -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-primary bg-gradient text-white">
                                            <h6 class="mb-0"><i class="bx bx-money me-2"></i>Payment Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Additional Amount <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">SAR</span>
                                                        <input type="number" class="form-control @error('additional_amount') is-invalid @enderror"
                                                            name="additional_amount" placeholder="Enter amount" required min="0" step="0.01"
                                                            value="{{ old('additional_amount', $annex->additional_amount) }}">
                                                        @error('additional_amount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                                        name="due_date" required min="{{ date('Y-m-d') }}"
                                                        value="{{ old('due_date', $annex->payment->due_date ?? '') }}">
                                                    @error('due_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label">Description</label>
                                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                                        name="description" rows="3"
                                                        placeholder="Enter annex description">{{ old('description', $annex->description) }}</textarea>
                                                    @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 text-end">
                                <a href="{{ route('contract.show.details', $contract->id) }}" class="btn btn-light me-2">
                                    <i class="bx bx-x me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Update Annex
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const branchesContainer = document.getElementById('branchesContainer');
    const addBranchBtn = document.getElementById('addBranch');
    let branchIndex = {{ count($branches->where('contracts_id', $contract->id)) }};

    addBranchBtn.addEventListener('click', function() {
        const newBranch = document.querySelector('.branch-section').cloneNode(true);
        newBranch.dataset.branchIndex = branchIndex;

        // Update all input names, IDs, and clear values
        newBranch.querySelectorAll('input, select, textarea').forEach(input => {
            const oldName = input.name;
            input.name = oldName.replace(/\[\d+\]/, `[${branchIndex}]`);
            input.value = '';
            
            // Remove hidden id field for new branches
            if (input.type === 'hidden' && input.name.includes('[id]')) {
                input.remove();
            }
            
            // Clear select element
            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
        });

        // Show remove button
        const removeBtn = newBranch.querySelector('.remove-branch');
        removeBtn.style.display = 'block';
        
        // Add remove button functionality
        removeBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this branch?')) {
                newBranch.remove();
            }
        });

        // Add animation class
        newBranch.classList.add('animate__animated', 'animate__fadeIn');
        
        branchesContainer.appendChild(newBranch);
        branchIndex++;
    });

    // Add remove functionality to existing branch remove buttons
    document.querySelectorAll('.remove-branch').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this branch?')) {
                this.closest('.branch-section').remove();
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
}
.branch-section {
    transition: all 0.3s ease;
}
.animate__animated {
    animation-duration: 0.5s;
}
.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
.btn-success {
    transition: all 0.3s ease;
}
.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
@endpush

@endsection
