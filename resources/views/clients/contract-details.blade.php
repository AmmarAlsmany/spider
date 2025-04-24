@extends('shared.dashboard')
@section('content')
    <div class="page-content">
        @if (session('error'))
            <div class="mb-3 alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-1"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-3 alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
            <div class="breadcrumb-title pe-3">{{ __('clients.contract_details.contract_details') }}</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}"><i
                                    class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('client.show') }}">{{ __('clients.contracts.my_contracts') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ __('clients.contract_details.contract_details') }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Contract Status Card -->
            <div class="mb-4 col-12">
                <div
                    class="card border-0 border-start border-4 border-{{ $contract->contract_status == 'pending'
                        ? 'warning'
                        : ($contract->contract_status == 'approved'
                            ? 'success'
                            : ($contract->contract_status == 'Not approved'
                                ? 'danger'
                                : ($contract->contract_status == 'active'
                                    ? 'primary'
                                    : ($contract->contract_status == 'completed'
                                        ? 'info'
                                        : 'secondary')))) }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{ __('clients.contract_details.status') }}</p>
                                <h4 class="my-1">{{ __('clients.status.' . strtolower($contract->contract_status)) }}</h4>
                                <p class="mb-0 font-13">{{ __('clients.contract_details.contract_number') }}
                                    {{ $contract->contract_number }}</p>
                            </div>
                            <div
                                class="widgets-icons-2 bg-gradient-{{ $contract->contract_status == 'pending'
                                    ? 'warning'
                                    : ($contract->contract_status == 'approved'
                                        ? 'success'
                                        : ($contract->contract_status == 'Not approved'
                                            ? 'danger'
                                            : ($contract->contract_status == 'active'
                                                ? 'primary'
                                                : ($contract->contract_status == 'completed'
                                                    ? 'info'
                                                    : 'secondary')))) }} text-white ms-auto">
                                <i class="bx bx-file"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Contract Information -->
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="bg-transparent card-header">
                        <h5 class="mb-0">{{ __('clients.contract_details.contract_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('contract.insect-analytics', $contract->id) }}" class="btn btn-primary">
                                    <i class="bx bx-line-chart"></i> {{ __('clients.contract_details.insect_analytics') }}
                                </a>
                            </div>
                        </div>
                        <div class="mb-4 row">
                            <div class="col-md-6">
                                <h6 class="mb-3 text-secondary">{{ __('contract_details.basic_details.title') }}</h6>
                                <div class="mb-2">
                                    <strong>{{ __('contract_details.basic_details.contract_type') }}:</strong>
                                    <span class="ms-2">{{ $contract->type ? $contract->type->name : 'N/A' }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>{{ __('contract_details.basic_details.property_type') }}:</strong>
                                    <span class="ms-2">{{ $contract->Property_type }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>{{ __('contract_details.basic_details.warranty') }}:</strong>
                                    <span
                                        class="ms-2">{{ __('contract_details.basic_details.warranty_months', ['months' => $contract->warranty]) }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>{{ __('contract_details.basic_details.number_of_visits') }}:</strong>
                                    <span
                                        class="ms-2">{{ __('contract_details.basic_details.visits_count', ['count' => $contract->number_of_visits]) }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>{{ __('contract_details.basic_details.start_date') }}:</strong>
                                    <span
                                        class="ms-2">{{ \Carbon\Carbon::parse($contract->contract_start_date)->format('M d, Y') }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>{{ __('contract_details.basic_details.end_date') }}:</strong>
                                    <span
                                        class="ms-2">{{ \Carbon\Carbon::parse($contract->contract_end_date)->format('M d, Y') }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>{{ __('contract_details.basic_details.multiple_branches') }}:</strong>
                                    <span
                                        class="ms-2">{{ $contract->is_multi_branch === 'yes' ? __('contract_details.basic_details.yes') : __('contract_details.basic_details.no') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3 text-secondary">{{ __('contract_details.financial_information.title') }}
                                </h6>
                                <div class="mb-2">
                                    <strong>{{ __('contract_details.financial_information.contract_price') }}:</strong>
                                    <span class="ms-2">{{ number_format($contract->contract_price, 2) }} SAR</span>
                                </div>
                                <div class="mb-2">
                                    <strong>{{ __('contract_details.financial_information.payment_type') }}:</strong>
                                    <span class="ms-2">{{ $contract->Payment_type }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>{{ __('contract_details.financial_information.number_of_payments') }}:</strong>
                                    <span
                                        class="ms-2">{{ $contract->number_Payments ? $contract->number_Payments : '1' }}</span>
                                </div>
                            </div>
                        </div>

                        @if ($contract->branchs && $contract->branchs->count() > 0)
                            <div class="mb-4 row">
                                <div class="col-12">
                                    <h6 class="mb-3 text-secondary">{{ __('contract_details.branch_information.title') }}
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('contract_details.branch_information.branch_name') }}</th>
                                                    <th>{{ __('contract_details.branch_information.location') }}</th>
                                                    <th>{{ __('contract_details.branch_information.city') }}</th>
                                                    <th>{{ __('contract_details.branch_information.contact') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($contract->branchs as $branch)
                                                    <tr>
                                                        <td>{{ $branch->branch_name }}</td>
                                                        <td> <a href="google.com/maps/search/?api=1&query={{ urlencode($branch->branch_address) }}"
                                                                target="_blank">{{ $branch->branch_address }}</a></td>
                                                        <td>{{ $branch->branch_city }}</td>
                                                        <td>
                                                            <strong>{{ __('contract_details.branch_information.manager') }}:</strong>
                                                            {{ $branch->branch_manager_name }}<br>
                                                            <strong>{{ __('contract_details.branch_information.phone') }}:</strong>
                                                            <a
                                                                href="tel:{{ $branch->branch_manager_phone }}">{{ $branch->branch_manager_phone }}</a><br>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Payment Information -->
                        <div class="mb-4 row">
                            <div class="col-12">
                                <h6 class="mb-3 text-secondary">{{ __('contract_details.payment_information.title') }}</h6>
                                @if ($contract->payments && $contract->payments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('contract_details.payment_information.payment_date') }}</th>
                                                    <th>{{ __('contract_details.payment_information.amount') }}</th>
                                                    <th>{{ __('contract_details.payment_information.method') }}</th>
                                                    <th>{{ __('contract_details.payment_information.status') }}</th>
                                                    <th>{{ __('contract_details.payment_information.paid_at') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($contract->payments as $payment)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}
                                                        </td>
                                                        <td>{{ number_format($payment->payment_amount, 2) }} SAR</td>
                                                        <td>{{ $payment->payment_method }}</td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $payment->payment_status == 'paid'
                                                                    ? 'success'
                                                                    : ($payment->payment_status == 'pending'
                                                                        ? 'warning'
                                                                        : ($payment->payment_status == 'overdue'
                                                                            ? 'danger'
                                                                            : 'secondary')) }}">
                                                                {{ ucfirst($payment->payment_status) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y H:i A') : '-' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light">
                                                    <td colspan="1">
                                                        <strong>{{ __('contract_details.payment_information.summary') }}</strong>
                                                    </td>
                                                    <td><strong>{{ number_format($contract->payments->sum('payment_amount'), 2) }}
                                                            SAR</strong></td>
                                                    <td colspan="3">
                                                        <div class="d-flex justify-content-between">
                                                            <span>
                                                                <i class="bx bx-check text-success"></i>
                                                                {{ __('contract_details.payment_information.paid') }}:
                                                                {{ number_format($contract->payments->where('payment_status', 'paid')->sum('payment_amount'), 2) }}
                                                                SAR
                                                            </span>
                                                            <span>
                                                                <i class="bx bx-time text-warning"></i>
                                                                {{ __('contract_details.payment_information.pending') }}:
                                                                {{ number_format($contract->payments->where('payment_status', 'unpaid')->sum('payment_amount'), 2) }}
                                                                SAR
                                                            </span>
                                                            <span>
                                                                <i class="bx bx-x text-danger"></i>
                                                                {{ __('contract_details.payment_information.overdue') }}:
                                                                {{ number_format($contract->payments->where('payment_status', 'overdue')->sum('payment_amount'), 2) }}
                                                                SAR
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <!-- Payment Progress -->
                                    <div class="mt-3">
                                        @php
                                            $totalAmount = $contract->payments->sum('payment_amount');
                                            $paidAmount = $contract->payments
                                                ->where('payment_status', 'paid')
                                                ->sum('payment_amount');
                                            $progressPercentage =
                                                $totalAmount > 0 ? ($paidAmount / $totalAmount) * 100 : 0;
                                        @endphp
                                        <h6 class="mb-2">{{ __('contract_details.payment_progress.title') }}</h6>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $progressPercentage }}%;"
                                                aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                                {{ number_format($progressPercentage, 1) }}%
                                            </div>
                                        </div>
                                        <div class="mt-2 text-muted small">
                                            {{ number_format($paidAmount, 2) }} SAR
                                            {{ __('contract_details.payment_progress.paid_out_of') }}
                                            {{ number_format($totalAmount, 2) }} SAR
                                        </div>
                                    </div>
                                @else
                                    <div class="py-4 text-center">
                                        <i class="bx bx-money text-muted" style="font-size: 48px;"></i>
                                        <p class="mt-2">
                                            {{ __('contract_details.payment_information.no_payment_records') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3 text-secondary">{{ __('contract_details.contract_description.title') }}
                                </h6>
                                <p class="text-muted">
                                    {{ $contract->contract_description ?: __('contract_details.contract_description.no_description') }}
                                </p>
                                @if ($contract->updateRequests->count() > 0)
                                    @php
                                        $latestRequest = $contract->updateRequests->first();
                                        $alertClass =
                                            $latestRequest->status === null
                                                ? 'info'
                                                : ($latestRequest->status === 'approved'
                                                    ? 'success'
                                                    : 'danger');
                                    @endphp
                                    <div class="alert alert-{{ $alertClass }} d-flex align-items-center mt-3"
                                        role="alert">
                                        <i class="bx bx-info-circle me-2" style="font-size: 1.2rem;"></i>
                                        <div>
                                            @if ($latestRequest->status === null)
                                                {{ __('contract_details.contract_description.update_request_pending') }}
                                            @else
                                                {{ __('contract_details.contract_description.update_request_status', ['status' => ucfirst($latestRequest->status)]) }}
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div class="col-12 col-lg-4">
                <!-- Status Actions -->
                <div class="mb-4 card">
                    <div class="bg-transparent card-header">
                        <h5 class="mb-0">{{ __('contract_details.actions.title') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($contract->contract_status == 'pending')
                            <div class="gap-2 d-grid">
                                <form action="{{ route('client.contract.approve', $contract->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="mb-2 btn btn-success w-100"
                                        {{ $contract->updateRequests->count() > 0 && $contract->updateRequests->first()->response === null ? 'disabled' : '' }}>
                                        <i class="bx bx-check me-1"></i>
                                        {{ __('contract_details.actions.approve_contract') }}
                                    </button>
                                </form>
                                <button type="button" class="mb-2 btn btn-danger w-100" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal{{ $contract->id }}"
                                    {{ $contract->updateRequests->count() > 0 && $contract->updateRequests->first()->response === null ? 'disabled' : '' }}>
                                    <i class="bx bx-x me-1"></i> {{ __('contract_details.actions.not_approve_contract') }}
                                </button>
                                <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal"
                                    data-bs-target="#updateRequestModal{{ $contract->id }}">
                                    <i class="bx bx-edit me-1"></i> {{ __('contract_details.actions.request_changes') }}
                                </button>
                            </div>
                        @elseif($contract->contract_status == 'approved')
                            <div class="gap-2 d-grid">
                                <button type="button" class="mb-2 btn btn-primary w-100" data-bs-toggle="modal"
                                    data-bs-target="#updateRequestModal{{ $contract->id }}">
                                    <i class="bx bx-edit me-1"></i>
                                    {{ __('contract_details.actions.request_modification') }}
                                </button>
                                <a href="{{ route('client.show.payment.details', $contract->id) }}"
                                    class="btn btn-info w-100">
                                    <i class="bx bx-money me-1"></i> {{ __('contract_details.actions.view_payments') }}
                                </a>
                            </div>
                        @endif

                        <div class="gap-2 mt-3 d-grid">
                            <button type="button" class="mb-2 btn btn-secondary w-100" data-bs-toggle="modal"
                                data-bs-target="#historyModal{{ $contract->id }}">
                                <i class="bx bx-history me-1"></i> {{ __('contract_details.actions.view_history') }}
                            </button>
                            <a href="{{ route('contract.pdf.generate', $contract->id) }}" class="btn btn-dark w-100">
                                <i class="bx bx-download me-1"></i>
                                {{ __('contract_details.actions.download_contract') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sales Representative Info -->
                @if ($contract->salesRepresentative)
                    <div class="mb-4 card">
                        <div class="bg-transparent card-header">
                            <h5 class="mb-0">{{ __('contract_details.sales_representative.title') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-2 rounded-circle bg-light">
                                        <i class="bx bx-user"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $contract->salesRepresentative->name }}</h6>
                                    <p class="mb-0 text-muted small">
                                        {{ __('contract_details.sales_representative.sales_agent') }}</p>
                                </div>
                            </div>
                            @if ($contract->salesRepresentative->email)
                                <div class="mb-2">
                                    <strong><i class="bx bx-envelope me-1"></i>
                                        {{ __('contract_details.sales_representative.email') }}:</strong>
                                    <a href="mailto:{{ $contract->salesRepresentative->email }}"><span
                                            class="ms-2">{{ $contract->salesRepresentative->email }}</span></a>
                                </div>
                            @endif
                            @if ($contract->salesRepresentative->phone)
                                <div class="mb-2">
                                    <strong><i class="bx bx-phone me-1"></i>
                                        {{ __('contract_details.sales_representative.phone') }}:</strong>
                                    <a href="tel:{{ $contract->salesRepresentative->phone }}"><span
                                            class="ms-2">{{ $contract->salesRepresentative->phone }}</span></a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('clients.partials.contract-modals')
@endsection

@push('styles')
    <style>
        .widgets-icons-2 {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 20px;
        }

        .bg-gradient-warning {
            background: linear-gradient(to right, #f7b731, #f5a623);
        }

        .bg-gradient-success {
            background: linear-gradient(to right, #28a745, #20c997);
        }

        .bg-gradient-danger {
            background: linear-gradient(to right, #dc3545, #f86c6b);
        }

        .bg-gradient-primary {
            background: linear-gradient(to right, #007bff, #1e88e5);
        }

        .bg-gradient-info {
            background: linear-gradient(to right, #17a2b8, #00b8d4);
        }

        .bg-gradient-secondary {
            background: linear-gradient(to right, #6c757d, #868e96);
        }
    </style>
@endpush
