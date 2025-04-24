@extends('shared.dashboard')
@section('title', __('clients.tickets.create_ticket'))
@section('content')
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
        <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('client.tikets') }}">{{ __('clients.tickets.support_tickets') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('clients.tickets.create_ticket') }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center">
                    <a href="{{ route('client.tikets') }}" class="btn btn-secondary me-3">
                        <i class="bx bx-arrow-back"></i> {{ __('clients.actions.cancel') }}
                    </a>
                </div>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="breadcrumb-title pe-3 fw-bold text-primary">{{ __('clients.tickets.create_ticket') }}</div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('client.tikets.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ Auth::guard('client')->user()->id }}">

                            <div class="mb-3">
                                <label for="tiket_title"
                                    class="form-label">{{ __('clients.tickets.ticket_subject') }}</label>
                                <input type="text" class="form-control @error('tiket_title') is-invalid @enderror"
                                    id="tiket_title" name="tiket_title" value="{{ old('tiket_title') }}" required>
                                @error('tiket_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="tiket_description"
                                    class="form-label">{{ __('clients.tickets.ticket_description') }}</label>
                                <textarea class="form-control @error('tiket_description') is-invalid @enderror" id="tiket_description"
                                    name="tiket_description" rows="4" required>{{ old('tiket_description') }}</textarea>
                                @error('tiket_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="priority" class="form-label">{{ __('clients.tickets.priority') }}</label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority"
                                    name="priority" required>
                                    <option value="">{{ __('clients.tickets.select_priority') }}</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                        {{ __('clients.tickets.priority_levels.low') }}</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>
                                        {{ __('clients.tickets.priority_levels.medium') }}</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                        {{ __('clients.tickets.priority_levels.high') }}</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ __('clients.tickets.submit_ticket') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
