@extends('shared.dashboard')

@section('title', 'Edit Target Insect')

@section('content')
<div class="page-content">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Target Insect</h5>
            <a href="{{ route('target-insects.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('target-insects.update', $targetInsect->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $targetInsect->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">This is the display name that will appear in forms and reports</small>
                </div>
                
                <div class="mb-3">
                    <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value', $targetInsect->value) }}" required>
                    @error('value')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">This is the value used in the form (e.g., "cockroaches", "flying_insects")</small>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $targetInsect->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="active" name="active" {{ $targetInsect->active ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Active</label>
                    <small class="d-block text-muted">Inactive target insects won't appear in forms</small>
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Update Target Insect
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
