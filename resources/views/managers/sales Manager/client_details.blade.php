@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="row">
        <!-- Client Information -->
        <div class="col-12 col-lg-4">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Client Information</h6>
                        </div>
                    </div>
                    <div class="mt-3 client-info">
                        <div class="mb-3">
                            <label class="fw-bold">Name</label>
                            <p>{{ $client->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Email</label>
                            <p>{{ $client->email }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Phone</label>
                            <p>{{ $client->phone }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Sales Agent</label>
                            <p>{{ $client->sales->name ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Status</label>
                            <p>
                                <span class="badge bg-{{ $client->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($client->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Join Date</label>
                            <p>{{ $client->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Statistics -->
        <div class="col-12 col-lg-8">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Client Statistics</h6>
                        </div>
                    </div>
                    <div class="mt-3 text-center row row-cols-1 row-cols-md-3 row-cols-xl-3 g-0 row-group border-top">
                        <div class="col">
                            <div class="p-3">
                                <h5 class="mb-0">{{ $client->contracts_count }}</h5>
                                <small class="mb-0">Total Contracts</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-3">
                                <h5 class="mb-0">{{ number_format($client->contracts_sum_contract_price ?? 0, 2) }} SAR</h5>
                                <small class="mb-0">Total Contract Value</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-3">
                                <h5 class="mb-0">{{ $client->contracts->where('status', 'active')->count() }}</h5>
                                <small class="mb-0">Active Contracts</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Contracts -->
            <div class="mt-3 card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Client Contracts</h6>
                        </div>
                    </div>
                    <div class="mt-3 table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Contract #</th>
                                    <th>Created Date</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($client->contracts as $contract)
                                <tr>
                                    <td>{{ $contract->contract_number }}</td>
                                    <td>{{ $contract->created_at->format('M d, Y') }}</td>
                                    <td>${{ number_format($contract->contract_price, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $contract->contract_status == 'approved' ? 'success' : 
                                            ($contract->contract_status == 'pending' ? 'warning' : 'danger') 
                                        }}">
                                            {{ ucfirst($contract->contract_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('sales_manager.contract.view', $contract->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bx bx-show"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No contracts found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
