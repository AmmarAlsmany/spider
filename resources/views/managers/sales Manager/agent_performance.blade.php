@extends('shared.dashboard')
@section('content')
<div class="page-content">
    @include('managers.sales Manager.partials.performance_filters')
    
    @foreach($performanceData as $data)
    <div class="card radius-10 mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-0">{{ $data['agent']->name }}'s Performance</h5>
                    <p class="mb-0 text-secondary">Current Month: {{ date('F Y') }}</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
                <div class="col">
                    <div class="card border-primary border-bottom border-3 border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">New Contracts (This Month)</p>
                                    <h4 class="my-1">{{ $data['current_month']['total_new_contracts'] }}</h4>
                                    <p class="mb-0">Value: {{ number_format($data['current_month']['total_contract_value'], 2) }}</p>
                                </div>
                                <div class="widget-icon-large bg-gradient-purple text-white ms-auto">
                                    <i class="bx bx-file"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-success border-bottom border-3 border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Collections (This Month)</p>
                                    <h4 class="my-1">{{ number_format($data['current_month']['total_collections'], 2) }}</h4>
                                </div>
                                <div class="widget-icon-large bg-gradient-success text-white ms-auto">
                                    <i class="bx bx-money"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-info border-bottom border-3 border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">All Time Contracts</p>
                                    <h4 class="my-1">{{ $data['all_time']['total_contracts'] }}</h4>
                                </div>
                                <div class="widget-icon-large bg-gradient-info text-white ms-auto">
                                    <i class="bx bx-folder"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-warning border-bottom border-3 border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">All Time Collections</p>
                                    <h4 class="my-1">{{ number_format($data['all_time']['total_collections'], 2) }}</h4>
                                </div>
                                <div class="widget-icon-large bg-gradient-warning text-white ms-auto">
                                    <i class="bx bx-bar-chart"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Contracts Table -->
            <div class="card radius-10 mt-4">
                <div class="card-header">
                    <h6 class="mb-0">New Contracts (This Month)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Contract Number</th>
                                    <th>Client</th>
                                    <th>Contract Value</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['current_month']['new_contracts'] as $contract)
                                <tr>
                                    <td>{{ $contract->contract_number }}</td>
                                    <td>{{ $contract->customer->name }}</td>
                                    <td>{{ number_format($contract->contract_price, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $contract->contract_status == 'approved' ? 'success' : 'warning' }}">
                                            {{ ucfirst($contract->contract_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $contract->created_at ? date('Y-m-d', strtotime($contract->created_at)) : 'Not set' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No new contracts this month</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Collections Table -->
            <div class="card radius-10 mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Collections (This Month)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Contract Number</th>
                                    <th>Client</th>
                                    <th>Amount</th>
                                    <th>Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['current_month']['collections'] as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>{{ $payment->contract->contract_number }}</td>
                                    <td>{{ $payment->contract->customer->name }}</td>
                                    <td>{{ number_format($payment->payment_amount, 2) }}</td>
                                    <td>{{ $payment->paid_at ? date('Y-m-d', strtotime($payment->paid_at)) : 'Not set' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No collections this month</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
