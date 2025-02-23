@extends('shared.dashboard')

@section('content')
<div class="px-4 container-fluid">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Contract Details</h1>
        <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary">Back to Contracts</a>
    </div>

    <div class="row">
        <!-- Basic Contract Information -->
        <div class="col-xl-4">
            <div class="mb-4 card">
                <div class="card-header">
                    <i class="fas fa-file-contract me-1"></i>
                    Contract Information
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Contract Type</dt>
                        <dd class="col-sm-8">{{ $contract->type->name }}</dd>
                        
                        <dt class="col-sm-4">Customer</dt>
                        <dd class="col-sm-8">{{ $contract->customer->name }}</dd>
                        
                        <dt class="col-sm-4">Start Date</dt>
                        <dd class="col-sm-8">{{ $contract->contract_start_date }}</dd>
                        
                        <dt class="col-sm-4">End Date</dt>
                        <dd class="col-sm-8">{{ $contract->contract_end_date }}</dd>
                        
                        <dt class="col-sm-4">Duration</dt>
                        <dd class="col-sm-8">{{ $duration }} months</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Payment Progress -->
        <div class="col-xl-4">
            <div class="mb-4 card">
                <div class="card-header">
                    <i class="fas fa-money-bill me-1"></i>
                    Payment Status
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="mb-1 d-flex justify-content-between">
                            <span>Payment Progress</span>
                            <span>{{ $paymentProgress }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $paymentProgress }}%"
                                aria-valuenow="{{ $paymentProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <dl class="mt-3 row">
                        <dt class="col-sm-6">Total Value</dt>
                        <dd class="col-sm-6">{{ number_format($contract->contract_price, 2) }}</dd>
                        
                        <dt class="col-sm-6">Paid Amount</dt>
                        <dd class="col-sm-6">{{ number_format($totalPayments, 2) }}</dd>
                        
                        <dt class="col-sm-6">Remaining</dt>
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
                    Time Progress
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="mb-1 d-flex justify-content-between">
                            <span>Time Progress</span>
                            <span>{{ $timeProgress }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $timeProgress }}%"
                                aria-valuenow="{{ $timeProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <h3 class="mb-0">{{ max(0, $remainingTime) }}</h3>
                        <p class="text-muted">Days Remaining</p>
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
                    <a class="nav-link active" data-bs-toggle="tab" href="#payments">Payments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tickets">Tickets</a>
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
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contract->payments as $payment)
                                <tr>
                                    <td>{{ $payment->due_date }}</td>
                                    <td>{{ number_format($payment->payment_amount, 2) }}</td>
                                    <td><span class="badge bg-{{ $payment->payment_status == 'paid' ? 'success' : 'warning' }}">{{ $payment->payment_status }}</span></td>
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
                                    <th>Title</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contract->customer->tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->tiket_title }}</td>
                                    <td>{{ $contract->customer->name }}</td>
                                    <td><span class="badge bg-{{ $ticket->status == 'open' ? 'success' : 'warning' }}">{{ $ticket->status }}</span></td>
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