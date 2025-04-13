@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">{{ __('admin.dashboard.title') }}</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.dashboard.overview') }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('admin.managers.create') }}" class="btn btn-primary">{{ __('admin.dashboard.add_manager') }}</a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Tickets Card -->
        <div class="mb-4 col-12 col-lg-3 col-md-6">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">{{ __('admin.stats.total_tickets') }}</p>
                            <h4 class="my-1">{{ $totalTickets }}</h4>
                            <p class="mb-0 font-13 text-success"><i class="align-middle bx bxs-up-arrow"></i> {{ $openTickets }} {{ __('admin.stats.open_tickets') }}</p>
                        </div>
                        <div class="widgets-icons bg-light-primary text-primary ms-auto"><i class="bx bx-support"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Contracts Card -->
        <div class="mb-4 col-12 col-lg-3 col-md-6">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">{{ __('admin.stats.active_contracts') }}</p>
                            <h4 class="my-1">{{ $activeContracts }}</h4>
                            <p class="mb-0 font-13 text-success">{{ __('admin.stats.total_contracts') }}: {{ $totalContracts }}</p>
                        </div>
                        <div class="widgets-icons bg-light-success text-success ms-auto"><i class="bx bx-file"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Card -->
        <div class="mb-4 col-12 col-lg-3 col-md-6">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">{{ __('admin.stats.monthly_revenue') }}</p>
                            <h4 class="my-1">{{ number_format($monthlyRevenue, 2) }}</h4>
                            <p class="mb-0 font-13 text-success">{{ __('admin.stats.yearly_revenue') }}: {{ number_format($yearlyRevenue, 2) }}</p>
                        </div>
                        <div class="widgets-icons bg-light-warning text-warning ms-auto"><i class="bx bx-dollar"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="mb-4 col-12 col-lg-3 col-md-6">
            <div class="card radius-10">
                <div class="card-body">
                    <h5 class="mb-3 card-title">{{ __('admin.quick_actions.title') }}</h5>
                    <div class="quick-actions">
                        <a href="{{ route('admin.managers.index') }}" class="mb-2 btn btn-light btn-sm w-100"><i class="bx bx-user-plus me-1"></i> {{ __('admin.quick_actions.manage_staff') }}</a>
                        <a href="{{ route('admin.reports.general') }}" class="btn btn-light btn-sm w-100"><i class="bx bx-chart me-1"></i> {{ __('admin.quick_actions.general_report') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Section -->
    <div class="row">
        <!-- Recent Tickets -->
        <div class="mb-4 col-12 col-lg-6">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="mb-4 d-flex align-items-center">
                        <h5 class="mb-0">{{ __('admin.recent.tickets.title') }}</h5>
                        <div class="ms-auto"><a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-outline-primary">{{ __('admin.recent.tickets.view_all') }}</a></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('admin.recent.tickets.table.title') }}</th>
                                    <th>{{ __('admin.recent.tickets.table.status') }}</th>
                                    <th>{{ __('admin.recent.tickets.table.date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTickets as $ticket)
                                <tr>
                                    <td>{{ Str::limit($ticket->tiket_title, 30) }}</td>
                                    <td><span class="badge bg-{{ $ticket->status === 'open' ? 'warning' : 'success' }}">{{ ucfirst($ticket->status) }}</span></td>
                                    <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Contracts -->
        <div class="mb-4 col-12 col-lg-6">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="mb-4 d-flex align-items-center">
                        <h5 class="mb-0">{{ __('admin.recent.contracts.title') }}</h5>
                        <div class="ms-auto"><a href="{{ route('admin.contracts.index') }}" class="btn btn-sm btn-outline-primary">{{ __('admin.recent.contracts.view_all') }}</a></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('admin.recent.contracts.table.id') }}</th>
                                    <th>{{ __('admin.recent.contracts.table.status') }}</th>
                                    <th>{{ __('admin.recent.contracts.table.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentContracts as $contract)
                                <tr>
                                    <td>#{{ $contract->contract_number }}</td>
                                    <td><span class="badge bg-{{ $contract->contract_status === 'approved' ? 'success' : 'secondary' }}">{{ ucfirst($contract->contract_status) }}</span></td>
                                    <td>{{ number_format($contract->contract_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection