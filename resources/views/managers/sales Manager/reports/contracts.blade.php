@extends('managers.sales Manager.reports.base_report')

@section('report_title')
Contracts Report {{ request('start_date') && request('end_date') ? '(' . request('start_date') . ' to ' . request('end_date') . ')' : '' }}
@endsection

@section('report_content')
<div class="table-responsive">
    <table class="table mb-0 align-middle">
        <thead class="table-light">
            <tr>
                <th>Contract Number</th>
                <th>Client</th>
                <th>Sales Agent</th>
                <th>Contract Value</th>
                <th>Start Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contracts as $contract)
            <tr>
                <td><a href="{{ route('sales_manager.contract.view', $contract->id) }}">{{ $contract->contract_number }}</a></td>
                <td><a href="{{ route('sales_manager.client.details', $contract->customer->id) }}">{{ $contract->customer->name }}</a></td>
                <td>{{ $contract->salesRepresentative->name }}</td>
                <td>{{ number_format($contract->contract_price, 2) }}</td>
                <td>{{ $contract->created_at ? date('Y-m-d', strtotime($contract->created_at)) : 'Not set' }}</td>
                <td>
                    <span class="badge bg-{{ $contract->contract_status == 'approved' ? 'success' : 'warning' }}">
                        {{ ucfirst($contract->contract_status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No contracts found for this period</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end fw-bold">Total Value:</td>
                <td colspan="3" class="fw-bold">{{ number_format($contracts->sum('contract_price'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
