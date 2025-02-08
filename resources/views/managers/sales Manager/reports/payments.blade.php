@extends('managers.sales Manager.reports.base_report')

@section('report_title')
Payments Report {{ request('start_date') && request('end_date') ? '(' . request('start_date') . ' to ' . request('end_date') . ')' : '' }}
@endsection

@section('report_content')
<div class="table-responsive">
    <table class="table mb-0 align-middle">
        <thead class="table-light">
            <tr>
                <th>Payment ID</th>
                <th>Contract</th>
                <th>Client</th>
                <th>Sales Agent</th>
                <th>Amount</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($remainingPayments as $payment)
            <tr>
                <td>{{ $payment->id }}</td>
                <td>{{ $payment->contract->contract_number }}</td>
                <td>{{ $payment->contract->customer->name }}</td>   
                <td>{{ $payment->contract->salesRepresentative->name }}</td>
                <td>{{ number_format($payment->payment_amount, 2) }}</td>
                <td>{{ $payment->due_date ? date('Y-m-d', strtotime($payment->due_date)) : 'Not set' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No remaining payments found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end fw-bold">Total Remaining:</td>
                <td colspan="3" class="fw-bold">{{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
