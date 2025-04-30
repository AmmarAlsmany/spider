@extends('managers.sales manager.reports.base_report')

@section('report_title')
Invoices Report {{ request('start_date') && request('end_date') ? '(' . request('start_date') . ' to ' . request('end_date') . ')' : '' }}
@endsection

@section('report_content')
<div class="mb-4 row">
    <div class="col-md-4">
        <div class="border-0 card border-primary border-bottom border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total Invoiced</p>
                        <h4 class="my-1 text-primary">{{ number_format($total, 2) }}</h4>
                    </div>
                    <div class="text-primary ms-auto"><i class='bx bx-dollar'></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="border-0 card border-success border-bottom border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Collected</p>
                        <h4 class="my-1 text-success">{{ number_format($collected, 2) }}</h4>
                    </div>
                    <div class="text-success ms-auto"><i class='bx bx-check-circle'></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="border-0 card border-danger border-bottom border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Remaining</p>
                        <h4 class="my-1 text-danger">{{ number_format($remaining, 2) }}</h4>
                    </div>
                    <div class="text-danger ms-auto"><i class='bx bx-time'></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table mb-0 align-middle">
        <thead class="table-light">
            <tr>
                <th>Invoice ID</th>
                <th>Contract</th>
                <th>Client</th>
                <th>Sales Agent</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->contract->contract_number }}</td>
                <td>{{ $invoice->contract->customer->name }}</td>
                <td>{{ $invoice->contract->salesRepresentative->name }}</td>
                <td>{{ number_format($invoice->payment_amount, 2) }}</td>
                <td>
                    <span class="badge bg-{{ $invoice->paid_at ? 'success' : 'warning' }}">
                        {{ $invoice->paid_at ? 'Paid' : 'Pending' }}
                    </span>
                </td>
                <td>{{ $invoice->due_date ? date('Y-m-d', strtotime($invoice->due_date)) : 'Not set' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No invoices found for this period</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $invoices->links("vendor.pagination.custom") }}
</div>
@endsection
