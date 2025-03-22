@extends('shared.dashboard')
@section('content')
<div class="container-fluid">
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add New Pesticide</h5>
                        <a href="{{ route('technical.pesticides.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>

                    <div class="card-body">
                        @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('technical.pesticides.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                    id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-control @error('category') is-invalid @enderror" id="category" name="category">
                                    <option value="">-- Select Category --</option>
                                    <option value="insecticide" {{ old('category') == 'insecticide' ? 'selected' : '' }}>Insecticide</option>
                                    <option value="rodenticide" {{ old('category') == 'rodenticide' ? 'selected' : '' }}>Rodenticide</option>
                                    <option value="fungicide" {{ old('category') == 'fungicide' ? 'selected' : '' }}>Fungicide</option>
                                    <option value="herbicide" {{ old('category') == 'herbicide' ? 'selected' : '' }}>Herbicide</option>
                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="current_stock" class="form-label">Current Stock</label>
                                        <input type="number" class="form-control @error('current_stock') is-invalid @enderror" 
                                            id="current_stock" name="current_stock" value="{{ old('current_stock', 0) }}" min="0">
                                        @error('current_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="min_stock_threshold" class="form-label">Minimum Stock Threshold</label>
                                        <input type="number" class="form-control @error('min_stock_threshold') is-invalid @enderror" 
                                            id="min_stock_threshold" name="min_stock_threshold" value="{{ old('min_stock_threshold', 10) }}" min="0">
                                        @error('min_stock_threshold')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{
                                    old('active') ? 'checked' : 'checked' }}>
                                <label class="form-check-label" for="active">Active</label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Pesticide
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection