@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">Visit Report</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('sales.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('contract.show') }}">Active Contracts</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('view.contract.visit', $visit->contract_id) }}">Visit Schedule</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Visit Report</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Visit Report Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="card-text">Visit Date: {{ \Carbon\Carbon::parse($visit->visit_date)->format('d M, Y') }}</p>
                                <p class="card-text">Visit Time In: {{ \Carbon\Carbon::parse($visit->report->time_in)->format('h:i A') }}</p>
                                <p class="card-text">Visit Time Out: {{ \Carbon\Carbon::parse($visit->report->time_out)->format('h:i A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="card-text">Contract Number: {{ $visit->contract->contract_number }}</p>
                                <p class="card-text">Client Name: {{ $visit->contract->customer->name }}</p>
                                <p class="card-text">Visit Type: {{ $visit->report->visit_type }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 row">
            <div class="col-12">
                <div class="border card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Visit Report</h6>
                            <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-printer me-1"></i>Print Report
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Target Insects</h6>
                                <ul class="list-unstyled">
                                    @foreach(json_decode($visit->report->target_insects) as $insect)
                                    <li><i class="bx bx-check text-success me-2"></i>{{ ucfirst(str_replace('_', ' ', $insect)) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Pesticides Used</h6>
                                <ul class="list-unstyled">
                                    @foreach(json_decode($visit->report->pesticides_used) as $pesticide)
                                    <li><i class="bx bx-check text-success me-2"></i>{{ ucfirst(str_replace('_', ' ', $pesticide)) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <h6>Recommendations & Observations</h6>
                                <p>{{ $visit->report->recommendations ?: 'No recommendations provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Customer Notes</h6>
                                <p>{{ $visit->report->customer_notes ?: 'No notes provided' }}</p>
                            </div>
                        </div>
                        @if($visit->report->customer_signature)
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                @if($visit->report->customer_signature)
                                <div class="mt-4 row">
                                    <div class="col-md-6">
                                        <h6>Customer Signature</h6>
                                        <img src="{{ $visit->report->customer_signature }}" alt="Customer Signature" class="img-fluid" style="max-height: 100px;">
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6>Created By</h6>
                                <p class="mb-1">{{ $visit->report->createdBy->name }}</p>
                                <p class="text-muted">{{ $visit->report->created_at->format('d M, Y h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
