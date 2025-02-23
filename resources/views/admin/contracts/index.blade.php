@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0 text-gray-800">Contracts Overview</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="mb-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i> Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Contracts</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="mb-4 row g-3">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="border-0 shadow-sm card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="rounded avatar avatar-lg bg-primary-subtle">
                                    <i class="bx bx-file fs-3 text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-1">Total Contracts</h6>
                                    <h4 class="mb-0">{{ $totalContracts }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="border-0 shadow-sm card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="rounded avatar avatar-lg bg-success-subtle">
                                    <i class="bx bx-check-circle fs-3 text-success"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-1">Active Contracts</h6>
                                    <h4 class="mb-0">{{ $activeContracts }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="border-0 shadow-sm card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="rounded avatar avatar-lg bg-warning-subtle">
                                    <i class="bx bx-time fs-3 text-warning"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-1">Expiring Soon</h6>
                                    <h4 class="mb-0">{{ $expiringSoonContracts }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="border-0 shadow-sm card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="rounded avatar avatar-lg bg-danger-subtle">
                                    <i class="bx bx-x-circle fs-3 text-danger"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-1">Expired Contracts</h6>
                                    <h4 class="mb-0">{{ $expiredContracts }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-4 row g-3">
                <div class="col-12 col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="approved">Approved</option>
                        <option value="Not approved">Not Approved</option>
                        <option value="completed">Completed</option>
                        <option value="stopped">Stopped</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <select class="form-select" id="typeFilter">
                        <option value="">All Types</option>
                        <option value="Residential">Residential</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Industrial">Industrial</option>
                        <option value="Government">Government</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search contracts...">
                </div>
                <div class="col-12 col-md-3">
                    <div class="gap-2 d-flex">
                        <button class="btn btn-primary flex-grow-1" id="filterButton">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        <button class="btn btn-light" id="resetButton">
                            <i class="bx bx-reset me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Contracts Table -->
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Contract #</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                        <tr>
                            <td>
                                <span class="fw-medium">{{ $contract->contract_number }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2 bg-light rounded-circle">
                                        <span class="avatar-text">{{ substr($contract->customer->name, 0, 1) }}</span>
                                    </div>
                                    {{ $contract->customer->name }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ ucfirst($contract->type->name) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($contract->contract_start_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($contract->contract_end_date)->format('M d, Y') }}</td>
                            <td>{{ number_format($contract->contract_price, 2) }} SAR</td>
                            <td>
                                @php
                                    $statusClass = match($contract->contract_status) {
                                        'pending' => 'bg-warning-subtle text-warning',
                                        'approved' => 'bg-success-subtle text-success',
                                        'not_approved' => 'bg-danger-subtle text-danger',
                                        default => 'bg-light text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($contract->contract_status) }}
                                </span>
                            </td>
                            <td>
                                <div class="gap-2 d-flex justify-content-end">
                                    <a href="{{ route('admin.contracts.show', $contract->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Details">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-4 text-center">
                                <div class="text-muted">
                                    <i class="mb-2 bx bx-folder-open fs-3"></i>
                                    <p class="mb-0">No contracts found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $contracts->firstItem() ?? 0 }} to {{ $contracts->lastItem() ?? 0 }} of {{ $contracts->total() ?? 0 }} contracts
                </div>
                {{ $contracts->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Delete contract confirmation
    function deleteContract(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the delete form
                document.getElementById('delete-contract-' + id).submit();
            }
        });
    }

    // Filter functionality
    document.getElementById('filterButton').addEventListener('click', function() {
        applyFilters();
    });

    document.getElementById('resetButton').addEventListener('click', function() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('typeFilter').value = '';
        document.getElementById('searchInput').value = '';
        applyFilters();
    });

    function applyFilters() {
        const status = document.getElementById('statusFilter').value;
        const type = document.getElementById('typeFilter').value;
        const search = document.getElementById('searchInput').value;

        window.location.href = `{{ route('admin.contracts.index') }}?contract_status=${status}&Property_type=${type}&search=${search}`;
    }
</script>
@endpush
</section>
@endsection
