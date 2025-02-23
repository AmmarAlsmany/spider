@extends('shared.dashboard')

@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">Reports</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">General Report</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Payments Stats -->
        <div class="col-12 col-lg-4">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h5 class="mb-0">Payments Overview</h5>
                        </div>
                        <div class="ms-auto fs-3 text-primary">
                            <i class="bx bx-money"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-grow-1">
                                <p class="mb-0">Total Payments</p>
                                <h4 class="mb-0" id="total-payments">0</h4>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0">Total Amount Paid</p>
                                <h4 class="mb-0" id="total-amount-paid">$0</h4>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0">Total Amount Unpaid</p>
                                <h4 class="mb-0" id="total-amount-unpaid">$0</h4>
                            </div>
                        </div>
                        <div class="progress radius-10" style="height: 10px">
                            <div class="progress-bar bg-success" id="payments-progress-paid" role="progressbar" style="width: 0%"></div>
                            <div class="progress-bar bg-warning" id="payments-progress-unpaid" role="progressbar" style="width: 0%"></div>
                            <div class="progress-bar bg-danger" id="payments-progress-overdue" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <p class="mb-0"><span class="dot-indicator bg-success"></span> Paid <span id="paid-payments">0</span></p>
                            <p class="mb-0"><span class="dot-indicator bg-warning"></span> Unpaid <span id="unpaid-payments">0</span></p>
                            <p class="mb-0"><span class="dot-indicator bg-danger"></span> Overdue <span id="overdue-payments">0</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contracts Stats -->
        <div class="col-12 col-lg-4">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h5 class="mb-0">Contracts Overview</h5>
                        </div>
                        <div class="ms-auto fs-3 text-success">
                            <i class="bx bx-file"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-grow-1">
                                <p class="mb-0">Total Contracts</p>
                                <h4 class="mb-0" id="total-contracts">0</h4>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0">Ending Soon</p>
                                <h4 class="mb-0" id="contracts-ending-soon">0</h4>
                            </div>
                        </div>
                        <div class="progress radius-10" style="height: 10px">
                            <div class="progress-bar bg-success" id="contracts-progress-active" role="progressbar" style="width: 0%"></div>
                            <div class="progress-bar bg-warning" id="contracts-progress-pending" role="progressbar" style="width: 0%"></div>
                            <div class="progress-bar bg-danger" id="contracts-progress-expired" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <p class="mb-0"><span class="dot-indicator bg-success"></span> Active <span id="active-contracts">0</span></p>
                            <p class="mb-0"><span class="dot-indicator bg-warning"></span> Pending <span id="pending-contracts">0</span></p>
                            <p class="mb-0"><span class="dot-indicator bg-danger"></span> Expired <span id="expired-contracts">0</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets Stats -->
        <div class="col-12 col-lg-4">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h5 class="mb-0">Tickets Overview</h5>
                        </div>
                        <div class="ms-auto fs-3 text-warning">
                            <i class="bx bx-support"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-grow-1">
                                <p class="mb-0">Total Tickets</p>
                                <h4 class="mb-0" id="total-tickets">0</h4>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0">Open Tickets</p>
                                <h4 class="mb-0" id="open-tickets">0</h4>
                            </div>
                        </div>
                        <div class="progress radius-10" style="height: 10px">
                            <div class="progress-bar bg-success" id="tickets-progress-closed" role="progressbar" style="width: 0%"></div>
                            <div class="progress-bar bg-warning" id="tickets-progress-pending" role="progressbar" style="width: 0%"></div>
                            <div class="progress-bar bg-primary" id="tickets-progress-open" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <p class="mb-0"><span class="dot-indicator bg-success"></span> Closed <span id="closed-tickets">0</span></p>
                            <p class="mb-0"><span class="dot-indicator bg-warning"></span> Pending <span id="pending-tickets">0</span></p>
                            <p class="mb-0"><span class="dot-indicator bg-primary"></span> Open <span id="active-tickets">0</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <!-- Monthly Trends -->
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h5 class="mb-0">Monthly Trends</h5>
                        </div>
                        <div class="ms-auto">
                            <button class="btn btn-sm btn-outline-secondary" id="toggleChartData">
                                <i class="bx bx-refresh"></i> Switch View
                            </button>
                        </div>
                    </div>
                    <div id="monthly-trends-chart" style="height: 300px"></div>
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Top Customers</h5>
                    <div id="top-customers-list"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="mb-3">Recent Activities</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="recent-activities-table"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.dot-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 5px;
}
.customer-card {
    border-left: 4px solid #3461ff;
    background: #f8f9fa;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 5px;
}
.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}
.activity-icon.payment { background-color: #3461ff; }
.activity-icon.contract { background-color: #28a745; }
.activity-icon.ticket { background-color: #ffc107; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
$(function() {
    let monthlyChart;
    let currentChartView = 'count'; // or 'amount' for payments

    function initializeChart(data) {
        const options = {
            series: [
                {
                    name: 'Payments',
                    data: Array(12).fill(0)
                },
                {
                    name: 'Contracts',
                    data: Array(12).fill(0)
                },
                {
                    name: 'Tickets',
                    data: Array(12).fill(0)
                }
            ],
            chart: {
                type: 'line',
                height: 300,
                toolbar: {
                    show: false
                }
            },
            stroke: {
                width: [3, 3, 3],
                curve: 'smooth'
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            colors: ['#3461ff', '#28a745', '#ffc107'],
            legend: {
                position: 'top'
            }
        };

        monthlyChart = new ApexCharts(document.querySelector("#monthly-trends-chart"), options);
        monthlyChart.render();
    }

    function updateChart(data, view = 'count') {
        const monthlyData = Array(12).fill(0).map((_, index) => {
            const month = index + 1;
            return {
                payments: data.monthlyData.payments.find(d => d.month === month) || { count: 0, amount: 0 },
                contracts: data.monthlyData.contracts.find(d => d.month === month) || { count: 0 },
                tickets: data.monthlyData.tickets.find(d => d.month === month) || { count: 0 }
            };
        });

        const series = [
            {
                name: 'Payments',
                data: monthlyData.map(d => view === 'count' ? d.payments.count : d.payments.amount)
            },
            {
                name: 'Contracts',
                data: monthlyData.map(d => d.contracts.count)
            },
            {
                name: 'Tickets',
                data: monthlyData.map(d => d.tickets.count)
            }
        ];

        monthlyChart.updateSeries(series);
    }

    function updateStatistics(data) {
        // Update payment statistics
        $('#total-payments').text(data.statistics.payments.total);
        $('#total-amount-paid').text('$' + data.statistics.payments.totalPaidSum.toFixed(2));
        $('#total-amount-unpaid').text('$' + data.statistics.payments.totalUnpaidSum.toFixed(2));
        $('#paid-payments').text(data.statistics.payments.paid);
        $('#unpaid-payments').text(data.statistics.payments.unpaid);
        $('#overdue-payments').text(data.statistics.payments.overdue);

        // Update payment progress bars
        const totalPayments = data.statistics.payments.total;
        if (totalPayments > 0) {
            $('#payments-progress-paid').css('width', (data.statistics.payments.paid / totalPayments * 100) + '%');
            $('#payments-progress-unpaid').css('width', (data.statistics.payments.unpaid / totalPayments * 100) + '%');
            $('#payments-progress-overdue').css('width', (data.statistics.payments.overdue / totalPayments * 100) + '%');
        }

        // Update contract statistics
        $('#total-contracts').text(data.statistics.contracts.total);
        $('#contracts-ending-soon').text(data.statistics.contracts.endingSoon);
        $('#active-contracts').text(data.statistics.contracts.active);
        $('#pending-contracts').text(data.statistics.contracts.pending);
        $('#expired-contracts').text(data.statistics.contracts.expired);

        // Update contract progress bars
        const totalContracts = data.statistics.contracts.total;
        if (totalContracts > 0) {
            $('#contracts-progress-active').css('width', (data.statistics.contracts.active / totalContracts * 100) + '%');
            $('#contracts-progress-pending').css('width', (data.statistics.contracts.pending / totalContracts * 100) + '%');
            $('#contracts-progress-expired').css('width', (data.statistics.contracts.expired / totalContracts * 100) + '%');
        }

        // Update ticket statistics
        $('#total-tickets').text(data.statistics.tickets.total);
        $('#open-tickets').text(data.statistics.tickets.open);
        $('#closed-tickets').text(data.statistics.tickets.closed);
        $('#pending-tickets').text(data.statistics.tickets.pending);
        $('#active-tickets').text(data.statistics.tickets.open);

        // Update ticket progress bars
        const totalTickets = data.statistics.tickets.total;
        if (totalTickets > 0) {
            $('#tickets-progress-closed').css('width', (data.statistics.tickets.closed / totalTickets * 100) + '%');
            $('#tickets-progress-pending').css('width', (data.statistics.tickets.pending / totalTickets * 100) + '%');
            $('#tickets-progress-open').css('width', (data.statistics.tickets.open / totalTickets * 100) + '%');
        }
    }

    function updateTopCustomers(customers) {
        const html = customers.map(customer => `
            <div class="customer-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-0">${customer.name}</h6>
                        <p class="mb-0 text-secondary">
                            ${customer.contracts_count} Contracts | ${customer.tickets_count} Tickets
                        </p>
                    </div>
                    <div class="text-end">
                        <h6 class="mb-0">$${customer.payments_sum_payment_amount?.toFixed(2) || '0.00'}</h6>
                        <small class="text-secondary">Total Paid</small>
                    </div>
                </div>
            </div>
        `).join('');
        
        $('#top-customers-list').html(html);
    }

    function updateRecentActivities(activities) {
        const getStatusBadgeClass = (type, status) => {
            const classes = {
                payment: {
                    paid: 'success',
                    unpaid: 'warning',
                    overdue: 'danger'
                },
                contract: {
                    active: 'success',
                    pending: 'warning',
                    expired: 'danger'
                },
                ticket: {
                    closed: 'success',
                    pending: 'warning',
                    open: 'primary'
                }
            };
            return `bg-${classes[type][status] || 'secondary'}`;
        };

        const html = activities.map(activity => `
            <tr>
                <td>
                    <div class="activity-icon ${activity.type}">
                        <i class="bx bx-${activity.type === 'payment' ? 'money' : (activity.type === 'contract' ? 'file' : 'support')}"></i>
                    </div>
                </td>
                <td>${activity.title}</td>
                <td>${activity.description}</td>
                <td>
                    <span class="badge ${getStatusBadgeClass(activity.type, activity.status)}">
                        ${activity.status}
                    </span>
                </td>
                <td>${new Date(activity.date).toLocaleString()}</td>
            </tr>
        `).join('');
        
        $('#recent-activities-table').html(html);
    }

    // Initialize
    initializeChart();

    // Load data
    fetch("{{ route('admin.reports.general.data') }}")
        .then(response => response.json())
        .then(data => {
            updateStatistics(data);
            updateChart(data, currentChartView);
            updateTopCustomers(data.topCustomers);
            updateRecentActivities(data.recentActivities);
        });

    // Handle chart view toggle
    $('#toggleChartData').click(function() {
        currentChartView = currentChartView === 'count' ? 'amount' : 'count';
        fetch("{{ route('admin.reports.general.data') }}")
            .then(response => response.json())
            .then(data => {
                updateChart(data, currentChartView);
            });
    });
});
</script>
@endpush