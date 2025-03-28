@extends('shared.dashboard')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
    <div class="mb-4 page-breadcrumb d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3 fw-bold text-primary">
            {{ __('contract_details_new.page_title') }}
        </div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none"><i
                                class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item">
                        <a href="{{ url('/contracts') }}" class="text-decoration-none">
                            {{ __('contract_details_new.breadcrumb_contracts') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ __('contract_details_new.breadcrumb_details') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="py-4 container-fluid">
        <div class="shadow-sm card">
            <div class="py-3 bg-white card-header">
                <div class="flex-wrap d-flex justify-content-between align-items-start">
                    <div class="mb-3 mb-md-0">
                        <h3 class="mb-0 card-title text-primary">
                            {{ __('contract_details_new.contract_header_number') }} {{$contract->contract_number}}
                        </h3>
                        <p class="mt-1 mb-0 text-muted">
                            {{ __('contract_details_new.contract_header_created') }} {{date('M d, Y',
                            strtotime($contract->created_at))}}
                        </p>
                    </div>
                    <div class="gap-2 d-flex flex-column flex-md-row">
                        @if($contract->contract_status !== 'stopped')
                        <form action="{{ route('contract.stop', $contract->id) }}" method="POST"
                            style="display: inline-block; margin: 0; padding: 0;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger w-100 w-md-auto">
                                <i class="bx bx-stop-circle me-1"></i>
                                {{ __('contract_details_new.button_stop_contract') }}
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('contract.pdf.generate', $contract->id) }}"
                            class="btn btn-success w-100 w-md-auto">
                            <i class="bx bx-download me-1"></i>
                            {{ __('contract_details_new.button_download_pdf') }}
                        </a>
                        <a href="{{ route('contract.edit', $contract->id) }}" class="btn btn-warning w-100 w-md-auto">
                            <i class="bx bx-edit me-1"></i>
                            {{ __('contract_details_new.button_edit_contract') }}
                        </a>
                        {{-- create annex --}}
                        <a href="{{ route('contracts.annex.create', $contract->id) }}"
                            class="btn btn-primary w-100 w-md-auto">
                            <i class="bx bx-plus me-1"></i>
                            {{ __('contract_details_new.button_add_annex') }}
                        </a>
                        {{-- end create annex --}}
                        {{-- view the visit schedule --}}
                        <a href="{{ route('view.contract.visit', $contract->id) }}"
                            class="btn btn-info w-100 w-md-auto">
                            <i class="bx bx-show me-1"></i>
                            {{ __('contract_details_new.button_view_visit') }}
                        </a>
                        {{-- end view the visit schedule --}}
                    </div>
                </div>
            </div>
            <div class="p-3 p-md-4 card-body">
                <div class="row g-3 g-md-4">
                    <div class="col-md-6">
                        <div class="contract-info-card h-100">
                            <h4 class="pb-2 mb-4 border-bottom text-primary">
                                <i class="bx bx-file me-2"></i>
                                {{ __('contract_details_new.contract_info_title') }}
                            </h4>
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.contract_info_number') }}
                                    </th>
                                    <td>{{ $contract->contract_number }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.contract_info_start_date') }}
                                    </th>
                                    <td>{{ $contract->contract_start_date ? date('M d, Y',
                                        strtotime($contract->contract_start_date)) : 'Same as contract start date' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.contract_info_end_date') }}
                                    </th>
                                    <td>{{ $contract->contract_end_date ? date('M d, Y',
                                        strtotime($contract->contract_end_date)) : 'Same as contract start date' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.visit_start_date') }}
                                    </th>
                                    <td>{{ $contract->visit_start_date ? date('M d, Y',
                                        strtotime($contract->visit_start_date)) : 'Same as contract start date' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.contract_info_type') }}
                                    </th>
                                    <td>{{ $contract->type->name }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.contract_info_warranty') }}
                                    </th>
                                    <td>{{ $contract->warranty }} {{ __('contract_details_new.contract_info_months') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.contract_info_visits') }}
                                    </th>
                                    <td>{{ $contract->number_of_visits }} {{
                                        __('contract_details_new.contract_info_visits_count') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.contract_info_amount_without_vat') }}
                                    </th>
                                    <td>{{ number_format($contract->contract_price/1.15, 2) }} SAR</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.vat_amount') }}
                                    </th>
                                    <td>{{ number_format($contract->contract_price * 0.15 / 1.15, 2) }} SAR</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.contract_info_amount_with_vat') }}
                                    </th>
                                    <td>{{ number_format($contract->contract_price, 2) }} SAR</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.contract_info_status') }}
                                    </th>
                                    <td>
                                        <span
                                            class="badge bg-{{ $contract->contract_status == 'approved' ? 'info' : ($contract->contract_status == 'completed' ? 'success' : 'danger')}}">
                                            {{ __('contract_details_new.status_' . $contract->contract_status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="client-info-card h-100">
                            <h4 class="pb-2 mb-4 border-bottom text-primary">
                                <i class="bx bx-user me-2"></i>
                                {{ __('contract_details_new.client_info_title') }}
                            </h4>
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.client_info_name') }}
                                    </th>
                                    <td>{{ $contract->customer->name }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.client_info_email') }}
                                    </th>
                                    <td><a href="mailto:{{ $contract->customer->email }}">{{ $contract->customer->email
                                            }}</a></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.client_info_phone') }}
                                    </th>
                                    <td><a href="tel:{{ $contract->customer->phone }}">{{ $contract->customer->phone
                                            }}</a></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.client_info_address') }}
                                    </th>
                                    <td><a href="https://www.google.com/maps/search/?api=1&query={{ $contract->customer->address }}"
                                            target="_blank">{{ $contract->customer->address }}</a></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    {{-- if the contract type is Buy equipment we need to show the equipment info --}}
                    @if ($contract->type->name == 'Buy equipment')
                    <div class="col-md-6">
                        <div class="equipment-info-card h-100">
                            <h4 class="pb-2 mb-4 border-bottom text-primary">
                                <i class="bx bx-info-circle me-2"></i>
                                {{ __('contract_details_new.equipment_info_title') }}
                            </h4>
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.equipment_info_name') }}
                                    </th>
                                    <td>{{ $contract->equipment && $contract->equipment->equipmentType ? $contract->equipment->equipmentType->name : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.equipment_info_description') }}
                                    </th>
                                    <td>{{ $contract->equipment ? $contract->equipment->equipment_model : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.equipment_info_quantity') }}
                                    </th>
                                    <td>{{ $contract->equipment ? $contract->equipment->equipment_quantity : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.equipment_unit_price') }}
                                    </th>
                                    <td>{{ $contract->equipment ? number_format($contract->equipment->unit_price, 2) : 'N/A' }} SAR</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.equipment_info_total_price') }}
                                    </th>
                                    <td>{{ $contract->equipment ? number_format($contract->equipment->total_price, 2) : 'N/A' }} SAR</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.equipment_info_vat_amount') }}
                                    </th>
                                    <td>{{ $contract->equipment ? number_format($contract->equipment->vat_amount, 2) : 'N/A' }} SAR</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        {{ __('contract_details_new.equipment_info_total_amount') }}
                                    </th>
                                    <td>{{ $contract->equipment ? number_format($contract->equipment->total_with_vat, 2) : 'N/A' }} SAR</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Contract Description Section -->
                <div class="mt-4 row">
                    <div class="col-md-12">
                        <div class="contract-info-card h-100">
                            <h4 class="pb-2 mb-4 border-bottom text-primary">
                                <i class="bx bx-info-circle me-2"></i>
                                {{ __('contract_details_new.contract_info_description') }}
                            </h4>
                            <div class="p-3">
                                <p class="mb-0">{{ $contract->contract_description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($contract->branchs->count() > 0 )
                <div class="mt-4 row">
                    <div class="col-md-12">
                        <h4 class="pb-2 mb-4 border-bottom text-primary">
                            <i class="bx bx-buildings me-2"></i>
                            {{ __('contract_details_new.branch_info_title') }}
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            {{ __('contract_details_new.branch_info_branch_name') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.branch_info_branch_manager') }}
                                        </th>
                                        <th class="d-none d-md-table-cell">
                                            {{ __('contract_details_new.branch_info_branch_phone') }}
                                        </th>
                                        <th class="d-none d-md-table-cell">
                                            {{ __('contract_details_new.branch_info_branch_address') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.branch_info_actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contract->branchs as $branch)
                                    <tr>
                                        <td>{{ $branch->branch_name }}</td>
                                        <td>{{ $branch->branch_manager_name }}</td>
                                        <td class="d-none d-md-table-cell"><a
                                                href="tel:{{ $branch->branch_manager_phone }}" target="_blank">{{
                                                $branch->branch_manager_phone }}</a></td>
                                        <td class="d-none d-md-table-cell"><a
                                                href="https://www.google.com/maps/search/?api=1&query={{ $branch->branch_address }}"
                                                target="_blank">{{ $branch->branch_address }}</a></td>
                                        <td>
                                            <div class="gap-2 d-flex">
                                                <button class="btn btn-sm btn-primary"
                                                    onclick="editBranch('{{ $branch->id }}')">
                                                    <i class="bx bx-edit-alt"></i>
                                                    {{ __('contract_details_new.button_edit') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-4 row">
                    <div class="col-md-12">
                        <h4 class="pb-2 mb-4 border-bottom text-primary">
                            <i class="bx bx-credit-card me-2"></i>
                            {{ __('contract_details_new.payment_info_title') }}
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>
                                            {{ __('contract_details_new.payment_info_invoice') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.payment_info_amount') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.payment_info_due_date') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.payment_info_status') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.payment_info_actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contract->payments as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payment->invoice_number }}</td>
                                        <td>{{ number_format($payment->payment_amount, 2) }} SAR</td>
                                        <td>{{ $payment->due_date }}</td>
                                        <td>
                                            <span
                                                class="badge rounded-pill bg-{{ $payment->payment_status == 'paid' ? 'success' : 'warning' }} px-3 py-2">
                                                {{ __('contract_details_new.status_' . $payment->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="flex-wrap gap-2 d-flex justify-content-center">
                                                @if($payment->payment_status != 'paid')
                                                <button class="gap-1 btn btn-sm btn-success d-flex align-items-center"
                                                    onclick="markAsPaid({{ $payment->id }})">
                                                    <i class="bx bx-check"></i> <span class="d-none d-sm-inline">
                                                        {{ __('contract_details_new.button_mark_paid') }}
                                                    </span>
                                                </button>
                                                @endif
                                                <button class="gap-1 btn btn-sm btn-info d-flex align-items-center"
                                                    onclick="viewPaymentDetails({{ $payment->id }})">
                                                    <i class="bx bx-detail"></i> <span class="d-none d-sm-inline">
                                                        {{ __('contract_details_new.button_view_payment_details') }}
                                                    </span>
                                                </button>
                                                {{-- view the invoice --}}

                                                <button class="gap-1 btn btn-sm btn-warning d-flex align-items-center"
                                                    onclick="viewInvoice({{ $payment->id }})">
                                                    <i class="bx bx-receipt"></i> <span class="d-none d-sm-inline">
                                                        {{ __('contract_details_new.button_view_invoice') }}
                                                    </span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4 row">
                    <div class="col-md-12">
                        <h4 class="pb-2 mb-4 border-bottom text-primary">
                            <i class="bx bx-time me-2"></i>
                            {{ __('contract_details_new.postponement_title') }}
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>
                                            {{ __('contract_details_new.postponement_payment_date') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.postponement_amount') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.postponement_requested_date') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.postponement_status') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.postponement_response') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.postponement_responded_at') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($contract->payments->flatMap->postponementRequests as $request)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($request->payment->due_date)->format('M d, Y') }}
                                        </td>
                                        <td>{{ number_format($request->payment->payment_amount, 2) }} SAR</td>
                                        <td>{{ \Carbon\Carbon::parse($request->requested_date)->format('M d, Y') }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ 
                                                    $request->status == 'approved' ? 'success' : 
                                                    ($request->status == 'pending' ? 'warning' : 'danger') 
                                                }} px-3 py-2">
                                                {{ __('contract_details_new.status_' . $request->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $request->response ?? '-' }}</td>
                                        <td>{{ $request->approved_at ?
                                            \Carbon\Carbon::parse($request->approved_at)->format('M d, Y H:i') : '-' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            {{ __('contract_details_new.postponement_no_requests') }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4 row">
                    <div class="col-md-12">
                        <h4 class="pb-2 mb-4 border-bottom text-primary">
                            <i class="bx bx-edit me-2"></i>
                            {{ __('contract_details_new.change_requests_title') }}
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>
                                            {{ __('contract_details_new.change_requests_date') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.change_requests_client') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.change_requests_details') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.change_requests_status') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.change_requests_actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($contract->updateRequests as $request)
                                    <tr>
                                        <td>{{ date('M d, Y', strtotime($request->created_at)) }}</td>
                                        <td>{{ $request->client->name }}</td>
                                        <td>{{ $request->request_details }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $request->status == 'pending' ? 'warning' : ($request->status == 'approved' ? 'success' : 'danger') }}">
                                                {{ __('contract_details_new.status_' . $request->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($request->status == 'pending' && auth()->user()->id ==
                                            $contract->sales_id)
                                            <button class="btn btn-sm btn-success"
                                                onclick="approveRequest({{ $request->id }})">
                                                <i class="bx bx-check"></i>
                                                {{ __('contract_details_new.button_approve') }}
                                            </button>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="rejectRequest({{ $request->id }})">
                                                <i class="bx bx-x"></i>
                                                {{ __('contract_details_new.button_reject') }}
                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            {{ __('contract_details_new.change_requests_no_requests') }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Contract Annexes Section -->
                <div class="mt-4 row">
                    <div class="col-md-12">
                        <h4 class="pb-2 mb-4 border-bottom text-primary">
                            <i class="bx bx-file-plus me-2"></i>
                            {{ __('contract_details_new.annexes_title') }}
                        </h4>
                        <div class="table-responsive">
                            @if($contract->annexes->count() > 0)
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            {{ __('contract_details_new.annexes_number') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.annexes_date') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.annexes_amount') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.annexes_status') }}
                                        </th>
                                        <th>
                                            {{ __('contract_details_new.annexes_actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contract->annexes as $annex)
                                    <tr>
                                        <td>{{ $annex->annex_number }}</td>
                                        <td>{{ $annex->annex_date->format('M d, Y') }}</td>
                                        <td>
                                            {{ number_format($annex->additional_amount, 2) }} SAR
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $annex->status === 'approved' ? 'success' : ($annex->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ __('contract_details_new.status_' . $annex->status) }}
                                            </span>
                                        </td>
                                        @if($annex->status === 'pending')
                                        <td>
                                            <form
                                                action="{{ route('contracts.annex.destroy', ['contract' => $contract->id, 'annex' => $annex->id]) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('{{ __('contract_details_new.annexes_confirm_delete_annex') }}')">
                                                    <i class="bx bx-trash"></i>
                                                    {{ __('contract_details_new.button_delete_annex') }}
                                                </button>
                                            </form>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <div class="py-4 text-center">
                                <p class="mb-0 text-muted">
                                    {{ __('contract_details_new.annexes_no_annexes') }}
                                </p>
                                @if($contract->contract_status === 'approved')
                                <a href="{{ route('contracts.annex.create', $contract->id) }}"
                                    class="mt-3 btn btn-primary">
                                    <i class="bx bx-plus-circle me-1"></i>
                                    {{ __('contract_details_new.button_add_annex') }}
                                </a>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    // Setup Axios CSRF token
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
    // Configure Toastr options
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }

    function editBranch(branchId) {
        fetch(`/api/branches/${branchId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('branchId').value = data.branch.id;
                    document.getElementById('branchName').value = data.branch.branch_name;
                    document.getElementById('branchManagerName').value = data.branch.branch_manager_name;
                    document.getElementById('branchManagerPhone').value = data.branch.branch_manager_phone;
                    document.getElementById('branchAddress').value = data.branch.branch_address;
                    
                    new bootstrap.Modal(document.getElementById('editBranchModal')).show();
                } else {
                    alert(data.message || '{{ __("contract_details_new.error_loading_branch") }}');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __("contract_details_new.error_loading_branch") }}');
            });
    }

    function submitEditBranch() {
        const form = document.getElementById('editBranchForm');
        const formData = new FormData(form);
        const branchId = document.getElementById('branchId').value;

        fetch(`/branches/${branchId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '{{ __("contract_details_new.error_updating_branch") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("contract_details_new.error_occurred") }}');
        });
    }

    function markAsPaid(paymentId) {
        const modal = new bootstrap.Modal(document.getElementById('markAsPaidModal'));
        document.getElementById('paymentId').value = paymentId;
        modal.show();
    }

    document.getElementById('markAsPaidForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const paymentId = document.getElementById('paymentId').value;
        const paymentMethod = document.getElementById('payment_method').value;

        fetch(`/Payments/${paymentId}/mark-as-paid`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ payment_method: paymentMethod })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Reload the page to show updated status
                window.location.reload();
            } else {
                alert(data.message || 'Error updating payment status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating payment status. Please try again.');
        });
    });

    function postponePayment(branchId) {
        document.getElementById('branchIdForPostponement').value = branchId;
        new bootstrap.Modal(document.getElementById('postponePaymentModal')).show();
    }

    function confirmPostponePayment() {
        const form = document.getElementById('postponePaymentForm');
        const formData = new FormData(form);
        const branchId = document.getElementById('branchIdForPostponement').value;

        fetch(`/branches/${branchId}/postpone`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("contract_details_new.error_occurred") }}');
        });
    }

    function approveRequest(requestId) {
        $('#requestId').val(requestId);
        $('#approve').prop('checked', true);
        $('#responseModal').modal('show');
    }

    function rejectRequest(requestId) {
        $('#requestId').val(requestId);
        $('#reject').prop('checked', true);
        $('#responseModal').modal('show');
    }

    function submitResponse() {
        const requestId = $('#requestId').val();
        const status = $('input[name="status"]:checked').val();
        const response = $('#response').val();

        if (!response) {
            toastr.error('{{ __("contract_details_new.error.response_required") }}');
            return;
        }

        axios.post(`/contract-requests/${requestId}/${status === 'approved' ? 'approve' : 'reject'}`, {
            response: response
        })
        .then(response => {
            if (response.data && response.data.message) {
                toastr.success(response.data.message);
                $('#responseModal').modal('hide');
                location.reload();
            }
        })
        .catch(error => {
            const errorMessage = error.response && error.response.data && error.response.data.error 
                ? error.response.data.error 
                : '{{ __("contract_details_new.error.process_request") }}';
            toastr.error(errorMessage);
            console.error('Error:', error);
        });
    }

    let currentPaymentId = null;

    function viewPaymentDetails(paymentId) {
        console.log('Opening payment details for ID:', paymentId);
        currentPaymentId = paymentId;
        
        // Show loading state
        const modal = $('#viewPaymentDetailsModal');
        modal.find('.modal-body').html('<div class="py-4 text-center"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">{{ __('contract_details_new.loading') }}...</div></div>');
        modal.modal('show');

        // Fetch payment details
        fetch(`/Payments/${paymentId}/details`, {
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Payment details data:', data);
            if (data.success) {
                const payment = data.payment;
                console.log('Payment object:', payment);
                
                // Create the content HTML
                const contentHtml = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded border h-100">
                                <h6 class="mb-3 text-primary">
                                    <i class="bx bx-info-circle me-2"></i>
                                    {{ __('contract_details_new.basic_info') }}
                                </h6>
                                <div class="mb-2">
                                    <label class="fw-bold">{{ __('contract_details_new.payment_id') }}:</label>
                                    <span>${payment.invoice_number || '-'}</span>
                                </div>
                                <div class="mb-2">
                                    <label class="fw-bold">{{ __('contract_details_new.payment_info_amount') }}:</label>
                                    <span>${new Intl.NumberFormat('en-US', {
                                        style: 'currency',
                                        currency: 'SAR'
                                    }).format(payment.payment_amount)}</span>
                                </div>
                                <div class="mb-2">
                                    <label class="fw-bold">{{ __('contract_details_new.payment_info_status') }}:</label>
                                    <span><span class="badge bg-${payment.payment_status === 'paid' ? 'success' : 'warning'}">${payment.payment_status.charAt(0).toUpperCase() + payment.payment_status.slice(1)}</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded border h-100">
                                <h6 class="mb-3 text-primary">
                                    <i class="bx bx-calendar me-2"></i>
                                    {{ __('contract_details_new.payment_info_date_info') }}
                                </h6>
                                <div class="mb-2">
                                    <label class="fw-bold">{{ __('contract_details_new.payment_info_due_date') }}:</label>
                                    <span>${payment.due_date ? new Date(payment.due_date).toLocaleDateString('en-US', { 
                                        year: 'numeric', 
                                        month: 'short', 
                                        day: 'numeric' 
                                    }) : '-'}</span>
                                </div>
                                <div class="mb-2">
                                    <label class="fw-bold">{{ __('contract_details_new.payment_info_payment_date')
                                    }}:</label>
                                    <span>${payment.paid_at ? new Date(payment.paid_at).toLocaleDateString('en-US', { 
                                        year: 'numeric', 
                                        month: 'short', 
                                        day: 'numeric' 
                                    }) : '-'}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 rounded border">
                                <h6 class="mb-3 text-primary">
                                    <i class="bx bx-note me-2"></i>
                                    {{ __('contract_details_new.additional_info') }}
                                </h6>
                                <div class="mb-2">
                                    <label class="fw-bold">{{ __('contract_details_new.payment_method') }}:</label>
                                    <span>${payment.payment_method || '-'}</span>
                                </div>
                                <div class="mb-2">
                                    <label class="fw-bold">{{ __('contract_details_new.notes') }}:</label>
                                    <p id="modal-payment-notes" class="mb-0">${payment.payment_description || 'No notes available'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Update modal content
                modal.find('.modal-body').html(contentHtml);
            } else {
                console.error('Error in response:', data.message);
                modal.find('.modal-body').html(`
                    <div class="mb-0 alert alert-danger">
                        <i class="bx bx-error-circle me-2"></i>
                        ${data.message || 'Failed to load payment details'}
                    </div>
                `);
            }
        })
        .catch(error => {
            console.error('Error fetching payment details:', error);
            let errorMessage = 'An error occurred while loading payment details';
            if (error.message.includes('HTTP error! status: 404')) {
                errorMessage = 'Payment not found';
            } else if (error.message.includes('HTTP error! status: 403')) {
                errorMessage = 'You do not have permission to view this payment';
            }
            modal.find('.modal-body').html(`
                <div class="mb-0 alert alert-danger">
                    <i class="bx bx-error-circle me-2"></i>
                    ${errorMessage}
                </div>
            `);
        });
    }

    function viewInvoice(paymentId) {
        window.location.href = `/Payment/view Payment Details/${paymentId}`;
    }
</script>
@endsection

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markAsPaidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="markAsPaidForm">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" id="paymentId" name="paymentId">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="bank transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                        </select>
                        <div class="invalid-feedback">Please select a payment method.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="markAsPaidForm" class="btn btn-success">Mark as Paid</button>
            </div>
        </div>
    </div>
</div>

<!-- Postpone Payment Modal -->
<div class="modal fade" id="postponePaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('contract_details_new.modal_postpone_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="postponePaymentForm">
                    <input type="hidden" id="branchIdForPostponement">
                    <div class="mb-3">
                        <label for="requestedDate" class="form-label">{{ __('contract_details_new.modal_postpone_date')
                            }}</label>
                        <input type="date" class="form-control" id="requestedDate" name="requested_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">{{ __('contract_details_new.modal_postpone_reason')
                            }}</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('contract_details_new.button_cancel') }}
                </button>
                <button type="button" class="btn btn-warning" onclick="confirmPostponePayment()">
                    {{ __('contract_details_new.button_confirm') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('contract_details_new.modal_response_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="responseForm">
                    <input type="hidden" id="requestId">
                    <div class="mb-3">
                        <label for="response" class="form-label">{{ __('contract_details_new.modal_response_text')
                            }}</label>
                        <textarea class="form-control" id="response" name="response" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="approve" value="approved"
                                checked>
                            <label class="form-check-label" for="approve">
                                {{ __('contract_details_new.status_approved') }}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="reject" value="rejected">
                            <label class="form-check-label" for="reject">
                                {{ __('contract_details_new.status_rejected') }}
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('contract_details_new.button_cancel') }}
                </button>
                <button type="button" class="btn btn-primary" onclick="submitResponse()">
                    {{ __('contract_details_new.button_submit') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Branch Modal -->
<div class="modal fade" id="editBranchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('contract_details_new.modal_edit_branch_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editBranchForm">
                    <input type="hidden" id="branchId" name="branch_id">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="branchName" class="form-label">{{
                                __('contract_details_new.modal_edit_branch_name') }}</label>
                            <input type="text" class="form-control" id="branchName" name="branch_name" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="branchManagerName" class="form-label">{{
                                __('contract_details_new.modal_edit_branch_manager') }}</label>
                            <input type="text" class="form-control" id="branchManagerName" name="branch_manager_name"
                                required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="branchManagerPhone" class="form-label">{{
                                __('contract_details_new.modal_edit_branch_phone') }}</label>
                            <input type="tel" class="form-control" id="branchManagerPhone" name="branch_manager_phone"
                                required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="branchAddress" class="form-label">{{
                                __('contract_details_new.modal_edit_branch_address') }}</label>
                            <input type="text" class="form-control" id="branchAddress" name="branch_address" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('contract_details_new.button_cancel') }}
                </button>
                <button type="button" class="btn btn-primary" onclick="submitEditBranch()">
                    {{ __('contract_details_new.button_save') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="viewPaymentDetailsModal" tabindex="-1" aria-labelledby="viewPaymentDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="text-white modal-header bg-primary">
                <h5 class="modal-title" id="viewPaymentDetailsModalLabel">
                    <i class="bx bx-credit-card me-2"></i>
                    {{ __('contract_details_new.button_view_payment_details') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded border h-100">
                            <h6 class="mb-3 text-primary">
                                <i class="bx bx-info-circle me-2"></i>
                                {{ __('contract_details_new.basic_info') }}
                            </h6>
                            <div class="mb-2">
                                <label class="fw-bold">{{ __('contract_details_new.payment_id') }}:</label>
                                <span id="modal-payment-id"></span>
                            </div>
                            <div class="mb-2">
                                <label class="fw-bold">{{ __('contract_details_new.payment_info_amount') }}:</label>
                                <span id="modal-payment-amount"></span>
                            </div>
                            <div class="mb-2">
                                <label class="fw-bold">{{ __('contract_details_new.payment_info_status') }}:</label>
                                <span id="modal-payment-status"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded border h-100">
                            <h6 class="mb-3 text-primary">
                                <i class="bx bx-calendar me-2"></i>
                                {{ __('contract_details_new.payment_info_date_info') }}
                            </h6>
                            <div class="mb-2">
                                <label class="fw-bold">{{ __('contract_details_new.payment_info_due_date') }}:</label>
                                <span id="modal-payment-due-date"></span>
                            </div>
                            <div class="mb-2">
                                <label class="fw-bold">{{ __('contract_details_new.payment_info_payment_date')
                                    }}:</label>
                                <span id="modal-payment-date"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded border">
                            <h6 class="mb-3 text-primary">
                                <i class="bx bx-note me-2"></i>
                                {{ __('contract_details_new.additional_info') }}
                            </h6>
                            <div class="mb-2">
                                <label class="fw-bold">{{ __('contract_details_new.payment_method') }}:</label>
                                <span id="modal-payment-method"></span>
                            </div>
                            <div class="mb-2">
                                <label class="fw-bold">{{ __('contract_details_new.notes') }}:</label>
                                <p id="modal-payment-notes" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i>
                    {{ __('contract_details_new.button_cancel') }}
                </button>
                <button type="button" class="btn btn-primary" onclick="viewInvoice(currentPaymentId)">
                    <i class="bx bx-file me-1"></i>
                    {{ __('contract_details_new.button_view_invoice') }}
                </button>
            </div>
        </div>
    </div>
</div>