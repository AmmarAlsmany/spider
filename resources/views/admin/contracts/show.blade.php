@extends('shared.dashboard')

@section('content')
    <div class="px-4 container-fluid">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="mt-4">{{ __('admin.contract_details.title') }}</h1>
            <a href="{{ route('admin.contracts.index') }}"
                class="btn btn-secondary">{{ __('admin.contract_details.back_button') }}</a>
        </div>

        <div class="row">
            <!-- Basic Contract Information -->
            <div class="col-xl-4">
                <div class="mb-4 card">
                    <div class="card-header">
                        <i class="fas fa-file-contract me-1"></i>
                        {{ __('admin.contract_details.information') }}
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">{{ __('admin.contract_details.type') }}</dt>
                            <dd class="col-sm-8">{{ $contract->type->name }}</dd>

                            <dt class="col-sm-4">{{ __('admin.contract_details.customer') }}</dt>
                            <dd class="col-sm-8">{{ $contract->customer->name }}</dd>

                            <dt class="col-sm-4">{{ __('admin.contract_details.start_date') }}</dt>
                            <dd class="col-sm-8">{{ $contract->contract_start_date }}</dd>

                            <dt class="col-sm-4">{{ __('admin.contract_details.end_date') }}</dt>
                            <dd class="col-sm-8">{{ $contract->contract_end_date }}</dd>

                            <dt class="col-sm-4">{{ __('admin.contract_details.duration') }}</dt>
                            <dd class="col-sm-8">{{ $duration }} {{ __('admin.contract_details.duration_months') }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Payment Progress -->
            <div class="col-xl-4">
                <div class="mb-4 card">
                    <div class="card-header">
                        <i class="fas fa-money-bill me-1"></i>
                        {{ __('admin.contract_details.payment_status') }}
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="mb-1 d-flex justify-content-between">
                                <span>{{ __('admin.contract_details.payment_progress') }}</span>
                                <span>{{ $paymentProgress }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $paymentProgress }}%"
                                    aria-valuenow="{{ $paymentProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <dl class="mt-3 row">
                            <dt class="col-sm-6">{{ __('admin.contract_details.total_value') }}</dt>
                            <dd class="col-sm-6">{{ number_format($contract->contract_price, 2) }}</dd>

                            <dt class="col-sm-6">{{ __('admin.contract_details.paid_amount') }}</dt>
                            <dd class="col-sm-6">{{ number_format($payments, 2) }}</dd>

                            <dt class="col-sm-6">{{ __('admin.contract_details.remaining') }}</dt>
                            <dd class="col-sm-6">{{ number_format($remainingAmount, 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Time Progress -->
            <div class="col-xl-4">
                <div class="mb-4 card">
                    <div class="card-header">
                        <i class="fas fa-clock me-1"></i>
                        {{ __('admin.contract_details.time_progress') }}
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="mb-1 d-flex justify-content-between">
                                <span>{{ __('admin.contract_details.time_progress') }}</span>
                                <span>{{ $timeProgress }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $timeProgress }}%"
                                    aria-valuenow="{{ $timeProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <h3 class="mb-0">{{ max(0, $remainingTime) }}</h3>
                            <p class="text-muted">{{ __('admin.contract_details.days_remaining') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Information Tabs -->
        <div class="mb-4 card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab"
                            href="#payments">{{ __('admin.contract_details.tabs.payments') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab"
                            href="#tickets">{{ __('admin.contract_details.tabs.tickets') }}</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Payments Tab -->
                    <div class="tab-pane fade show active" id="payments">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('admin.contract_details.payment_table.due_date') }}</th>
                                        <th>{{ __('admin.contract_details.payment_table.amount') }}</th>
                                        <th>{{ __('admin.contract_details.payment_table.status') }}</th>
                                        <th>{{ __('admin.contract_details.payment_table.reference') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contract->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->due_date }}</td>
                                            <td>{{ number_format($payment->payment_amount, 2) }}</td>
                                            <td><span
                                                    class="badge bg-{{ $payment->payment_status == 'paid' ? 'success' : 'warning' }}">{{ $payment->payment_status }}</span>
                                            </td>
                                            <td>{{ $payment->invoice_number }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tickets Tab -->
                    <div class="tab-pane fade" id="tickets">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('admin.contract_details.ticket_table.title') }}</th>
                                        <th>{{ __('admin.contract_details.ticket_table.client') }}</th>
                                        <th>{{ __('admin.contract_details.ticket_table.status') }}</th>
                                        <th>{{ __('admin.contract_details.ticket_table.created') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contract->customer->tickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->tiket_title }}</td>
                                            <td>{{ $contract->customer->name }}</td>
                                            <td><span
                                                    class="badge bg-{{ $ticket->status == 'open' ? 'success' : 'warning' }}">{{ $ticket->status }}</span>
                                            </td>
                                            <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
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
