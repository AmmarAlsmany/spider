@extends('shared.dashboard')
@section('content')
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0 text-gray-800">{{ __('admin.manager_form.create_title') }}</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="mb-0 breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                            class="bx bx-home-alt"></i> {{ __('admin.sidebar.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('admin.managers.index') }}">{{ __('admin.sidebar.managers') }}</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ __('admin.manager_form.breadcrumb_create') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.managers.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">{{ __('admin.manager_form.name') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-user"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">{{ __('admin.manager_form.email') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label">{{ __('admin.manager_form.password') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role" class="form-label">{{ __('admin.manager_form.role') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-user-circle"></i></span>
                                    <select id="role" name="role"
                                        class="form-select @error('role') is-invalid @enderror" required>
                                        <option value="">{{ __('admin.manager_form.select_role') }}</option>
                                        <option value="technical" {{ old('role') == 'technical' ? 'selected' : '' }}>
                                            {{ __('admin.manager_form.role_technical') }}</option>
                                        <option value="finance" {{ old('role') == 'finance' ? 'selected' : '' }}>
                                            {{ __('admin.manager_form.role_finance') }}</option>
                                        <option value="sales_manager"
                                            {{ old('role') == 'sales_manager' ? 'selected' : '' }}>
                                            {{ __('admin.manager_form.role_sales') }}</option>
                                    </select>
                                </div>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="form-label">{{ __('admin.manager_form.phone') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone') }}" required>
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address" class="form-label">{{ __('admin.manager_form.address') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-map"></i></span>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                        id="address" name="address" value="{{ old('address') }}" required>
                                </div>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="gap-2 mt-4 d-flex justify-content-end">
                        <a href="{{ route('admin.managers.index') }}" class="btn btn-light">
                            <i class="bx bx-arrow-back me-1"></i> {{ __('admin.manager_form.back_button') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-user-plus me-1"></i> {{ __('admin.manager_form.create_button') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
