@extends('shared.dashboard')

@section('content')
<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Tickets</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tickets Reports</li>
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
                            <h6 class="mb-0">Tickets Status</h6>
                        </div>
                    </div>
                    <div class="chart-container-2 mt-4">
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
                            <h6 class="mb-0">Monthly Tickets Trend</h6>
                        </div>
                    </div>
                    <div class="chart-container-1 mt-4">
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
                            <h6 class="mb-0">Top Customers by Tickets</h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table align-middle mb-0" id="topCustomersTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Total Tickets</th>
                                    <th>Percentage</th>
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
                labels: ['Open', 'Closed'],
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
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const monthlyData = new Array(12).fill(0);
        
        data.monthly.forEach(item => {
            monthlyData[item.month - 1] = item.count;
        });

        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'Number of Tickets',
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
