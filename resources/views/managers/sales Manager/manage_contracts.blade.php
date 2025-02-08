@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Manage Contracts</h6>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div class="mb-3 row">
                <div class="col-12">
                    <form action="{{ route('sales_manager.manage_contracts') }}" method="GET" class="row g-3">
                        <div class="col-md-2">
                            <input type="text" name="contract_number" class="form-control" 
                                placeholder="Contract Number" value="{{ request('contract_number') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="customer" class="form-control" 
                                placeholder="Customer Name" value="{{ request('customer') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="sales_agent" class="form-select">
                                <option value="">Select Sales Agent</option>
                                @foreach($salesAgents as $agent)
                                    <option value="{{ $agent->id }}" {{ request('sales_agent') == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Select Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date" class="form-control" 
                                placeholder="Date" value="{{ request('date') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary me-2">Search</button>
                            @if(request()->hasAny(['contract_number', 'customer', 'sales_agent', 'status', 'date']))
                                <a href="{{ route('sales_manager.manage_contracts') }}" class="btn btn-secondary">Clear</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Contract Number</th>
                            <th>Client</th>
                            <th>Sales Agent</th>
                            <th>Contract Value</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                        <tr>
                            <td>{{ $contract->contract_number }}</td>
                            <td>{{ $contract->customer->name }}</td>
                            <td>{{ $contract->salesRepresentative->name }}</td>
                            <td>{{ number_format($contract->contract_price, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $contract->contract_status == 'approved' ? 'success' : 'warning' }}">
                                    {{ ucfirst($contract->contract_status) }}
                                </span>
                            </td>
                            <td>{{ $contract->created_at ? date('Y-m-d', strtotime($contract->created_at)) : 'Not set' }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                    data-bs-target="#transferModal{{ $contract->id }}">
                                    Transfer
                                </button>
                                <form action="{{ route('sales_manager.delete_contract', $contract->id) }}" 
                                    method="POST" class="d-inline" 
                                    onsubmit="return confirm('Are you sure you want to delete this contract?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Transfer Modal -->
                        <div class="modal fade" id="transferModal{{ $contract->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Transfer Contract</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('sales_manager.transfer_contract', $contract->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Select New Sales Agent</label>
                                                <select class="form-select" name="new_agent_id" required>
                                                    <option value="">Select Sales Agent</option>
                                                    @foreach($salesAgents as $agent)
                                                        @if($agent->id != $contract->sales_id)
                                                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Transfer Reason</label>
                                                <textarea class="form-control" name="reason" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Transfer Contract</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No contracts found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
