@extends('shared.dashboard')
@section('content')
<div class="page-content">
    @if(session('error'))
    <div class="mb-3 alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bx bx-error-circle me-1"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-3 alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-1"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Manage Clients</h6>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Search Form -->
            <div class="mb-3 row">
                <div class="col-12">
                    <form action="{{ route('sales_manager.manage_clients') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" 
                                placeholder="Search by name, email or phone" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="sales_agent" class="form-select">
                                <option value="">All Sales Agents</option>
                                @foreach($salesAgents as $agent)
                                    <option value="{{ $agent->id }}" {{ request('sales_agent') == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">Join Date</span>
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary me-2">Search</button>
                            @if(request()->hasAny(['search', 'sales_agent', 'status', 'date']))
                                <a href="{{ route('sales_manager.manage_clients') }}" class="btn btn-secondary">Clear</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Clients Table -->
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Sales Agent</th>
                            <th>Total Contracts</th>
                            <th>Total Value</th>
                            <th>Join Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->phone }}</td>
                            <td>{{ $client->sales->name ?? 'N/A' }}</td>
                            <td>{{ $client->contracts_count }}</td>
                            <td>${{ number_format($client->contracts_sum_contract_price ?? 0, 2) }}</td>
                            <td>{{ $client->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $client->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($client->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="gap-2 d-flex">
                                    <a href="{{ route('sales_manager.client.details', $client->id) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bx bx-show"></i> View
                                    </a>
                                    <a href="{{ route('sales.clients.edit', $client->id) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="bx bx-edit-alt"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No clients found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $clients->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
