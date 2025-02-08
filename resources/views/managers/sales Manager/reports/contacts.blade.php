@extends('managers.sales Manager.reports.base_report')

@section('report_title')
Customer Contacts Report {{ request('start_date') && request('end_date') ? '(' . request('start_date') . ' to ' . request('end_date') . ')' : '' }}
@endsection

@section('report_content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Sales Agent</th>
                            <th>Status</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->sales->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $customer->status == 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($customer->status) }}
                                    </span>
                                </td>
                                <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No customers found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
@endsection
