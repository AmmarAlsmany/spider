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
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col">
            <div class="border-0 border-4 card radius-10 border-start border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">{{ __('finance_views.total_contracts') }}</p>
                            <h4 class="my-1 text-info">{{ $totalContracts }}</h4>
                            <p class="mb-0 font-13">{{ $activeContracts }} {{ __('finance_views.active_contracts') }}</p>
                        </div>
                        <div class="text-white widgets-icons-2 rounded-circle bg-gradient-blues ms-auto">
                            <i class='bx bxs-folder'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="border-0 border-4 card radius-10 border-start border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">{{ __('finance_views.total_payments') }}</p>
                            <h4 class="my-1 text-danger">{{ number_format($totalRevenue, 2) }} {{ __('finance_views.currency_sar') }}</h4>
                            <p class="mb-0 font-13">
                                <span class="text-warning">{{ $pendingPayments }} {{ __('finance_views.pending') }}</span> / 
                                <span class="text-danger">{{ $overduePayments }} {{ __('finance_views.overdue') }}</span>
                            </p>
                        </div>
                        <div class="text-white widgets-icons-2 rounded-circle bg-gradient-burning ms-auto">
                            <i class='bx bxs-wallet'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="border-0 border-4 card radius-10 border-start border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">{{ __('finance_views.active_contracts') }}</p>
                            <h4 class="my-1 text-success">{{ $activeContracts }}</h4>
                            <p class="mb-0 font-13">{{ round(($activeContracts / ($totalContracts ?: 1)) * 100) }}% {{ __('finance_views.of_total') }}</p>
                        </div>
                        <div class="text-white widgets-icons-2 rounded-circle bg-gradient-ohhappiness ms-auto">
                            <i class='bx bxs-bar-chart-alt-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="border-0 border-4 card radius-10 border-start border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">{{ __('finance_views.open_tickets') }}</p>
                            <h4 class="my-1 text-warning">{{ $openTickets }}</h4>
                            <p class="mb-0 font-13">{{ __('finance_views.pending_support_requests') }}</p>
                        </div>
                        <div class="text-white widgets-icons-2 rounded-circle bg-gradient-orange ms-auto">
                            <i class='bx bxs-group'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">{{ __('finance_views.recent_payments') }}</h6>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('finance_views.contract_number') }}</th>
                            <th>{{ __('finance_views.payment_number') }}</th>
                            <th>{{ __('finance_views.payment_amount') }}</th>
                            <th>{{ __('finance_views.payment_date') }}</th>
                            <th>{{ __('finance_views.status') }}</th>
                            <th>{{ __('finance_views.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                        <tr>
                            <td>{{ $payment->contract->contract_number }}</td>
                            <td>{{ $payment->invoice_number }}</td>
                            <td>{{ number_format($payment->payment_amount, 2) }} {{ __('finance_views.currency_sar') }}</td>
                            <td>
                                @if($payment->postponementRequests->where('status', 'pending')->first())
                                    {{ \Carbon\Carbon::parse($payment->postponementRequests->where('status', 'pending')->first()->requested_date)->format('Y-m-d') }}
                                    <span class="badge bg-info">{{ __('finance_views.postponement_requested') }}</span>
                                @else
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}
                                @endif
                            </td>
                            <td>
                                @if($payment->payment_status == 'paid')
                                    <span class="badge bg-success">{{ __('finance_views.completed') }}</span>
                                @elseif($payment->postponementRequests->where('status', 'pending')->first())
                                    <span class="badge bg-info">{{ __('finance_views.postponement_pending') }}</span>
                                @elseif($payment->payment_date < now() && $payment->payment_status == 'overdue')
                                    <span class="badge bg-danger">{{ __('finance_views.overdue') }}</span>
                                @elseif($payment->payment_date < now() && $payment->payment_status == 'unpaid')
                                    <span class="badge bg-danger">{{ __('finance_views.unpaid') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('finance_views.pending') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('finance.payments.show', $payment->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-show"></i> {{ __('finance_views.view') }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('finance_views.no_payments_found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection