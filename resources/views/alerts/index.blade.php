@extends('shared.dashboard')
@section('title', 'Alerts')
@section('content')
<div class="page-content">
    <div class="card radius-10">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-0">Alerts</h5>
                </div>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alerts as $alert)
                        <tr>
                            <td>
                                @switch($alert->type)
                                    @case('expired')
                                        <span class="badge bg-danger">Expired</span>
                                        @break
                                    @case('payment_due')
                                        <span class="badge bg-warning">Payment Due</span>
                                        @break
                                    @case('renewal_needed')
                                        <span class="badge bg-info">Renewal Needed</span>
                                        @break
                                    @case('monthly_report')
                                        <span class="badge bg-primary">Monthly Report</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $alert->message }}</td>
                            <td>
                                @if($alert->status === 'unread')
                                    <span class="badge bg-danger">Unread</span>
                                @else
                                    <span class="badge bg-success">Read</span>
                                @endif
                            </td>
                            <td>{{ $alert->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($alert->status === 'unread')
                                    <a href="{{ route('alerts.mark-as-read', $alert->id) }}" 
                                       class="btn btn-sm btn-primary">Mark as Read</a>
                                @endif
                                @if($alert->contract_id)
                                    <a href="{{ url('contract.view') }}" 
                                       class="btn btn-sm btn-info">View Contract</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $alerts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
