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
                    <h6 class="mb-0">Contracts for {{ $agent->name }}</h6>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('sales_manager.manage_agents') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back"></i> Back to Agents
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3 row">
                <div class="col-md-6">
                    <form action="{{ route('sales_manager.agent.contracts', $agent->id) }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" 
                            placeholder="Search by contract number or customer" 
                            value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if(request('search'))
                            <a href="{{ route('sales_manager.agent.contracts', $agent->id) }}" class="btn btn-secondary ms-2">Clear</a>
                        @endif
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Contract Number</th>
                            <th>Customer</th>
                            <th>Start Date</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                        <tr>
                            <td>{{ $contract->contract_number }}</td>
                            <td>{{ $contract->customer ? $contract->customer->name : 'N/A' }}</td>
                            <td>{{ $contract->contract_start_date }}</td>
                            <td>
                                <span class="badge {{ 
                                    $contract->contract_status == 'active' ? 'bg-success' : 
                                    ($contract->contract_status == 'expired' ? 'bg-danger' : 
                                    ($contract->contract_status == 'pending' ? 'bg-warning' : 'bg-info')) 
                                }}">
                                    {{ ucfirst($contract->contract_status) }}
                                </span>
                            </td>
                            <td>{{ $contract->type->name }}</td>
                            <td>${{ number_format($contract->contract_price, 2) }}</td>
                            <td>
                                <a href="{{ route('sales_manager.contract.view', $contract->id) }}" 
                                    class="btn btn-sm btn-primary">
                                    <i class="bx bx-show"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No contracts found for this agent.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
