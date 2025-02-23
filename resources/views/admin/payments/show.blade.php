@extends('shared.dashboard')

@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">Payments</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">All Payments</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Payment Details</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4 d-flex align-items-center">
                        <div>
                            <h5 class="mb-0">Payment Details</h5>
                            <p class="mb-0 text-secondary">Invoice # <a href="{{ route('payment.show', $payment->id) }}" target="_blank">{{ $payment->invoice_number }}</a></p>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-{{ $payment->payment_status === 'paid' ? 'success' : ($payment->payment_status === 'pending' ? 'warning' : 'danger') }} fs-6">
                                {{ ucfirst($payment->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label text-secondary">Amount</label>
                            <h6>{{ number_format($payment->payment_amount, 2) }}</h6>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label text-secondary">Due Date</label>
                            <h6>{{ $payment->due_date instanceof \Carbon\Carbon ? $payment->due_date->format('Y-m-d') : $payment->due_date }}</h6>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label text-secondary">Payment Method</label>
                            <h6>{{ ucfirst($payment->payment_method) }}</h6>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label text-secondary">Paid At</label>
                            <h6>{{ $payment->paid_at ? ($payment->paid_at instanceof \Carbon\Carbon ? $payment->paid_at->format('Y-m-d H:i:s') : $payment->paid_at) : 'Not paid yet' }}</h6>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-secondary">Description</label>
                            <p class="mb-0">{{ $payment->payment_description ?: 'No description available' }}</p>
                        </div>
                    </div>

                    @if($payment->payment_status !== 'paid')
                    <div class="mt-4">
                        <form action="{{ route('admin.payments.status.update', $payment->id) }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Payment Status</label>
                                    <select name="status" class="form-select" id="paymentStatus">
                                        <option value="pending" {{ $payment->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid">Paid</option>
                                        <option value="overdue" {{ $payment->payment_status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Payment Method</label>
                                    <select name="payment_method" class="form-select" id="paymentMethod" disabled>
                                        <option value="">Select Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank transfer">Bank Transfer</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Paid At</label>
                                    <input type="datetime-local" name="paid_at" class="form-control" id="paidAt" value="{{ now()->format('Y-m-d\TH:i') }}" disabled>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="mb-3 card">
                <div class="card-body">
                    <h5 class="mb-3">Customer Information</h5>
                    <div class="customer-info">
                        <p class="mb-1"><strong>Name:</strong> {{ $payment->customer->name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $payment->customer->email }}</p>
                        <p class="mb-1"><strong>Phone:</strong> {{ $payment->customer->phone }}</p>
                        <p class="mb-0"><strong>Address:</strong> {{ $payment->customer->address }}</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Contract Information</h5>
                    <div class="contract-info">
                        <p class="mb-1">
                            <strong>Contract Number:</strong>
                            <a href="{{ route('admin.contracts.show', $payment->contract->id) }}" class="text-primary">
                                #{{ $payment->contract->contract_number }}
                            </a>
                        </p>
                        <p class="mb-1"><strong>Start Date:</strong> {{ $payment->contract->start_date instanceof \Carbon\Carbon ? $payment->contract->start_date->format('Y-m-d') : $payment->contract->start_date }}</p>
                        <p class="mb-1"><strong>End Date:</strong> {{ $payment->contract->end_date instanceof \Carbon\Carbon ? $payment->contract->end_date->format('Y-m-d') : $payment->contract->end_date }}</p>
                        <p class="mb-0"><strong>Status:</strong> {{ ucfirst($payment->contract->status) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($payment->postponementRequests->count() > 0)
    <div class="mt-3 card">
        <div class="card-body">
            <h5 class="mb-3">Postponement Requests</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Requested By</th>
                            <th>Requested Date</th>
                            <th>New Due Date</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payment->postponementRequests as $request)
                        <tr>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->created_at instanceof \Carbon\Carbon ? $request->created_at->format('Y-m-d') : $request->created_at }}</td>
                            <td>{{ $request->new_due_date instanceof \Carbon\Carbon ? $request->new_due_date->format('Y-m-d') : $request->new_due_date }}</td>
                            <td>
                                <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td>{{ $request->created_at instanceof \Carbon\Carbon ? $request->created_at->format('Y-m-d H:i:s') : $request->created_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Handle payment status change
        $('#paymentStatus').on('change', function() {
            const isPaid = $(this).val() === 'paid';
            $('#paymentMethod').prop('disabled', !isPaid);
            $('#paidAt').prop('disabled', !isPaid);
            
            if (isPaid) {
                $('#paymentMethod').prop('required', true);
                $('#paidAt').prop('required', true);
            } else {
                $('#paymentMethod').prop('required', false);
                $('#paidAt').prop('required', false);
            }
        });
    });
</script>
@endpush
