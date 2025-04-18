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
    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-0">{{ $agent->name }}'s Performance</h5>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('sales_manager.manage_agents') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back"></i> Back to Agents
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Date Range Filter -->
            <div class="mb-4 row">
                <div class="col-md-6">
                    <form action="{{ route('sales_manager.agent.performance', $agent->id) }}" method="GET" class="gap-2 d-flex">
                        <div class="input-group">
                            <span class="input-group-text">From</span>
                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">To</span>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        @if(request('start_date') || request('end_date'))
                            <a href="{{ route('sales_manager.agent.performance', $agent->id) }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
                <div class="col">
                    <div class="border-0 card border-primary border-bottom border-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Total Contracts</p>
                                    <h4 class="my-1">{{ $stats['total_contracts'] }}</h4>
                                    <p class="mb-0 font-13">Value: ${{ number_format($stats['total_contract_value'], 2) }}</p>
                                </div>
                                <div class="text-white widget-icon-large bg-gradient-purple ms-auto">
                                    <i class="bx bx-file"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col">
                    <div class="border-0 card border-success border-bottom border-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Total Collections</p>
                                    <h4 class="my-1">${{ number_format($stats['total_collections'], 2) }}</h4>
                                    <p class="mb-0 font-13">From {{ $stats['paid_contracts'] }} contracts</p>
                                </div>
                                <div class="text-white widget-icon-large bg-gradient-success ms-auto">
                                    <i class="bx bx-money"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="border-0 card border-info border-bottom border-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Average Contract Value</p>
                                    <h4 class="my-1">${{ number_format($stats['avg_contract_value'], 2) }}</h4>
                                </div>
                                <div class="text-white widget-icon-large bg-gradient-info ms-auto">
                                    <i class="bx bx-line-chart"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="border-0 card border-warning border-bottom border-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Pending Collections</p>
                                    <h4 class="my-1">${{ number_format($stats['pending_collections'], 2) }}</h4>
                                    <p class="mb-0 font-13">From {{ $stats['pending_contracts'] }} contracts</p>
                                </div>
                                <div class="text-white widget-icon-large bg-gradient-warning ms-auto">
                                    <i class="bx bx-time"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Performance Chart -->
            <div class="mt-4 card radius-10">
                <div class="card-header">
                    <h6 class="mb-0">Monthly Performance</h6>
                </div>
                <div class="card-body">
                    <div id="monthlyPerformanceChart"></div>
                </div>
            </div>

            <!-- Recent Contracts -->
            <div class="mt-4 card radius-10">
                <div class="card-header">
                    <h6 class="mb-0">Recent Contracts</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Contract Number</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Value</th>
                                    <th>Collections</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_contracts as $contract)
                                <tr>
                                    <td>{{ $contract->contract_number }}</td>
                                    <td>{{ $contract->customer->name }}</td>
                                    <td>{{ $contract->contract_start_date }}</td>
                                    <td>
                                        <span class="badge {{ 
                                            $contract->contract_status == 'active' ? 'bg-success' : 
                                            ($contract->contract_status == 'expired' ? 'bg-danger' : 
                                            ($contract->contract_status == 'pending' ? 'bg-warning' : 'bg-info')) 
                                        }}">
                                            {{ ucfirst($contract->contract_status) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($contract->contract_price, 2) }}</td>
                                    <td>${{ number_format($contract->collections_sum ?? 0, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No contracts found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var options = {
            series: [{
                name: 'Contracts Value',
                data: @json($monthly_stats['contract_values'])
            }, {
                name: 'Collections',
                data: @json($monthly_stats['collections'])
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: false,
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: @json($monthly_stats['months']),
            },
            yaxis: {
                title: {
                    text: 'Amount ($)'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "$ " + val.toLocaleString()
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#monthlyPerformanceChart"), options);
        chart.render();
    });
</script>
@endpush