@extends('shared.dashboard')
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
        <!-- Statistics Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
            <div class="col">
                <div class="border-4 card radius-10 border-start border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{ __('sales_views.current_contracts') }}</p>
                                <h4 class="my-1 text-info">{{ $totalContracts }}</h4>
                                <p class="mb-0 font-13">{{ $approvedContracts }} {{ __('sales_views.approved') }}</p>
                            </div>
                            <div class="text-white widgets-icons-2 rounded-circle bg-gradient-blues ms-auto">
                                <i class='bx bxs-report'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="border-4 card radius-10 border-start border-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{ __('sales_views.total_revenue') }}</p>
                                <h4 class="my-1 text-danger">{{ number_format($totalRevenue, 2) }} {{ __('sales_views.sar') }}</h4>
                                <p class="mb-0 font-13">{{ __('sales_views.from_approved_contracts') }}</p>
                            </div>
                            <div class="text-white widgets-icons-2 rounded-circle bg-gradient-burning ms-auto">
                                <i class='bx bxs-wallet'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="border-4 card radius-10 border-start border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{ __('sales_views.my_clients') }}</p>
                                <h4 class="my-1 text-success">{{ $totalClients }}</h4>
                                <p class="mb-0 font-13">{{ __('sales_views.active_clients') }}</p>
                            </div>
                            <div class="text-white widgets-icons-2 rounded-circle bg-gradient-ohhappiness ms-auto">
                                <i class='bx bx-user'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="border-4 card radius-10 border-start border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{ __('sales_views.customer_support') }}</p>
                                <h4 class="my-1 text-warning">{{ $openTickets }}</h4>
                                <p class="mb-0 font-13">
                                    @if ($urgentTickets > 0)
                                        <span class="text-danger">{{ $urgentTickets }} {{ __('sales_views.urgent') }}</span>
                                    @else
                                        {{ __('sales_views.no_urgent_tickets') }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-white widgets-icons-2 rounded-circle bg-gradient-orange ms-auto">
                                <i class='bx bx-support'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contracts Overview -->
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card radius-10">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">{{ __('sales_views.current_contracts') }}</h6>
                            </div>
                            <div class="ms-auto">
                                <a href="{{ route('sales.contract.type.cards') }}" class="btn btn-primary btn-sm">{{ __('sales_views.create_new_contract') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('sales_views.contract_number') }}</th>
                                        <th>{{ __('sales_views.client') }}</th>
                                        <th>{{ __('sales_views.type') }}</th>
                                        <th>{{ __('sales_views.status') }}</th>
                                        <th>{{ __('sales_views.created') }}</th>
                                        <th>{{ __('sales_views.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentContracts as $contract)
                                        <tr>
                                            <td>{{ $contract->contract_number }}</td>
                                            <td>{{ $contract->customer->name }}</td>
                                            <td>{{ $contract->type->name }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $contract->contract_status == 'approved' ? 'success' : ($contract->contract_status == 'completed' ? 'primary' : 'danger') }}">
                                                    {{ ucfirst($contract->contract_status) }}
                                                </span>
                                            </td>
                                            <td>{{ $contract->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if ($contract->contract_status == 'approved' || $contract->contract_status == 'pending')
                                                    <a href="{{ route('contract.show.details', $contract->id) }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="bx bx-show"></i> {{ __('sales_views.view') }}
                                                    </a>
                                                @elseif($contract->contract_status == 'completed')
                                                    <a href="{{ route('completed.show.all') }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="bx bx-show"></i> {{ __('sales_views.view') }}
                                                    </a>
                                                @elseif($contract->contract_status == 'stopped')
                                                    <a href="{{ route('stopped.show.all') }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="bx bx-show"></i> {{ __('sales_views.view') }}
                                                    </a>
                                                @elseif($contract->contract_status == 'canceled' || $contract->contract_status == 'Not approved')
                                                    <a href="{{ route('canceled.show.all') }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="bx bx-show"></i> {{ __('sales_views.view') }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">{{ __('sales_views.no_contracts_found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $recentContracts->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Support -->
            <div class="col-12 col-lg-4">
                <div class="card radius-10">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">{{ __('sales_views.customer_suggestions') }}</h6>
                            </div>
                            <div class="ms-auto">
                                <a href="{{ route('sales.tikets') }}" class="btn btn-primary btn-sm">{{ __('sales_views.view_all') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @forelse($recentTickets as $ticket)
                            <div class="mb-4 d-flex align-items-center">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">{{ $ticket->tiket_title }}</h6>
                                    <p class="mb-0 text-secondary small">{{ $ticket->client_info->name }}</p>
                                    <span
                                        class="badge bg-{{ $ticket->priority == 'high' ? 'danger' : ($ticket->priority == 'medium' ? 'warning' : 'info') }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                    <small class="ms-2 text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="ms-auto">
                                    <a href="{{ route('sales.show.ticket', $ticket->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="mb-0 text-center">No suggestions found</p>
                        @endforelse
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $recentTickets->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
