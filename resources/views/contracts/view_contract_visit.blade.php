@extends('shared.dashboard')
@section('content')
    <div class="page-content">
        <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('sales.dashboard') }}" class="text-decoration-none">
                                <i class="bx bx-home-alt"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('contract.show') }}" class="text-decoration-none">
                                {{ __('contracts.active_contracts') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-muted" aria-current="page">
                            {{ __('contract_views.visit_schedule') }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Contract Information Card -->
            <div class="mb-4 col-12">
                <div class="border-0 shadow-sm card">
                    <div class="bg-transparent card-header">
                        <h5 class="mb-0"><i
                                class="bx bx-info-circle me-2"></i>{{ __('contract_views.contract_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{{ __('contract_views.contract_number') }}</strong>
                                    {{ $contract->contract_number }}</p>
                                <p><strong>{{ __('contract_views.client_name') }}:</strong> {{ $contract->customer->name }}
                                </p>
                                <p><strong>{{ __('contract_views.client_email') }}:</strong>
                                    {{ $contract->customer->email }}</p>
                                <p><strong>{{ __('contract_views.client_phone') }}:</strong>
                                    {{ $contract->customer->phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ __('contract_views.start_date') }}:</strong>
                                    {{ $contract->contract_start_date }}</p>
                                <p><strong>{{ __('contract_views.end_date') }}:</strong>
                                    {{ $contract->contract_end_date }}</p>
                                <p><strong>{{ __('contract_views.number_of_visits') }}:</strong>
                                    {{ $contract->number_of_visits }} {{ __('contract_views.visits_per_branch') }}</p>
                                <p><strong>{{ __('contract_views.status') }}:</strong> <span
                                        class="badge bg-success">{{ __('contract_views.active') }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visit Schedule Card -->
            <div class="col-12">
                <div class="border-0 shadow-sm card">
                    <div class="bg-transparent card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bx bx-calendar me-2"></i>{{ __('contract_views.visit_schedule') }}
                        </h5>
                        <div>
                            <a href="{{ route('contract.insect-analytics', $contract->id) }}" class="btn btn-primary">
                                <i class="bx bx-line-chart"></i> {{ __('contract_views.insect_analytics') }}
                            </a>
                        </div>
                    </div>
                    <div class="p-0 card-body">
                        <div class="accordion custom-accordion" id="contractAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#contract-{{ $contract->id }}">
                                        <div class="contract-info w-100">
                                            <div class="flex-wrap d-flex justify-content-between align-items-start w-100">
                                                <div class="contract-main-info">
                                                    <h5 class="gap-2 contract-title d-flex align-items-center">
                                                        <i class="bx bx-buildings me-1 text-primary"></i>
                                                        {{ $contract->customer->name }}
                                                    </h5>
                                                    <div class="contract-details">
                                                        <span class="contract-detail-item">
                                                            <i class="bx bx-hash"></i>
                                                            {{ $contract->contract_number }}
                                                        </span>
                                                        @php
                                                            $branchCount = App\Models\branchs::where(
                                                                'contracts_id',
                                                                $contract->id,
                                                            )->count();
                                                        @endphp
                                                        <span class="contract-detail-item">
                                                            <i class="bx bx-git-branch"></i>
                                                            {{ $branchCount }}
                                                            {{ Str::plural('Branch', $branchCount) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex-wrap gap-2 visit-stats d-flex">
                                                    @php
                                                        $totalVisits = $contract->visitSchedules->count();
                                                        $completedVisits = $contract->visitSchedules
                                                            ->where('status', 'completed')
                                                            ->count();
                                                        $pendingVisits = $contract->visitSchedules
                                                            ->where('status', 'scheduled')
                                                            ->count();
                                                        $cancelledVisits = $contract->visitSchedules
                                                            ->where('status', 'cancelled')
                                                            ->count();
                                                    @endphp
                                                    <div class="visit-stat-item">
                                                        <span class="stat-label">Total Visits</span>
                                                        <span class="stat-value text-primary">{{ $totalVisits }}</span>
                                                    </div>
                                                    <div class="visit-stat-item">
                                                        <span class="stat-label">Completed</span>
                                                        <span class="stat-value text-success">{{ $completedVisits }}</span>
                                                    </div>
                                                    <div class="visit-stat-item">
                                                        <span class="stat-label">Pending</span>
                                                        <span class="stat-value text-warning">{{ $pendingVisits }}</span>
                                                    </div>
                                                    <div class="visit-stat-item">
                                                        <span class="stat-label">Cancelled</span>
                                                        <span class="stat-value text-danger">{{ $cancelledVisits }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="contract-{{ $contract->id }}" class="accordion-collapse collapse show"
                                    aria-labelledby="heading{{ $contract->id }}" data-bs-parent="#contractAccordion">
                                    <div class="accordion-body">
                                        <!-- Branch Links Instead of Tabs -->
                                        <div class="mb-4">
                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 fw-semibold">
                                                    <i class="bx bx-map-pin text-primary me-1"></i>
                                                    {{ __('contract_views.contract_locations') }}
                                                </h6>
                                            </div>

                                            <div class="row g-3">
                                                @php
                                                    // Get all branches for this contract
                                                    $contractBranches = App\Models\branchs::where(
                                                        'contracts_id',
                                                        $contract->id,
                                                    )->get();

                                                    // Get all visits without a branch (main location)
                                                    $mainLocationVisits = $contract->visitSchedules
                                                        ->filter(function ($visit) {
                                                            return $visit->branch_id == null;
                                                        })
                                                        ->count();
                                                @endphp

                                                <!-- Main Location -->
                                                @if ($mainLocationVisits > 0)
                                                    <div class="col-md-6 col-lg-4">
                                                        <a href="{{ route('contract.branch.visits', [$contract->id, 'main']) }}"
                                                            class="text-decoration-none">
                                                            <div class="border branch-card card h-100 hover-shadow">
                                                                <div class="p-3 card-body">
                                                                    <div class="d-flex align-items-center">
                                                                        <div
                                                                            class="avatar-md rounded-circle bg-soft-primary me-3 d-flex align-items-center justify-content-center">
                                                                            <i
                                                                                class="bx bx-building text-primary font-size-20"></i>
                                                                        </div>
                                                                        <div>
                                                                            <h6 class="mb-1">
                                                                                {{ __('contract_views.main_location') }}
                                                                            </h6>
                                                                            <div
                                                                                class="text-muted small d-flex align-items-center">
                                                                                <i class="bx bx-calendar me-1"></i>
                                                                                {{ $mainLocationVisits }}
                                                                                {{ __('contract_views.visits') }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-2 d-flex justify-content-end">
                                                                        <span
                                                                            class="px-2 py-1 badge bg-soft-primary text-primary rounded-pill">
                                                                            <i class="bx bx-chevron-right"></i>
                                                                            {{ __('contract_views.view_visits') }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endif

                                                <!-- Contract Branches -->
                                                @foreach ($contractBranches as $branch)
                                                    <div class="col-md-6 col-lg-4">
                                                        <a href="{{ route('contract.branch.visits', [$contract->id, $branch->id]) }}"
                                                            class="text-decoration-none">
                                                            <div class="border branch-card card h-100 hover-shadow">
                                                                <div class="p-3 card-body">
                                                                    <div class="d-flex align-items-center">
                                                                        <div
                                                                            class="avatar-md rounded-circle bg-soft-info me-3 d-flex align-items-center justify-content-center">
                                                                            <i
                                                                                class="bx bx-map-pin text-info font-size-20"></i>
                                                                        </div>
                                                                        <div>
                                                                            <h6 class="mb-1">{{ $branch->branch_name }}
                                                                            </h6>
                                                                            <div
                                                                                class="text-muted small d-flex align-items-center">
                                                                                <i class="bx bx-calendar me-1"></i>
                                                                                {{ $contract->visitSchedules->where('branch_id', $branch->id)->count() }}
                                                                                {{ __('contract_views.visits') }}
                                                                            </div>
                                                                            <small
                                                                                class="ms-1 badge rounded-pill bg-info">{{ $branch->branch_address }}</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-2 d-flex justify-content-end">
                                                                        <span
                                                                            class="px-2 py-1 badge bg-soft-info text-info rounded-pill">
                                                                            <i class="bx bx-chevron-right"></i>
                                                                            {{ __('contract_views.view_visits') }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endforeach

                                                <!-- No Branches Message -->
                                                @if ($contractBranches->count() == 0 && $mainLocationVisits == 0)
                                                    <div class="col-12">
                                                        <div class="alert alert-info">
                                                            <i class="bx bx-info-circle me-1"></i>
                                                            {{ __('contract_views.no_branches_found') }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('styles')
            <style>
                /* Contract Info Styles */
                .contract-info {
                    display: flex;
                    flex-direction: column;
                    gap: 1rem;
                    width: 100%;
                }

                .contract-main-info {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }

                .contract-title {
                    font-size: 1.1rem;
                    font-weight: 600;
                    color: #1e293b;
                    margin: 0;
                    display: flex;
                    align-items: center;
                }

                .contract-details {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 1rem;
                    align-items: center;
                }

                .contract-detail-item {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    font-size: 0.875rem;
                    color: #64748b;
                }

                /* Visit Stats Styles */
                .visit-stats {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 1rem;
                    padding: 0.5rem 0;
                }

                .visit-stat-item {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 0.25rem;
                    padding: 0.5rem 1rem;
                    background-color: #f8fafc;
                    border-radius: 8px;
                    min-width: 100px;
                }

                .stat-label {
                    font-size: 0.75rem;
                    color: #64748b;
                    font-weight: 500;
                }

                .stat-value {
                    font-size: 1.125rem;
                    font-weight: 600;
                }

                /* Custom Accordion Styles */
                .custom-accordion .accordion-item {
                    border-left: 0;
                    border-right: 0;
                    border-radius: 0;
                }

                .custom-accordion .accordion-item:first-of-type {
                    border-top: 0;
                }

                .custom-accordion .accordion-button {
                    padding: 1rem 1.25rem;
                    background-color: #fff;
                    box-shadow: none;
                }

                .custom-accordion .accordion-button:not(.collapsed) {
                    color: var(--bs-primary);
                    background-color: rgba(var(--bs-primary-rgb), 0.05);
                    box-shadow: none;
                }

                .custom-accordion .accordion-button:focus {
                    box-shadow: none;
                    border-color: rgba(var(--bs-primary-rgb), 0.1);
                }

                .custom-accordion .accordion-body {
                    padding: 1rem;
                }

                /* Branch Card Styles */
                .branch-card {
                    transition: all 0.2s ease-in-out;
                }

                .branch-card:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 8px 24px rgba(149, 157, 165, 0.15) !important;
                    border-color: var(--bs-primary) !important;
                }

                .avatar-md {
                    width: 50px;
                    height: 50px;
                }

                .font-size-20 {
                    font-size: 20px;
                }

                .bg-soft-primary {
                    background-color: rgba(var(--bs-primary-rgb), 0.1);
                }

                .bg-soft-info {
                    background-color: rgba(var(--bs-info-rgb), 0.1);
                }

                .text-primary {
                    color: var(--bs-primary) !important;
                }

                .text-info {
                    color: var(--bs-info) !important;
                }

                .hover-shadow:hover {
                    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
                }

                /* Mobile Responsiveness */
                @media (max-width: 767.98px) {
                    .contract-info {
                        gap: 0.75rem;
                    }

                    .visit-stats {
                        width: 100%;
                        justify-content: space-between;
                        gap: 0.5rem;
                    }

                    .visit-stat-item {
                        flex: 1;
                        min-width: calc(50% - 0.5rem);
                        padding: 0.375rem 0.5rem;
                    }

                    .stat-label {
                        font-size: 0.7rem;
                    }

                    .stat-value {
                        font-size: 1rem;
                    }
                }

                @media (max-width: 575.98px) {
                    .visit-stat-item {
                        min-width: calc(50% - 0.25rem);
                    }
                }
            </style>
        @endpush
    @endsection
