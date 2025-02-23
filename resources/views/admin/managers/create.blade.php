@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-0 text-gray-800">Create Manager</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i> Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.managers.index') }}">Managers</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create Manager</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.managers.store') }}">
                @csrf
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Name</label>
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
                            <label for="email" class="form-label">Email</label>
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
                            <label for="password" class="form-label">Password</label>
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
                            <label for="role" class="form-label">Role</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-user-circle"></i></span>
                                <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">Select Role</option>
                                    <option value="technical" {{ old('role') == 'technical' ? 'selected' : '' }}>Technical Manager</option>
                                    <option value="finance" {{ old('role') == 'finance' ? 'selected' : '' }}>Finance Manager</option>
                                    <option value="sales_manager" {{ old('role') == 'sales_manager' ? 'selected' : '' }}>Sales Manager</option>
                                </select>
                            </div>
                            @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone</label>
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
                            <label for="address" class="form-label">Address</label>
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

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.managers.index') }}" class="btn btn-light">
                        <i class="bx bx-arrow-back me-1"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-user-plus me-1"></i> Create Manager
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection