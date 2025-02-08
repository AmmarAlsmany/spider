@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <!--breadcrumb-->
    <div class="mb-4 page-breadcrumb d-flex align-items-center">
        <div class="pe-3 breadcrumb-title d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
                <i class="bx bx-arrow-back"></i> Back
            </a>
            <h4 class="mb-0 text-primary"><i class="bx bx-user-circle"></i> Client Profile</h4>
        </div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Client Details</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="container px-0">
        <div class="main-body">
            <div class="row">
                <div class="mb-4 col-lg-4">
                    <div class="shadow-sm card">
                        <div class="text-center card-body">
                            <div class="p-4 mb-3 rounded-circle bg-light d-inline-block">
                                <i class="bx bx-user-circle text-primary" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="mb-3">{{ $client->name }}</h5>
                            <p class="mb-1 text-muted">
                                <i class="bi bi-envelope me-2"></i>{{ $client->email }}
                            </p>
                            <p class="text-muted">
                                <i class="bi bi-phone me-2"></i>{{ $client->phone }}
                            </p>
                            <div class="mt-4 d-flex justify-content-center">
                                <span class="px-3 py-2 badge bg-primary-subtle text-primary">
                                    <i class="bi bi-file-text me-1"></i>
                                    {{ count($contracts) }} Contracts
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="shadow-sm card">
                        <div class="card-body">
                            <h5 class="mb-4"><i class="bi bi-person-vcard me-2"></i>Client Information</h5>
                            <form action="{{ route('sales.update.client.info', ['id' => $client->id]) }}" method="POST">
                                @method('PATCH')
                                @csrf
                                <div class="mb-4 row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ $client->name }}" required />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ $client->email }}" required />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Phone</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ $client->phone }}" required />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Mobile</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                            <input type="text" name="mobile" class="form-control"
                                                value="{{ $client->mobile }}" required />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Tax Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-file-text"></i></span>
                                            <input type="number" name="tax_number" class="form-control"
                                                value="{{ $client->tax_number }}" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Zip Code</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo"></i></span>
                                            <input type="number" name="zip_code" class="form-control"
                                                value="{{ $client->zip_code }}" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">City</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                                            <select name="city" class="form-select" required>
                                                <option value="">Select City</option>
                                                @foreach ($saudiCities as $city)
                                                <option value="{{ $city }}" {{ $client->city == $city ? 'selected' : ''
                                                    }}>
                                                    {{ $city }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted">Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <input type="text" name="address" class="form-control"
                                                value="{{ $client->address }}" required />
                                        </div>
                                    </div>
                                </div>

                                <h5 class="mb-4"><i class="bi bi-file-earmark-text me-2"></i>Contracts</h5>
                                @if ($contracts->isEmpty())
                                <div class="p-4 text-center rounded bg-light">
                                    <i class="bi bi-file-earmark-x text-muted" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0 text-muted">No contracts available.</p>
                                </div>
                                @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Contract Type</th>
                                                <th>Status</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($contracts as $contract)
                                            <tr>
                                                <td>
                                                    <i class="bi bi-file-text me-2 text-muted"></i>
                                                    {{ $contract->type->name ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    @if($contract->contract_status == 'pending')
                                                    <span
                                                        class="px-3 py-1 badge rounded-pill bg-warning-subtle text-warning">
                                                        <i class="bi bi-clock me-1"></i>Pending
                                                    </span>
                                                    @else
                                                    <span
                                                        class="px-3 py-1 badge rounded-pill bg-success-subtle text-success">
                                                        <i class="bi bi-check-circle me-1"></i>Active
                                                    </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <i class="bi bi-calendar-event me-2 text-muted"></i>
                                                    {{ $contract->contract_start_date ? date('M d, Y',
                                                    strtotime($contract->contract_start_date)) : 'Not set' }}
                                                </td>
                                                <td>
                                                    <i class="bi bi-calendar-event me-2 text-muted"></i>
                                                    {{ $contract->contract_end_date ? date('M d, Y',
                                                    strtotime($contract->contract_end_date)) : 'Not set' }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('contract.show.details', ['id' => $contract->id]) }}"
                                                        class="shadow-sm btn btn-sm btn-primary">
                                                        <i class="bi bi-eye me-1"></i> View Details
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif

                                <div class="mt-4 text-end">
                                    <button type="submit" class="shadow-sm btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .form-control,
    .input-group-text {
        padding: 0.6rem 1rem;
        font-size: 0.875rem;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #80bdff;
    }

    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
    }

    .table {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #eee;
    }

    .table thead {
        background-color: #f8f9fa;
    }

    .table thead th {
        border-bottom: none;
        font-weight: 600;
        color: #444;
    }

    .table td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }

    .badge {
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    .btn {
        padding: 0.5rem 1rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endsection