@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">{{ __('reports.visit_report') }}</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('client.show') }}">{{ __('reports.contracts') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('client.contract.visit.details', $visit->contract->id) }}">{{ __('reports.contract_number', ['number' => $visit->contract->contract_number]) }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('reports.visit_report') }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <button onclick="window.print()" class="btn btn-primary d-flex align-items-center">
                <i class="bx bx-printer me-1"></i> {{ __('reports.print_report') }}
            </button>
        </div>
    </div>

    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-0"><i class="bx bx-calendar-check me-2"></i>{{ __('reports.visit_details') }}</h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <p><strong>{{ __('reports.visit_date') }}:</strong> {{ \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') }}</p>
                    <p><strong>{{ __('reports.visit_time') }}:</strong> {{ \Carbon\Carbon::parse($visit->visit_time)->format('h:i A') }}</p>
                    <p><strong>{{ __('reports.status') }}:</strong> 
                        <span class="badge bg-success">{{ ucfirst($visit->status) }}</span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>{{ __('reports.contract_number', ['number' => '']) }}:</strong> {{ $visit->contract->contract_number }}</p>
                    <p><strong>{{ __('reports.contract_type') }}:</strong> {{ $visit->contract->contract_type }}</p>
                    <p><strong>{{ __('reports.service_team') }}:</strong> 
                        @if($visit->team)
                            <span class="d-flex align-items-center">
                                <i class="bx bx-group me-1"></i>
                                {{ $visit->team->name }}
                            </span>
                        @else
                            <span class="text-muted">{{ __('reports.not_assigned') }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($visit->report)
    <div class="mt-4 card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-0"><i class="bx bx-clipboard me-2"></i>{{ __('reports.service_report') }}</h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <h6 class="mb-2">{{ __('reports.target_insects') }}</h6>
                    <div class="mb-3">
                        @foreach(explode(',', $visit->report->target_insects) as $insect)
                            <span class="badge bg-info me-1">{{ trim($insect) }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="col-12">
                    <h6 class="mb-2">{{ __('reports.pesticides_used') }}</h6>
                    <div class="mb-3">
                        @foreach(explode(',', $visit->report->pesticides_used) as $pesticide)
                            <span class="badge bg-warning text-dark me-1">{{ trim($pesticide) }}</span>
                        @endforeach
                    </div>
                </div>

                @if($visit->report->notes)
                <div class="col-12">
                    <h6 class="mb-2">{{ __('reports.notes') }}</h6>
                    <p class="mb-3">{{ $visit->report->notes }}</p>
                </div>
                @endif

                @if($visit->report->recommendations)
                <div class="col-12">
                    <h6 class="mb-2">{{ __('reports.recommendations') }}</h6>
                    <p class="mb-3">{{ $visit->report->recommendations }}</p>
                </div>
                @endif

                <div class="col-12">
                    <div class="row g-3">
                        @if($visit->report->customer_signature)
                        <div class="col-md-6">
                            <h6 class="mb-2">{{ __('reports.customer_signature') }}</h6>
                            <img src="{{ asset('storage/' . $visit->report->customer_signature) }}" 
                                 alt="{{ __('reports.customer_signature') }}" class="img-fluid border rounded p-2"
                                 style="max-height: 100px;">
                            <div class="mt-1 text-muted small">
                                {{ __('reports.signed_on') }} {{ \Carbon\Carbon::parse($visit->report->customer_signature_timestamp)->format('M d, Y h:i A') }}
                            </div>
                        </div>
                        @endif

                        @if($visit->report->creator_signature)
                        <div class="col-md-6">
                            <h6 class="mb-2">{{ __('reports.team_leader_signature') }}</h6>
                            <img src="{{ asset('storage/' . $visit->report->creator_signature) }}" 
                                 alt="{{ __('reports.team_leader_signature') }}" class="img-fluid border rounded p-2"
                                 style="max-height: 100px;">
                            <div class="mt-1 text-muted small">
                                {{ __('reports.signed_on') }} {{ \Carbon\Carbon::parse($visit->report->creator_signature_timestamp)->format('M d, Y h:i A') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style media="print">
    .page-breadcrumb,
    .sidebar-wrapper,
    .navbar,
    .back-to-top {
        display: none !important;
    }
    .page-content {
        margin: 0 !important;
        padding: 0 !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
</style>
@endpush
@endsection
