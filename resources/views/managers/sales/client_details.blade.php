@extends('shared.dashboard')
@push('style')
<style>
    /* Card Styling */
    .card {
        border: none;
        border-radius: 16px;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        background: linear-gradient(145deg, #ffffff, #f8f9fa);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        background-color: transparent;
    }

    /* Client Profile Section */
    .client-avatar {
        width: 120px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 1.5rem;
        position: relative;
        background: linear-gradient(145deg, #f0f7ff, #e6f0ff);
        box-shadow: 0 8px 16px rgba(0, 123, 255, 0.1);
    }

    .client-avatar i {
        font-size: 4.5rem;
        background: linear-gradient(45deg, #0d6efd, #4da3ff);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Form Controls */
    .form-control,
    .input-group-text,
    .form-select {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
        border-color: #80bdff;
    }

    .input-group-text {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        background: linear-gradient(145deg, #f0f7ff, #e6f0ff);
        color: #0d6efd;
        border: 1px solid #deecff;
        font-size: 1rem;
    }

    .form-control,
    .form-select {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border: 1px solid #deecff;
    }

    /* Labels */
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        color: #495057;
    }

    /* Table Styling */
    .table {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border: none;
        margin-bottom: 0;
    }

    .table thead {
        background: linear-gradient(145deg, #f0f7ff, #e6f0ff);
    }

    .table thead th {
        border-bottom: none;
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        vertical-align: middle;
        padding: 1.2rem 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    /* Badges */
    .badge {
        font-weight: 500;
        letter-spacing: 0.5px;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        font-size: 0.8rem;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
    }

    /* Buttons */
    .btn {
        padding: 0.6rem 1.2rem;
        font-weight: 500;
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        letter-spacing: 0.3px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .btn-primary {
        background: linear-gradient(45deg, #0d6efd, #4da3ff);
        border: none;
    }

    .btn-secondary {
        background: linear-gradient(45deg, #6c757d, #adb5bd);
        border: none;
    }

    .btn::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 5px;
        height: 5px;
        background: rgba(255, 255, 255, 0.5);
        opacity: 0;
        border-radius: 100%;
        transform: scale(1, 1) translate(-50%);
        transform-origin: 50% 50%;
    }

    .btn:focus::after {
        animation: ripple 1s ease-out;
    }

    @keyframes ripple {
        0% {
            transform: scale(0, 0);
            opacity: 0.5;
        }
        100% {
            transform: scale(20, 20);
            opacity: 0;
        }
    }

    /* Empty State */
    .empty-state {
        padding: 3rem;
        text-align: center;
        border-radius: 16px;
        background: linear-gradient(145deg, #f8f9fa, #ffffff);
        box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        background: linear-gradient(45deg, #6c757d, #adb5bd);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Section Titles */
    .section-title {
        position: relative;
        padding-bottom: 0.8rem;
        margin-bottom: 1.5rem;
        font-weight: 600;
        color: #212529;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(45deg, #0d6efd, #4da3ff);
        border-radius: 3px;
    }

    .section-title i {
        margin-right: 0.5rem;
        color: #0d6efd;
    }

    /* Page Breadcrumb */
    .page-breadcrumb {
        padding: 1rem 0;
        margin-bottom: 2rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
</style>
@endpush
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
    <div class="page-breadcrumb d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
                <i class="bx bx-arrow-back"></i> {{ __('sales_views.back') }}
            </a>
            <div>
                <h4 class="mb-1 fw-bold">{{ $client->name }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('sales.dashboard') }}" class="text-decoration-none"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('sales_views.client_details') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="container px-0">
        <div class="main-body mt-4">
            <div class="row g-4">
                <div class="mb-4 col-lg-4 fade-in" style="animation-delay: 0.1s;">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0 fw-bold"><i class="bx bx-user-circle text-primary"></i> {{ __('sales_views.client_profile') }}</h4>
                        </div>
                        <div class="text-center card-body p-4">
                            <div class="client-avatar">
                                <i class="bx bx-user-circle"></i>
                            </div>
                            <h4 class="mb-3 fw-bold">{{ $client->name }}</h4>
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <span class="d-flex align-items-center justify-content-center bg-primary-subtle rounded-circle" style="width: 32px; height: 32px;">
                                    <i class="bi bi-envelope text-primary"></i>
                                </span>
                                <span class="ms-2">{{ $client->email }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <span class="d-flex align-items-center justify-content-center bg-primary-subtle rounded-circle" style="width: 32px; height: 32px;">
                                    <i class="bi bi-phone text-primary"></i>
                                </span>
                                <span class="ms-2">{{ $client->phone }}</span>
                            </div>
                            <div class="mt-4">
                                <span class="badge bg-primary-subtle text-primary">
                                    <i class="bi bi-file-text me-1"></i>
                                    {{ count($contracts) }} {{ __('sales_views.contracts') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 fade-in" style="animation-delay: 0.2s;">
                    <div class="card">
                        <div class="card-body p-4">
                            <h5 class="section-title"><i class="bi bi-person-vcard"></i>{{ __('sales_views.client_information') }}</h5>
                            <form action="{{ route('sales.update.client.info', ['id' => $client->id]) }}"
                                method="POST">
                                @method('PATCH')
                                @csrf
                                <div class="row g-4 mb-4">
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">{{ __('sales_views.full_name') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ $client->name }}" required />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">{{ __('sales_views.email') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ $client->email }}" required />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">{{ __('sales_views.phone') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ $client->phone }}" required />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">{{ __('sales_views.mobile') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                            <input type="text" name="mobile" class="form-control"
                                                value="{{ $client->mobile }}" required />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">{{ __('sales_views.tax_number') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-file-text"></i></span>
                                            <input type="number" name="tax_number" class="form-control"
                                                value="{{ $client->tax_number }}" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">{{ __('sales_views.zip_code') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo"></i></span>
                                            <input type="number" name="zip_code" class="form-control"
                                                value="{{ $client->zip_code }}" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">{{ __('sales_views.city') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                                            <select name="city" class="form-select" required>
                                                <option value="">{{ __('sales_views.select_city') }}</option>
                                                @foreach ($saudiCities as $city)
                                                <option value="{{ $city }}" {{ $client->city == $city ? 'selected' : '' }}>
                                                    {{ $city }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted">{{ __('sales_views.address') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <input type="text" name="address" class="form-control"
                                                value="{{ $client->address }}" required />
                                        </div>
                                    </div>
                                </div>

                                <h5 class="section-title mt-5"><i class="bi bi-file-earmark-text"></i>{{ __('sales_views.contracts') }}</h5>
                                @if ($contracts->isEmpty())
                                <div class="empty-state">
                                    <i class="bi bi-file-earmark-x"></i>
                                    <p class="mb-0 text-muted">{{ __('sales_views.no_contracts_available') }}</p>
                                </div>
                                @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>{{ __('sales_views.contract_type') }}</th>
                                                <th>{{ __('sales_views.status') }}</th>
                                                <th>{{ __('sales_views.start_date') }}</th>
                                                <th>{{ __('sales_views.end_date') }}</th>
                                                <th>{{ __('sales_views.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($contracts as $contract)
                                            <tr>
                                                <td>
                                                    <i class="bi bi-file-text me-2 text-muted"></i>
                                                    {{ $contract->type->name ?? __('sales_views.not_available') }}
                                                </td>
                                                <td>
                                                    @if($contract->contract_status == 'pending')
                                                    <span class="badge bg-warning-subtle text-warning">
                                                        <i class="bi bi-clock me-1"></i>{{ __('sales_views.pending') }}
                                                    </span>
                                                    @else
                                                    <span class="badge bg-success-subtle text-success">
                                                        <i class="bi bi-check-circle me-1"></i>{{ __('sales_views.active') }}
                                                    </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <i class="bi bi-calendar-event me-2 text-muted"></i>
                                                    {{ $contract->contract_start_date ? date('M d, Y', strtotime($contract->contract_start_date)) : __('sales_views.not_set') }}
                                                </td>
                                                <td>
                                                    <i class="bi bi-calendar-event me-2 text-muted"></i>
                                                    {{ $contract->contract_end_date ? date('M d, Y', strtotime($contract->contract_end_date)) : __('sales_views.not_set') }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('contract.show.details', ['id' => $contract->id]) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="bi bi-eye me-1"></i> {{ __('sales_views.view_details') }}
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif

                                <div class="mt-5 text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>{{ __('sales_views.save_changes') }}
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