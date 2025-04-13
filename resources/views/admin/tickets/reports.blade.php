@extends('shared.dashboard')

@section('content')
    <div class="page-content">
        <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
            <div class="breadcrumb-title pe-3">{{ __('admin.tickets.title') }}</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('admin.tickets.reports.title') }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Status Overview -->
            <div class="col-12 col-lg-4">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">{{ __('admin.tickets.reports.status_overview') }}</h6>
                            </div>
                        </div>
                        <div class="mt-4 chart-container-2">
                            <canvas id="ticketStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Trend -->
            <div class="col-12 col-lg-8">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">{{ __('admin.tickets.reports.monthly_trend') }}</h6>
                            </div>
                        </div>
                        <div class="mt-4 chart-container-1">
                            <canvas id="monthlyTicketsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Customers -->
            <div class="col-12">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">{{ __('admin.tickets.reports.top_customers') }}</h6>
                            </div>
                        </div>
                        <div class="mt-3 table-responsive">
                            <table class="table mb-0 align-middle" id="topCustomersTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin.tickets.reports.customer_name') }}</th>
                                        <th>{{ __('admin.tickets.reports.total_tickets') }}</th>
                                        <th>{{ __('admin.tickets.reports.percentage') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function() {
            // Load reports data
            $.get("{{ route('admin.tickets.reports.data') }}", function(data) {
                // Status Chart
                const statusCtx = document.getElementById('ticketStatusChart').getContext('2d');
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['{{ __('admin.tickets.reports.status.open') }}',
                            '{{ __('admin.tickets.reports.status.closed') }}'
                        ],
                        datasets: [{
                            data: [data.status.open, data.status.closed],
                            backgroundColor: ['#198754', '#6c757d'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Monthly Trend Chart
                const monthlyCtx = document.getElementById('monthlyTicketsChart').getContext('2d');
                const monthNames = [
                    '{{ __('admin.tickets.reports.months.jan') }}',
                    '{{ __('admin.tickets.reports.months.feb') }}',
                    '{{ __('admin.tickets.reports.months.mar') }}',
                    '{{ __('admin.tickets.reports.months.apr') }}',
                    '{{ __('admin.tickets.reports.months.may') }}',
                    '{{ __('admin.tickets.reports.months.jun') }}',
                    '{{ __('admin.tickets.reports.months.jul') }}',
                    '{{ __('admin.tickets.reports.months.aug') }}',
                    '{{ __('admin.tickets.reports.months.sep') }}',
                    '{{ __('admin.tickets.reports.months.oct') }}',
                    '{{ __('admin.tickets.reports.months.nov') }}',
                    '{{ __('admin.tickets.reports.months.dec') }}'
                ];
                const monthlyData = new Array(12).fill(0);

                data.monthly.forEach(item => {
                    monthlyData[item.month - 1] = item.count;
                });

                new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: monthNames,
                        datasets: [{
                            label: '{{ __('admin.tickets.reports.number_of_tickets') }}',
                            data: monthlyData,
                            borderColor: '#0d6efd',
                            tension: 0.3,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Top Customers Table
                const totalTickets = data.topCustomers.reduce((sum, customer) => sum + customer.count, 0);
                const tbody = $('#topCustomersTable tbody');

                data.topCustomers.forEach(customer => {
                    const percentage = ((customer.count / totalTickets) * 100).toFixed(1);
                    tbody.append(`
                <tr>
                    <td>${customer.customer.name}</td>
                    <td>${customer.count}</td>
                    <td>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: ${percentage}%"></div>
                        </div>
                        <span class="font-13">${percentage}%</span>
                    </td>
                </tr>
            `);
                });
            });
        });
    </script>
@endpush
