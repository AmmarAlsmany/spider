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
    <!--breadcrumb-->
    <div class="mb-4 page-breadcrumb d-flex align-items-center">
        <div class="pe-3 breadcrumb-title d-flex align-items-center">
            <a href="{{ route('equipment-types.index') }}" class="btn btn-secondary me-3">
                <i class="bx bx-arrow-back"></i> {{ __('equipment_views.back') }}
            </a>
            <h4 class="mb-0 text-primary"><i class="bx bx-edit"></i> {{ __('equipment_views.edit_equipment_type') }}</h4>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-body">
            <form action="{{ route('equipment-types.update', $equipmentType->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label for="name" class="form-label">{{ __('equipment_views.name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $equipmentType->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">{{ __('equipment_views.description') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $equipmentType->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>  

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" 
                                   id="is_active" name="is_active" 
                                   {{ old('is_active', $equipmentType->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('equipment_views.active') }}</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="px-4 btn btn-primary">
                            <i class="bx bx-save"></i> {{ __('equipment_views.update_equipment_type') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
