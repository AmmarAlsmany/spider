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
    <div class="card">
        <div class="card-body">
            <div class="mb-4 d-flex align-items-center">
                <div>
                    <h5 class="mb-0">Contract Details</h5>
                    <p class="mb-0 text-muted">Contract #{{ $contract->contract_number }}</p>
                </div>
                <div class="ms-auto">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i>Back
                    </a>
                </div>
            </div>

            <!-- Contract Information -->
            <div class="row">
                <div class="col-md-6">
                    <div class="border card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Contract Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">Contract Number</th>
                                    <td>{{ $contract->contract_number }}</td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <td>{{ date('M d, Y', strtotime($contract->contract_start_date)) }}</td>
                                </tr>
                                <tr>
                                    <th>End Date</th>
                                    <td>{{ date('M d, Y', strtotime($contract->contract_end_date)) }}</td>
                                </tr>
                                {{-- contract type --}}
                                <tr>
                                    <th>Contract Type</th>
                                    <td>{{ $contract->type->name }}</td>
                                </tr>
                                {{-- contract status --}}
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($contract->contract_status == 'approved')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($contract->contract_status == 'completed')
                                            <span class="badge bg-info">Completed</span>
                                        @elseif($contract->contract_status == 'stopped')
                                            <span class="badge bg-warning">Stopped</span>
                                        @else
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Customer Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">Customer Name</th>
                                    <td>{{ $contract->customer->name }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Number</th>
                                    <td>{{ $contract->customer->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $contract->customer->email }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $contract->customer->address }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Locations -->
            @if($contract->branchs->count() > 0)
            <div class="mt-4 row">
                <div class="col-12">
                    <div class="border card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Service Locations</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Branch Name</th>
                                            <th>Address</th>
                                            <th>Contact Person</th>
                                            <th>Phone</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contract->branchs as $branch)
                                        <tr>
                                            <td>{{ $branch->branch_name }}</td>
                                            <td>{{ $branch->branch_address }}</td>
                                            <td>{{ $branch->branch_manager_name }}</td>
                                            <td>{{ $branch->branch_manager_phone }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Visit Schedule -->
            <div class="mt-4 row">
                <div class="col-12">
                    <div class="border card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Visit Schedule</h6>
                        </div>
                        <div class="card-body">
                            @if($visitSchedules->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Visit Date</th>
                                            <th>Time</th>
                                            <th>Location</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($visitSchedules as $visit)
                                        <tr>
                                            <td>{{ date('M d, Y', strtotime($visit->visit_date)) }}</td>
                                            <td>{{ date('h:i A', strtotime($visit->visit_time)) }}</td>
                                            <td>
                                                @if($visit->branch_id)
                                                    {{ $visit->branch->branch_name }}
                                                    <small class="d-block text-muted">{{ $visit->branch->branch_address }}</small>
                                                @else
                                                    Main Location
                                                @endif
                                            </td>
                                           <td>{{ $contract->type->name }}</td>
                                            <td>
                                                @if($visit->status == 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($visit->status == 'cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($visit->status == 'completed')
                                                    <a href="{{ route('technical.visit.report.view', $visit->id) }}" class="btn btn-sm btn-primary"><i class="bx bx-file me-1"></i> View Report</a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    </tbody>
                                </table>
                                <div class="mt-3 d-flex justify-content-end">
                                    {{ $visitSchedules->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                            @else
                            <div class="py-4 text-center">
                                <i class="bx bx-calendar-x text-muted" style="font-size: 4rem;"></i>
                                <p class="mt-2 text-muted">No visits scheduled</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
