@extends('shared.dashboard')
@section('content')
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0 text-gray-800">{{ __('admin.contract_reports.title') }}</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="mb-0 breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                            class="bx bx-home-alt"></i> {{ __('admin.sidebar.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('admin.contracts.index') }}">{{ __('admin.sidebar.contracts') }}</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ __('admin.contract_reports.breadcrumb') }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <select class="form-select" id="yearFilter" onchange="updateCharts()">
                            @for ($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                    {{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="mb-4 row">
                    <div class="col-12">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <h5 class="mb-3">{{ __('admin.contract_reports.monthly_overview') }}</h5>
                                <canvas id="monthlyStatsChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="mb-4 row g-3">
                    <div class="col-12 col-md-4">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded avatar avatar-lg bg-primary-subtle">
                                        <i class="bx bx-file fs-3 text-primary"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">{{ __('admin.contract_reports.total_contracts') }}</h6>
                                        <h4 class="mb-0" id="totalContracts">0</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded avatar avatar-lg bg-warning-subtle">
                                        <i class="bx bx-time fs-3 text-warning"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">{{ __('admin.contract_reports.expired_contracts') }}</h6>
                                        <h4 class="mb-0" id="expiredContracts">0</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded avatar avatar-lg bg-success-subtle">
                                        <i class="bx bx-dollar fs-3 text-success"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">{{ __('admin.contract_reports.total_revenue') }}</h6>
                                        <h4 class="mb-0" id="totalRevenue">$0</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Data Table -->
                <div class="border-0 shadow-sm card">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('admin.contract_reports.monthly_breakdown') }}</h5>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin.contract_reports.month') }}</th>
                                        <th>{{ __('admin.contract_reports.new_contracts') }}</th>
                                        <th>{{ __('admin.contract_reports.expired_contracts') }}</th>
                                        <th>{{ __('admin.contract_reports.revenue') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="monthlyDataTable">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            let monthlyStatsChart;

            // Initialize the chart
            function initChart() {
                const ctx = document.getElementById('monthlyStatsChart').getContext('2d');
                monthlyStatsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                                label: '{{ __('admin.contract_reports.new_contracts') }}',
                                data: [],
                                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                borderColor: 'rgb(59, 130, 246)',
                                borderWidth: 1
                            },
                            {
                                label: '{{ __('admin.contract_reports.expired_contracts') }}',
                                data: [],
                                backgroundColor: 'rgba(245, 158, 11, 0.5)',
                                borderColor: 'rgb(245, 158, 11)',
                                borderWidth: 1
                            },
                            {
                                label: '{{ __('admin.contract_reports.revenue') }}',
                                data: [],
                                backgroundColor: 'rgba(16, 185, 129, 0.5)',
                                borderColor: 'rgb(16, 185, 129)',
                                borderWidth: 1,
                                yAxisID: 'revenue'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: '{{ __('admin.contract_reports.chart.number_of_contracts') }}'
                                }
                            },
                            revenue: {
                                position: 'right',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: '{{ __('admin.contract_reports.chart.revenue') }}'
                                }
                            }
                        }
                    }
                });
            }

            // Update charts and tables with new data
            function updateCharts() {
                const year = document.getElementById('yearFilter').value;

                fetch(`/admin/contracts/reports/data?year=${year}`)
                    .then(response => response.json())
                    .then(data => {
                        // Update chart data
                        monthlyStatsChart.data.labels = data.map(item => item.month);
                        monthlyStatsChart.data.datasets[0].data = data.map(item => item.new_contracts);
                        monthlyStatsChart.data.datasets[1].data = data.map(item => item.expired_contracts);
                        monthlyStatsChart.data.datasets[2].data = data.map(item => item.revenue);
                        monthlyStatsChart.update();

                        // Update summary stats
                        const totalContracts = data.reduce((sum, item) => sum + item.new_contracts, 0);
                        const totalExpired = data.reduce((sum, item) => sum + item.expired_contracts, 0);
                        const totalRev = data.reduce((sum, item) => sum + item.revenue, 0);

                        document.getElementById('totalContracts').textContent = totalContracts;
                        document.getElementById('expiredContracts').textContent = totalExpired;
                        document.getElementById('totalRevenue').textContent = '$' + totalRev.toLocaleString();

                        // Update table
                        const tableBody = document.getElementById('monthlyDataTable');
                        tableBody.innerHTML = data.map(item => `
                    <tr>
                        <td>${item.month}</td>
                        <td>${item.new_contracts}</td>
                        <td>${item.expired_contracts}</td>
                        <td>$${item.revenue.toLocaleString()}</td>
                    </tr>
                `).join('');
                    });
            }

            // Initialize chart when page loads
            document.addEventListener('DOMContentLoaded', function() {
                initChart();
                updateCharts();
            });
        </script>
    @endpush
@endsection
