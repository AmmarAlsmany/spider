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
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">{{ __('clients.contracts.my_contracts') }}</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('clients.contracts.my_contracts') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12">
                @if($my_contracts->isEmpty())
                <div class="card">
                    <div class="py-5 text-center card-body">
                        <i class="bx bx-file text-primary" style="font-size: 48px;"></i>
                        <h5 class="mt-3">{{ __('contracts.no_contracts.title') }}</h5>
                        <p class="text-muted">{{ __('contracts.no_contracts.message') }}</p>
                    </div>
                </div>
                @else
                @foreach($my_contracts as $contract)
                <div class="mb-3 card">
                    <div class="bg-transparent card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('contracts.contract_number', ['number' => $contract->contract_number]) }}</h5>
                        <span class="badge bg-{{ 
                            $contract->contract_status == 'pending' ? 'warning' : 
                            ($contract->contract_status == 'approved' ? 'success' : 
                            ($contract->contract_status == 'Not approved' ? 'danger' :
                            ($contract->contract_status == 'active' ? 'primary' :
                            ($contract->contract_status == 'completed' ? 'info' : 'secondary')))) 
                        }}">
                            {{ __('contracts.status.' . strtolower($contract->contract_status)) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>{{ __('contracts.details.start_date') }}:</strong> {{ \Carbon\Carbon::parse($contract->contract_start_date)->format('M d, Y') }}</p>
                                <p class="mb-2"><strong>{{ __('contracts.details.property_type') }}:</strong> {{ $contract->Property_type }}</p>
                                <p class="mb-2"><strong>{{ __('contracts.details.contract_type') }}:</strong> {{ $contract->type ? $contract->type->name : 'N/A' }}</p>
                                <p class="mb-2"><strong>{{ __('contracts.details.contract_price') }}:</strong> {{ number_format($contract->contract_price, 2) }} SAR</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>{{ __('contracts.details.payment_type') }}:</strong> {{ $contract->Payment_type }}</p>
                                <p class="mb-2"><strong>{{ __('contracts.details.number_of_payments') }}:</strong> {{ $contract->number_Payments ? $contract->number_Payments : '1' }}</p>
                                <p class="mb-2"><strong>{{ __('contracts.details.multiple_branches') }}:</strong> {{ $contract->is_multi_branch === 'yes' ? __('contracts.details.yes') : __('contracts.details.no') }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6 class="mb-3">{{ __('contracts.details.description') }}</h6>
                            <p class="text-muted">{{ $contract->contract_description ?: __('contracts.details.no_description') }}</p>
                            @if($contract->updateRequests->count() > 0)
                                @php
                                    $latestRequest = $contract->updateRequests->first();
                                    $alertClass = $latestRequest->status === null ? 'info' : 
                                                ($latestRequest->status === 'approved' ? 'success' : 'danger');
                                @endphp
                                <div class="alert alert-{{ $alertClass }} d-flex align-items-center mt-3" role="alert">
                                    <i class="bx bx-info-circle me-2" style="font-size: 1.2rem;"></i>
                                    <div>
                                        @if($latestRequest->status === null)
                                            {{ __('contracts.update_request.pending') }}
                                        @else
                                            {{ __('contracts.update_request.status', ['status' => ucfirst($latestRequest->status)]) }}
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex-wrap gap-2 mt-4 d-flex">
                            @if($contract->contract_status == 'pending')
                            <div class="gap-2 mb-2 d-flex">
                                <form action="{{ route('client.contract.approve', $contract->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" {{ $contract->updateRequests->count() > 0 && $contract->updateRequests->first()->response === null ? 'disabled' : '' }}>
                                        <i class="bx bx-check me-1"></i> {{ __('contracts.actions.approve.button') }}
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $contract->id }}" {{ $contract->updateRequests->count() > 0 && $contract->updateRequests->first()->response === null ? 'disabled' : '' }}>
                                    <i class="bx bx-x me-1"></i> {{ __('contracts.actions.reject.button') }}
                                </button>
                            </div>
                            @endif
                            
                            <div class="flex-wrap gap-2 d-flex">
                                <a href="{{ route('client.contract.details', $contract->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-show"></i> {{ __('contracts.actions.view_details') }}
                                </a>

                                @if($contract->contract_status == 'pending')
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateRequestModal{{ $contract->id }}">
                                    <i class="bx bx-edit me-1"></i> {{ __('contracts.actions.request_update') }}
                                </button>
                                @endif

                                @if($contract->contract_status == 'approved')
                                <a href="{{ route('client.show.payment.details', $contract->id) }}" class="btn btn-info btn-sm">
                                    <i class="bx bx-money me-1"></i> {{ __('contracts.actions.payment_details') }}
                                </a>
                                <a href="{{ route('client.contract.visit.details', ['client' => Auth::guard('client')->id(), 'contract' => $contract->id]) }}" class="btn btn-success btn-sm">
                                    <i class="bx bx-calendar-check me-1"></i> {{ __('contracts.actions.visit_details') }}
                                </a>
                                @endif

                                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#historyModal{{ $contract->id }}">
                                    <i class="bx bx-history me-1"></i> {{ __('contracts.actions.contract_history') }}
                                </button>

                                @if($contract->contract_status != 'pending')
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#updateHistoryModal{{ $contract->id }}">
                                    <i class="bx bx-list-check me-1"></i> {{ __('contracts.actions.update_history') }}
                                </button>
                                @endif

                                <a href="{{ route('contract.pdf.generate', $contract->id) }}" class="btn btn-dark btn-sm">
                                    <i class="bx bx-download me-1"></i> {{ __('contracts.actions.download') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal{{ $contract->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('contracts.actions.reject.title', ['number' => $contract->contract_number]) }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('client.contract.reject', $contract->id) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="reject_reason" class="form-label">{{ __('contracts.actions.reject.reason_label') }}</label>
                                        <textarea class="form-control" id="reject_reason" name="reject_reason" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('contracts.cancel') }}</button>
                                    <button type="submit" class="btn btn-danger">{{ __('contracts.actions.reject.submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Update Request Modal -->
                <div class="modal fade" id="updateRequestModal{{ $contract->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('contracts.actions.request_update') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('client.contract.update-request', $contract->id) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="request_details" class="form-label">{{ __('contracts.actions.request_update_label') }}</label>
                                        <textarea class="form-control" id="request_details" name="request_details" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('contracts.cancel') }}</button>
                                    <button type="submit" class="btn btn-warning">{{ __('contracts.actions.request_update_submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contract History Modal -->
                <div class="modal fade" id="historyModal{{ $contract->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('contracts.modals.contract_history', ['number' => $contract->contract_number]) }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="timeline-wrapper">
                                    <div class="timeline-item">
                                        <div class="timeline-badge bg-primary">
                                            <i class="bx bx-file"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">{{ __('contracts.modals.contract_created') }}</h6>
                                            <p class="mb-0">{{ __('contracts.modals.contract_number', ['number' => $contract->contract_number]) }}</p>
                                            <p class="mb-0">{{ __('contracts.modals.contract_type', ['type' => $contract->type ? $contract->type->name : 'N/A']) }}</p>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($contract->created_at)->format('M d, Y H:i A') }}</small>
                                        </div>
                                    </div>
                                    @if($contract->contract_status != 'pending')
                                    <div class="timeline-item">
                                        <div class="timeline-badge bg-{{ 
                                            $contract->contract_status == 'pending' ? 'warning' : 
                                            ($contract->contract_status == 'approved' ? 'success' : 
                                            ($contract->contract_status == 'Not approved' ? 'danger' :
                                            ($contract->contract_status == 'active' ? 'primary' :
                                            ($contract->contract_status == 'completed' ? 'info' : 'secondary')))) 
                                        }}">
                                            <i class="bx bx-{{ 
                                                $contract->contract_status == 'approved' || $contract->contract_status == 'active' ? 'check' : 
                                                ($contract->contract_status == 'Not approved' ? 'x' :
                                                ($contract->contract_status == 'completed' ? 'flag' : 'dots-horizontal-rounded')) 
                                            }}"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">{{ __('contracts.modals.contract_status', ['status' => ucfirst($contract->contract_status)]) }}</h6>
                                            <p class="mb-0 text-muted">{{ $contract->updated_at->format('M d, Y H:i A') }}</p>
                                            @if($contract->contract_status == 'Not approved' && $contract->reject_reason)
                                            <p class="mt-2 text-danger">{{ __('contracts.modals.reject_reason', ['reason' => $contract->reject_reason]) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update History Modal -->
                <div class="modal fade" id="updateHistoryModal{{ $contract->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('contracts.modals.update_history') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if($contract->updateRequests && $contract->updateRequests->count() > 0)
                                    @foreach($contract->updateRequests as $request)
                                    <div class="mb-3 card">
                                        <div class="card-body">
                                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                                <span class="badge bg-{{ $request->status == 'pending' ? 'warning' : ($request->status == 'approved' ? 'success' : 'danger') }}">
                                                    {{ __('contracts.modals.update_request_status', ['status' => ucfirst($request->status)]) }}
                                                </span>
                                                <small class="text-muted">{{ $request->created_at->format('M d, Y H:i A') }}</small>
                                            </div>
                                            <h6 class="mb-2">{{ __('contracts.modals.update_request_details') }}</h6>
                                            <p class="mb-3">{{ $request->request_details }}</p>
                                            @if($request->response)
                                            <div class="p-3 rounded bg-light">
                                                <h6 class="mb-2">{{ __('contracts.modals.update_request_response') }}</h6>
                                                <p class="mb-0">{{ $request->response }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                <div class="py-4 text-center">
                                    <i class="bx bx-info-circle text-muted" style="font-size: 48px;"></i>
                                    <p class="mt-2">{{ __('contracts.modals.no_update_requests') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Details Modal -->
                <div class="modal fade" id="viewDetailsModal{{ $contract->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('contracts.modals.contract_details', ['number' => $contract->contract_number]) }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h6 class="text-muted">{{ __('contracts.modals.contract_information') }}</h6>
                                            <p class="mb-2"><strong>{{ __('contracts.modals.contract_number') }}:</strong> {{ $contract->contract_number }}</p>
                                            <p class="mb-2"><strong>{{ __('contracts.modals.contract_status') }}:</strong> 
                                                <span class="badge bg-{{ 
                                                    $contract->contract_status == 'pending' ? 'warning' : 
                                                    ($contract->contract_status == 'approved' ? 'success' : 
                                                    ($contract->contract_status == 'Not approved' ? 'danger' :
                                                    ($contract->contract_status == 'active' ? 'primary' :
                                                    ($contract->contract_status == 'completed' ? 'info' : 'secondary')))) 
                                                }}">
                                                    {{ __('contracts.status.' . strtolower($contract->contract_status)) }}
                                                </span>
                                            </p>
                                            <p class="mb-2"><strong>{{ __('contracts.modals.contract_start_date') }}:</strong> {{ \Carbon\Carbon::parse($contract->contract_start_date)->format('M d, Y') }}</p>
                                            <p class="mb-2"><strong>{{ __('contracts.modals.contract_end_date') }}:</strong> {{ \Carbon\Carbon::parse($contract->contract_end_date)->format('M d, Y') }}</p>
                                            <p class="mb-2"><strong>{{ __('contracts.modals.property_type') }}:</strong> {{ $contract->Property_type }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h6 class="text-muted">{{ __('contracts.modals.financial_details') }}</h6>
                                            <p class="mb-2"><strong>{{ __('contracts.modals.monthly_rent') }}:</strong> {{ number_format($contract->monthly_rent, 2) }} SAR</p>
                                            <p class="mb-2"><strong>{{ __('contracts.modals.security_deposit') }}:</strong> {{ number_format($contract->security_deposit, 2) }} SAR</p>
                                            <p class="mb-2"><strong>{{ __('contracts.modals.contract_duration') }}:</strong> {{ $contract->contract_duration }} months</p>
                                            <p class="mb-2"><strong>{{ __('contracts.modals.payment_method') }}:</strong> {{ $contract->payment_method }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 row">
                                    <div class="col-12">
                                        <h6 class="text-muted">{{ __('contracts.modals.additional_information') }}</h6>
                                        <p class="mb-2"><strong>{{ __('contracts.modals.terms_conditions') }}:</strong></p>
                                        <div class="p-3 rounded bg-light">
                                            {!! $contract->terms_conditions !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('contracts.cancel') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .detail-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        height: 100%;
    }

    .card {
        border: none;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .badge {
        padding: 8px 12px;
        font-weight: 500;
    }

    /* Timeline Styles */
    .timeline-wrapper {
        position: relative;
        padding: 20px 0;
    }

    .timeline-item {
        position: relative;
        padding-left: 40px;
        margin-bottom: 30px;
    }

    .timeline-badge {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        position: absolute;
        left: 0;
        top: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .timeline-content {
        padding-left: 15px;
        border-left: 2px solid #e9ecef;
    }
</style>
@endpush
@endsection