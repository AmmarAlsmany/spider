@extends('shared.dashboard')
@section('title', 'Visit Change Requests')
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
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">Technical Manager</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Visit Change Requests</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <h5 class="mb-0">Visit Change Requests</h5>
            </div>
            <hr>

            @if($pendingVisits->isEmpty())
            <div class="py-5 text-center">
                <i class="bx bx-calendar-check fs-1 text-primary"></i>
                <p class="mt-2">No pending visit change requests</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Client</th>
                            <th>Branch</th>
                            <th>Current Schedule</th>
                            <th>Requested Schedule</th>
                            <th>Contract</th>
                            <th>Visit Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingVisits as $visit)
                        <tr>
                            <td>{{ $visit->contract->customer->name }}</td>
                            <td>
                                @if($visit->branch_id)
                                    {{ $visit->branch->branch_name }}
                                    <small class="d-block text-muted">{{ $visit->branch->branch_address }}</small>
                                @else
                                    Main Location
                                    <small class="d-block text-muted">{{ $visit->contract->customer->address }}</small>
                                @endif
                            </td>
                            <td>
                                <div>Date: {{ $visit->visit_date }}</div>
                                <div>Time: {{ $visit->visit_time }}</div>
                            </td>
                            <td>
                                <div>Date: {{ $visit->changeRequest->visit_date }}</div>
                                <div>Time: {{ $visit->changeRequest->visit_time }}</div>
                            </td>
                            <td>
                                <a href="{{ route('technical.contract.show', $visit->contract_id) }}"
                                    class="text-primary">
                                    View Contract
                                </a>
                            </td>
                            <td>{{ ucfirst($visit->visit_type) }}</td>
                            <td>
                                <div class="gap-2 d-flex">
                                    <form action="{{ route('technical.visit.update', $visit->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="visit_id" value="{{ $visit->id }}">
                                        <input type="hidden" name="visit_date" value="{{ $visit->changeRequest->visit_date }}">
                                        <input type="hidden" name="visit_time" value="{{ $visit->changeRequest->visit_time }}">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bx bx-check"></i> Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('technical.visit.update', $visit->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="visit_id" value="{{ $visit->id }}">
                                        <input type="hidden" name="visit_date" value="{{ $visit->changeRequest->visit_date }}">
                                        <input type="hidden" name="visit_time" value="{{ $visit->changeRequest->visit_time }}">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bx bx-x"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($pendingVisits->hasPages())
            <div class="mt-4">
                {{ $pendingVisits->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection