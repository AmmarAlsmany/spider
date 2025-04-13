@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0 text-gray-800">{{ __('admin.contracts.overview') }}</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="mb-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i> {{ __('admin.sidebar.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('admin.contracts.breadcrumb') }}</li>
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
                                    <h6 class="mb-1">{{ __('admin.contracts.stats.total') }}</h6>
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
                                    <h6 class="mb-1">{{ __('admin.contracts.stats.active') }}</h6>
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
                                    <h6 class="mb-1">{{ __('admin.contracts.stats.expiring_soon') }}</h6>
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
                                    <h6 class="mb-1">{{ __('admin.contracts.stats.expired') }}</h6>
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
                        <option value="">{{ __('admin.contracts.filters.all_statuses') }}</option>
                        <option value="approved">{{ __('admin.contracts.filters.approved') }}</option>
                        <option value="Not approved">{{ __('admin.contracts.filters.not_approved') }}</option>
                        <option value="completed">{{ __('admin.contracts.filters.completed') }}</option>
                        <option value="stopped">{{ __('admin.contracts.filters.stopped') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <select class="form-select" id="typeFilter">
                        <option value="">{{ __('admin.contracts.filters.all_types') }}</option>
                        <option value="Residential">{{ __('admin.contracts.filters.residential') }}</option>
                        <option value="Commercial">{{ __('admin.contracts.filters.commercial') }}</option>
                        <option value="Industrial">{{ __('admin.contracts.filters.industrial') }}</option>
                        <option value="Government">{{ __('admin.contracts.filters.government') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="{{ __('admin.contracts.filters.search_placeholder') }}">
                </div>
                <div class="col-12 col-md-3">
                    <div class="gap-2 d-flex">
                        <button class="btn btn-primary flex-grow-1" id="filterButton">
                            <i class="bx bx-filter-alt me-1"></i> {{ __('admin.contracts.filters.filter_button') }}
                        </button>
                        <button class="btn btn-light" id="resetButton">
                            <i class="bx bx-reset me-1"></i> {{ __('admin.contracts.filters.reset_button') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Contracts Table -->
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('admin.contracts.table.contract_number') }}</th>
                            <th>{{ __('admin.contracts.table.client') }}</th>
                            <th>{{ __('admin.contracts.table.type') }}</th>
                            <th>{{ __('admin.contracts.table.start_date') }}</th>
                            <th>{{ __('admin.contracts.table.end_date') }}</th>
                            <th>{{ __('admin.contracts.table.value') }}</th>
                            <th>{{ __('admin.contracts.table.status') }}</th>
                            <th class="text-end">{{ __('admin.contracts.table.actions') }}</th>
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
                                    <a href="{{ route('admin.contracts.show', $contract->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="{{ __('admin.contracts.table.view_details') }}">
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
                                    <p class="mb-0">{{ __('admin.contracts.table.no_contracts') }}</p>
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
                    {{ __('admin.contracts.table.showing', ['from' => $contracts->firstItem() ?? 0, 'to' => $contracts->lastItem() ?? 0, 'total' => $contracts->total() ?? 0]) }}
                </div>
                {{ $contracts->links("vendor.pagination.custom") }}
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
