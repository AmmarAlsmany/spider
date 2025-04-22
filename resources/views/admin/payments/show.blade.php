@extends('shared.dashboard')

@section('content')
    <div class="page-content">
        <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
            <div class="breadcrumb-title pe-3">{{ __('admin.payments.title') }}</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.payments.index') }}">{{ __('admin.payments.all_payments') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('admin.payments.details.title') }}</li>
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
                                <h5 class="mb-0">{{ __('admin.payments.details.title') }}</h5>
                                <p class="mb-0 text-secondary">{{ __('admin.payments.details.invoice') }} <a
                                        href="{{ route('payment.show', $payment->id) }}"
                                        target="_blank">{{ $payment->invoice_number }}</a></p>
                            </div>
                            <div class="ms-auto">
                                <span
                                    class="badge bg-{{ $payment->payment_status === 'paid' ? 'success' : ($payment->payment_status === 'pending' ? 'warning' : 'danger') }} fs-6">
                                    {{ ucfirst($payment->payment_status) }}
                                </span>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label text-secondary">{{ __('admin.payments.details.amount') }}</label>
                                <h6>{{ number_format($payment->payment_amount, 2) }}</h6>
                            </div>
                            <div class="col-12 col-md-6">
                                <label
                                    class="form-label text-secondary">{{ __('admin.payments.details.due_date') }}</label>
                                <h6>{{ $payment->due_date instanceof \Carbon\Carbon ? $payment->due_date->format('Y-m-d') : $payment->due_date }}
                                </h6>
                            </div>
                            <div class="col-12 col-md-6">
                                <label
                                    class="form-label text-secondary">{{ __('admin.payments.details.payment_method') }}</label>
                                <h6>{{ ucfirst($payment->payment_method ?? 'N/A') }}</h6>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label text-secondary">{{ __('admin.payments.details.paid_at') }}</label>
                                <h6>{{ $payment->paid_at ? ($payment->paid_at instanceof \Carbon\Carbon ? $payment->paid_at->format('Y-m-d H:i:s') : $payment->paid_at) : __('admin.payments.details.not_paid_yet') }}
                                </h6>
                            </div>
                            <div class="col-12">
                                <label
                                    class="form-label text-secondary">{{ __('admin.payments.details.description') }}</label>
                                <p class="mb-0">
                                    {{ $payment->payment_description ?: __('admin.payments.details.no_description') }}</p>
                            </div>
                        </div>

                        {{-- @if ($payment->payment_status !== 'paid')
                            <div class="mt-4">
                                <form action="{{ route('admin.payments.status.update', $payment->id) }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12 col-md-4">
                                            <label
                                                class="form-label">{{ __('admin.payments.details.payment_status') }}</label>
                                            <select name="status" class="form-select" id="paymentStatus">
                                                <option value="pending"
                                                    {{ $payment->payment_status === 'pending' ? 'selected' : '' }}>
                                                    {{ __('admin.payments.table.status.pending') }}</option>
                                                <option value="paid">{{ __('admin.payments.table.status.paid') }}
                                                </option>
                                                <option value="overdue"
                                                    {{ $payment->payment_status === 'overdue' ? 'selected' : '' }}>
                                                    {{ __('admin.payments.table.status.overdue') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label
                                                class="form-label">{{ __('admin.payments.details.payment_method') }}</label>
                                            <select name="payment_method" class="form-select" id="paymentMethod" disabled>
                                                <option value="">{{ __('admin.payments.details.select_method') }}
                                                </option>
                                                <option value="cash">{{ __('admin.payments.details.methods.cash') }}
                                                </option>
                                                <option value="bank transfer">
                                                    {{ __('admin.payments.details.methods.bank_transfer') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="form-label">{{ __('admin.payments.details.paid_at') }}</label>
                                            <input type="datetime-local" name="paid_at" class="form-control" id="paidAt"
                                                value="{{ now()->format('Y-m-d\TH:i') }}" disabled>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit"
                                                class="btn btn-primary">{{ __('admin.payments.details.update_status') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif --}}
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="mb-3 card">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('admin.payments.details.customer_information') }}</h5>
                        <div class="customer-info">
                            <p class="mb-1"><strong>{{ __('admin.payments.details.customer.name') }}:</strong>
                                {{ $payment->customer->name }}</p>
                            <p class="mb-1"><strong>{{ __('admin.payments.details.customer.email') }}:</strong>
                                {{ $payment->customer->email }}</p>
                            <p class="mb-1"><strong>{{ __('admin.payments.details.customer.phone') }}:</strong>
                                {{ $payment->customer->phone }}</p>
                            <p class="mb-0"><strong>{{ __('admin.payments.details.customer.address') }}:</strong>
                                {{ $payment->customer->address }}</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('admin.payments.details.contract_information') }}</h5>
                        <div class="contract-info">
                            <p class="mb-1">
                                <strong>{{ __('admin.payments.details.contract.number') }}:</strong>
                                <a href="{{ route('admin.contracts.show', $payment->contract->id) }}" class="text-primary">
                                    #{{ $payment->contract->contract_number }}
                                </a>
                            </p>
                            <p class="mb-1"><strong>{{ __('admin.payments.details.contract.start_date') }}:</strong>
                                {{ $payment->contract->start_date instanceof \Carbon\Carbon ? $payment->contract->start_date->format('Y-m-d') : $payment->contract->start_date }}
                            </p>
                            <p class="mb-1"><strong>{{ __('admin.payments.details.contract.end_date') }}:</strong>
                                {{ $payment->contract->end_date instanceof \Carbon\Carbon ? $payment->contract->end_date->format('Y-m-d') : $payment->contract->end_date }}
                            </p>
                            <p class="mb-0"><strong>{{ __('admin.payments.details.contract.status') }}:</strong>
                                {{ ucfirst($payment->contract->status) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($payment->postponementRequests->count() > 0)
            <div class="mt-3 card">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('admin.payments.details.postponement_requests') }}</h5>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('admin.payments.details.postponement.requested_by') }}</th>
                                    <th>{{ __('admin.payments.details.postponement.requested_date') }}</th>
                                    <th>{{ __('admin.payments.details.postponement.new_due_date') }}</th>
                                    <th>{{ __('admin.payments.details.postponement.status') }}</th>
                                    <th>{{ __('admin.payments.details.postponement.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payment->postponementRequests as $request)
                                    <tr>
                                        <td>{{ $request->user->name }}</td>
                                        <td>{{ $request->created_at instanceof \Carbon\Carbon ? $request->created_at->format('Y-m-d') : $request->created_at }}
                                        </td>
                                        <td>{{ $request->new_due_date instanceof \Carbon\Carbon ? $request->new_due_date->format('Y-m-d') : $request->new_due_date }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at instanceof \Carbon\Carbon ? $request->created_at->format('Y-m-d H:i:s') : $request->created_at }}
                                        </td>
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
