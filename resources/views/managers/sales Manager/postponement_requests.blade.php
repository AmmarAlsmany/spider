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
    <div class="mb-4 page-breadcrumb d-flex align-items-center">
        <div class="pe-3 breadcrumb-title d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
                <i class="bx bx-arrow-back"></i> Back
            </a>
            <h4 class="mb-0 text-primary"><i class="bx bx-time"></i> Payment Postponement Requests</h4>
        </div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Postponement Requests</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example2" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Contract Number</th>
                            <th>Payment Amount</th>
                            <th>Current Date</th>
                            <th>Requested Date</th>
                            <th>Request Reason</th>
                            <th>Request Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                        <tr>
                            <td>
                                <a href="{{ route('sales_manager.client.details', ['id' => $request->payment->customer->id]) }}"
                                    class="text-primary text-decoration-none">
                                    <i class="bi bi-person me-2"></i>{{ $request->payment->customer->name }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('sales_manager.contract.view', ['id' => $request->payment->contract->id]) }}"
                                    class="text-primary text-decoration-none">
                                    <i class="bi bi-file-text me-2"></i>{{ $request->payment->contract->contract_number
                                    }}
                                </a>
                            </td>
                            <td>{{ number_format($request->payment->payment_amount, 2) }} SAR</td>
                            <td>{{ \Carbon\Carbon::parse($request->payment->due_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($request->requested_date)->format('M d, Y') }}</td>
                            <td>{{ $request->reason }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                            $request->status == 'approved' ? 'success' : 
                                            ($request->status == 'pending' ? 'warning' : 'danger') 
                                        }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td>
                                @if($request->status == 'pending')
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                    data-bs-target="#approveRequestModal" data-request-id="{{ $request->id }}"
                                    data-payment-date="{{ $request->payment->due_date }}"
                                    data-requested-date="{{ $request->requested_date }}">
                                    <i class="bx bx-check"></i> Approve
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#rejectRequestModal" data-request-id="{{ $request->id }}">
                                    <i class="bx bx-x"></i> Reject
                                </button>
                                @else
                                <span class="text-muted">No actions available</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Approve Request Modal -->
<div class="modal fade" id="approveRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Payment Postponement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('postponement.approve') }}" method="POST">
                @csrf
                <input type="hidden" name="request_id" id="approve_request_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Payment Date</label>
                        <input type="text" class="form-control" id="current_payment_date" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Requested New Date</label>
                        <input type="text" class="form-control" id="requested_payment_date" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Approve Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Request Modal -->
<div class="modal fade" id="rejectRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Payment Postponement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('postponement.reject') }}" method="POST">
                @csrf
                <input type="hidden" name="request_id" id="reject_request_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
            // Handle Approve Modal
            var approveModal = document.getElementById('approveRequestModal');
            approveModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var requestId = button.getAttribute('data-request-id');
                var paymentDate = button.getAttribute('data-payment-date');
                var requestedDate = button.getAttribute('data-requested-date');
                
                document.getElementById('approve_request_id').value = requestId;
                document.getElementById('current_payment_date').value = new Date(paymentDate).toLocaleDateString();
                document.getElementById('requested_payment_date').value = new Date(requestedDate).toLocaleDateString();
            });

            // Handle Reject Modal
            var rejectModal = document.getElementById('rejectRequestModal');
            rejectModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var requestId = button.getAttribute('data-request-id');
                document.getElementById('reject_request_id').value = requestId;
            });
        });
</script>
@endpush
@endsection