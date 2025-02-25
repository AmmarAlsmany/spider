@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <!--breadcrumb-->
    <div class="mb-4 page-breadcrumb d-flex align-items-center">
        <div class="pe-3 breadcrumb-title d-flex align-items-center">
            <a href="{{ route('equipment-types.index') }}" class="btn btn-secondary me-3">
                <i class="bx bx-arrow-back"></i> Back
            </a>
            <h4 class="mb-0 text-primary"><i class="bx bx-plus"></i> Add New Equipment Type</h4>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-body">
            <form action="{{ route('equipment-types.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <button type="submit" class="px-4 btn btn-primary">
                            <i class="bx bx-save"></i> Save Equipment Type
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
