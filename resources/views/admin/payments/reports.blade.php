@extends('shared.dashboard')

@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">Payments</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">All Payments</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reports</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-3">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total Payments</p>
                            <h4 class="my-1" id="total-payments">0</h4>
                        </div>
                        <div class="text-primary ms-auto fs-3">
                            <i class="bx bx-money"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Paid Payments</p>
                            <h4 class="my-1" id="paid-payments">0</h4>
                        </div>
                        <div class="text-success ms-auto fs-3">
                            <i class="bx bx-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Pending Payments</p>
                            <h4 class="my-1" id="pending-payments">0</h4>
                        </div>
                        <div class="text-warning ms-auto fs-3">
                            <i class="bx bx-time"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Overdue Payments</p>
                            <h4 class="my-1" id="overdue-payments">0</h4>
                        </div>
                        <div class="text-danger ms-auto fs-3">
                            <i class="bx bx-error"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Monthly Payment Amounts</h6>
                        </div>
                    </div>
                    <div id="monthly-payments-chart" class="mt-4" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Top Customers by Payment</h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody id="top-customers-table">
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
$(function() {
    // Initialize chart
    const monthlyPaymentsChart = new ApexCharts(document.querySelector("#monthly-payments-chart"), {
        chart: {
            type: 'bar',
            height: 300,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '30%',
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
        series: [{
            name: 'Amount',
            data: []
        }],
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        },
        yaxis: {
            title: {
                text: 'Amount'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val
                }
            }
        },
        colors: ['#3461ff']
    });
    monthlyPaymentsChart.render();

    // Load data
    fetch("{{ route('admin.payments.reports.data') }}")
        .then(response => response.json())
        .then(data => {
            // Update statistics
            $('#total-payments').text(data.statistics.total);
            $('#paid-payments').text(data.statistics.paid);
            $('#pending-payments').text(data.statistics.pending);
            $('#overdue-payments').text(data.statistics.overdue);

            // Update monthly chart
            const monthlyData = Array(12).fill(0);
            data.monthly.forEach(item => {
                monthlyData[item.month - 1] = parseFloat(item.amount);
            });
            monthlyPaymentsChart.updateSeries([{
                name: 'Amount',
                data: monthlyData
            }]);

            // Update top customers table
            const topCustomersHtml = data.topCustomers.map(customer => `
                <tr>
                    <td>${customer.customer.name}</td>
                    <td>${parseFloat(customer.total_amount).toFixed(2)}</td>
                    <td>${customer.payment_count}</td>
                </tr>
            `).join('');
            $('#top-customers-table').html(topCustomersHtml);
        });
});
</script>
@endpush
