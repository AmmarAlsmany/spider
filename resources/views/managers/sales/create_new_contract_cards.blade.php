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
            <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
                <i class="bx bx-arrow-back"></i> Back
            </a>
            <h4 class="mb-0 text-primary"><i class="bx bx-file-plus"></i> Create New Contract</h4>
        </div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create new Contract</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4">
        @empty($contracts_types)
        <div class="col-12">
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>No contract types found</div>
            </div>
        </div>
        @endempty

        @foreach ($contracts_types as $type)
        <div class="col-md-6 col-lg-4">
            <div class="shadow-sm card contract-card h-100">
                <div class="position-relative">
                    <img src="{{ !empty($type->avatar) ? url('upload/ContractType/' . $type->avatar) : url('upload/contracts.jpg') }}"
                        class="card-img-top contract-img" alt="{{ $type->name }}">
                    <div class="contract-type-badge">
                        <i class="bi bi-file-earmark-text me-1"></i>{{ $type->name }}
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="mb-3 card-title fw-bold">{{ $type->name }}</h5>
                    <p class="mb-4 text-muted">{{ $type->description ?? 'Create a new contract with ' . $type->name }}
                    </p>
                    <div class="gap-2 d-flex justify-content-center">
                        @if($type->name == 'Buy equipment')
                        <a href="{{ route('equipment.contract.create') }}" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-person-plus me-2"></i>New Client
                        </a>
                        <a href="{{ route('equipment.contract.create') }}?existing=true"
                            class="btn btn-outline-primary flex-grow-1">
                            <i class="bi bi-person-check me-2"></i>Existing Client
                        </a>
                        @else
                        <button class="btn btn-primary flex-grow-1" data-bs-toggle="modal"
                            data-bs-target="#newClientModal{{ $type->id }}">
                            <i class="bi bi-person-plus me-2"></i>New Client
                        </button>
                        <button class="btn btn-outline-primary flex-grow-1" data-bs-toggle="modal"
                            data-bs-target="#existingClientModal{{ $type->id }}">
                            <i class="bi bi-person-check me-2"></i>Existing Client
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($type->name != 'Buy Equipment')
        <!-- New Client Modal -->
        <div class="modal fade" id="newClientModal{{ $type->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="text-white modal-header bg-primary">
                        <h5 class="modal-title">
                            <i class="bi bi-person-plus me-2"></i>
                            New Client - {{ $type->name }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('contract.index') }}" method="POST">
                        @csrf
                        <input type="hidden" name="contract_type_id" value="{{ $type->id }}">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="branches" class="form-label">
                                    <i class="bi bi-building me-2"></i>Number of Branches
                                </label>
                                <input type="number" class="form-control form-control-lg" id="branches" name="branches"
                                    required min="1" placeholder="Enter number of branches">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i>Close
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-arrow-right-circle me-2"></i>Continue
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Existing Client Modal -->
        <div class="modal fade" id="existingClientModal{{ $type->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="text-white modal-header bg-primary">
                        <h5 class="modal-title">
                            <i class="bi bi-person-check me-2"></i>
                            Select Existing Client - {{ $type->name }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('contract.index') }}" method="POST">
                        @csrf
                        <input type="hidden" name="contract_type_id" value="{{ $type->id }}">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="client" class="form-label">
                                    <i class="bi bi-search me-2"></i>Select Client
                                </label>
                                <select class="form-select form-select-lg" id="client" name="client_id" required>
                                    <option value="">Choose a client...</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}
                                        <small class="text-muted">({{ $client->email }})</small>
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="existing_branches" class="form-label">
                                    <i class="bi bi-building me-2"></i>Number of Branches
                                </label>
                                <input type="number" class="form-control form-control-lg" id="existing_branches"
                                    name="branches" required min="1" placeholder="Enter number of branches">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i>Close
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-arrow-right-circle me-2"></i>Continue
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>

<style>
    .contract-card {
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .contract-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .contract-img {
        height: 200px;
        object-fit: cover;
    }

    .contract-type-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255, 255, 255, 0.9);
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card-title {
        color: #2b2b2b;
        font-size: 1.25rem;
    }

    .btn {
        padding: 0.6rem 1rem;
        font-weight: 500;
    }

    .btn-primary {
        background: #435ebe;
        border-color: #435ebe;
    }

    .btn-primary:hover {
        background: #364b96;
        border-color: #364b96;
    }

    .btn-outline-primary {
        color: #435ebe;
        border-color: #435ebe;
    }

    .btn-outline-primary:hover {
        background: #435ebe;
        border-color: #435ebe;
    }

    .modal-content {
        border: none;
        border-radius: 0.5rem;
    }

    .modal-header {
        border-radius: 0.5rem 0.5rem 0 0;
    }
</style>
@endsection