@extends('shared.dashboard')
@section('title', __('tickets.create_ticket'))
@section('content')

<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">{{ __('tickets.support') }}</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('client.tikets') }}">{{ __('tickets.my_tickets') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('tickets.create_ticket') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('client.tikets') }}" class="btn btn-secondary me-3">
                            <i class="bx bx-arrow-back"></i> {{ __('tickets.back') }}
                        </a>
                        <h4 class="mb-0">{{ __('tickets.create_new_ticket') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('client.tikets.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ Auth::guard('client')->user()->id }}">
                        
                        <div class="mb-3">
                            <label for="tiket_title" class="form-label">{{ __('tickets.title') }}</label>
                            <input type="text" class="form-control @error('tiket_title') is-invalid @enderror" 
                                id="tiket_title" name="tiket_title" value="{{ old('tiket_title') }}" required>
                            @error('tiket_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tiket_description" class="form-label">{{ __('tickets.description') }}</label>
                            <textarea class="form-control @error('tiket_description') is-invalid @enderror" 
                                id="tiket_description" name="tiket_description" rows="4" required>{{ old('tiket_description') }}</textarea>
                            @error('tiket_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="form-label">{{ __('tickets.priority') }}</label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="">{{ __('tickets.select_priority') }}</option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>{{ __('tickets.priority_levels.low') }}</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>{{ __('tickets.priority_levels.medium') }}</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>{{ __('tickets.priority_levels.high') }}</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">{{ __('tickets.create') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
