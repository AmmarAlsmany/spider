@extends('shared.dashboard')
@section('content')
    <div class="container">
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
            <div class="mb-4 page-breadcrumb d-sm-flex align-items-center">
                <div class="breadcrumb-title pe-3 fw-bold text-primary">Contract Management</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="p-0 mb-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none"><i
                                        class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/contracts') }}"
                                    class="text-decoration-none">Contracts</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create Annex</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="shadow-sm card">
                        <div class="py-3 bg-white card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-primary"><i
                                        class="bx bx-file-plus me-2"></i>{{ __('contract_views.contract_annex') }}</h5>
                                <a href="{{ route('contract.show.details', $contract->id) }}"
                                    class="btn btn-outline-secondary btn-sm">
                                    <i class="bx bx-arrow-back me-1"></i> {{ __('contract_views.back') }}
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-4 alert alert-info">
                                <i class="bx bx-info-circle me-1"></i>
                                Adding new branch and payment details to contract
                                <strong>#{{ $contract->contract_number }}</strong>
                            </div>

                            <form action="{{ route('contracts.annex.store', $contract->id) }}" method="post"
                                id="annexForm">
                                @csrf
                                <div class="row g-4">
                                    <!-- Branch Information -->
                                    <div class="col-12" id="branchesContainer">
                                        <div class="mb-4 branch-section" data-branch-index="0">
                                            <div class="col-md-12">
                                                <div class="border-0 shadow-sm card h-100">
                                                    <div
                                                        class="text-white card-header bg-primary bg-gradient d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0"><i class="bx bx-building me-2"></i>Branch
                                                            Information</h6>
                                                        <button type="button"
                                                            class="btn btn-outline-light btn-sm remove-branch">
                                                            <i class="bx bx-trash me-1"></i>Remove Branch
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="mb-3 col-md-6">
                                                                <label class="form-label">Branch Name <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text"
                                                                    class="form-control @error('branches.0.branch_name') is-invalid @enderror"
                                                                    name="branches[0][branch_name]"
                                                                    placeholder="Enter Branch Name" required
                                                                    value="{{ old('branches.0.branch_name') }}">
                                                                @error('branches.0.branch_name')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="mb-3 col-md-6">
                                                                <label class="form-label">Branch City <span
                                                                        class="text-danger">*</span></label>
                                                                <select
                                                                    class="form-select @error('branches.0.branch_city') is-invalid @enderror"
                                                                    name="branches[0][branch_city]" required>
                                                                    <option value="">Select City</option>
                                                                    @foreach ($saudiCities as $city)
                                                                        <option value="{{ $city }}"
                                                                            {{ old('branches.0.branch_city') == $city ? 'selected' : '' }}>
                                                                            {{ $city }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('branches.0.branch_city')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="mb-3 col-md-6">
                                                                <label class="form-label">Branch Manager Name <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text"
                                                                    class="form-control @error('branches.0.branch_manager_name') is-invalid @enderror"
                                                                    name="branches[0][branch_manager_name]"
                                                                    placeholder="Enter Branch Manager Name" required
                                                                    value="{{ old('branches.0.branch_manager_name') }}">
                                                                @error('branches.0.branch_manager_name')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="mb-3 col-md-6">
                                                                <label class="form-label">Branch Manager Phone <span
                                                                        class="text-danger">*</span></label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text bg-light">+966</span>
                                                                    <input type="text"
                                                                        class="form-control @error('branches.0.branch_manager_phone') is-invalid @enderror"
                                                                        name="branches[0][branch_manager_phone]"
                                                                        placeholder="5XXXXXXXX" required pattern="[0-9]{9}"
                                                                        value="{{ old('branches.0.branch_manager_phone') }}">
                                                                    @error('branches.0.branch_manager_phone')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                                <small class="text-muted">Enter 9 digits without the country
                                                                    code</small>
                                                            </div>
                                                            
                                                            <div class="mb-3 col-md-6">
                                                                <label class="form-label">Number of Visits <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="number"
                                                                    class="form-control @error('branches.0.number_of_visits') is-invalid @enderror"
                                                                    name="branches[0][number_of_visits]"
                                                                    placeholder="Enter Number of Visits" required min="1"
                                                                    value="{{ old('branches.0.number_of_visits') }}">
                                                                @error('branches.0.number_of_visits')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="mb-3 col-12">
                                                                <label class="form-label">Branch Address <span
                                                                        class="text-danger">*</span></label>
                                                                <textarea class="form-control @error('branches.0.branch_address') is-invalid @enderror"
                                                                    name="branches[0][branch_address]" placeholder="Enter complete branch address" required rows="2">{{ old('branches.0.branch_address') }}</textarea>
                                                                @error('branches.0.branch_address')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Add Branch Button -->
                                    <div class="mb-4 text-center col-12">
                                        <button type="button" class="px-4 btn btn-success btn-lg" id="addBranch">
                                            <i class="bx bx-plus-circle me-2"></i>Add Another Branch
                                        </button>
                                    </div>

                                    <!-- Payment Information -->
                                    <div class="col-md-12">
                                        <div class="border-0 shadow-sm card">
                                            <div class="text-white card-header bg-primary bg-gradient">
                                                <h6 class="mb-0"><i class="bx bx-money me-2"></i>Payment Information
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Additional Amount with out VAT <span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="number"
                                                            class="form-control @error('additional_amount') is-invalid @enderror"
                                                            name="additional_amount" placeholder="0.00" step="0.01"
                                                            min="0" required
                                                            value="{{ old('additional_amount') }}">
                                                        <span class="input-group-text">SAR</span>
                                                    </div>
                                                    @error('additional_amount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Due Date <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date"
                                                        class="form-control @error('due_date') is-invalid @enderror"
                                                        name="due_date" required min="{{ date('Y-m-d') }}"
                                                        value="{{ old('due_date', date('Y-m-d')) }}">
                                                    @error('due_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea class="form-control @error('description') is-invalid @enderror" name="description"
                                                        placeholder="Enter any additional notes or description" rows="4">{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-end">
                                    <a href="{{ route('contract.show.details', $contract->id) }}"
                                        class="btn btn-light me-2">
                                        <i class="bx bx-x me-1"></i> {{ __('contract_views.cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-check me-1"></i> {{ __('contract_views.submit') }}
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
                let branchIndex = 0;

                addBranchBtn.addEventListener('click', function() {
                    branchIndex++;
                    const newBranch = document.querySelector('.branch-section').cloneNode(true);
                    newBranch.dataset.branchIndex = branchIndex;

                    // Update all input names, IDs, and clear values
                    newBranch.querySelectorAll('input, select, textarea').forEach(input => {
                        const oldName = input.name;
                        input.name = oldName.replace('[0]', `[${branchIndex}]`);
                        input.value = '';

                        // Clear select element
                        if (input.tagName === 'SELECT') {
                            input.selectedIndex = 0;
                        }
                    });

                    // Show remove button for all branches except the first one
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
                });

                // Show/hide remove button for the first branch
                const firstBranchRemoveBtn = document.querySelector(
                    '.branch-section[data-branch-index="0"] .remove-branch');
                if (firstBranchRemoveBtn) {
                    firstBranchRemoveBtn.style.display = branchIndex > 0 ? 'block' : 'none';
                }
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

            .form-control:focus,
            .form-select:focus {
                border-color: #86b7fe;
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }

            .btn-success {
                transition: all 0.3s ease;
            }

            .btn-success:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }
        </style>
    @endpush
@endsection
