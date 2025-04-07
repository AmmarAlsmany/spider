@extends('shared.dashboard')

@section('styles')
    <style>
        @media print {

            /* Hide everything except the content we want to print */
            body * {
                visibility: hidden;
            }

            .page-content,
            .page-content * {
                visibility: visible;
            }

            /* Remove all navigation elements */
            .sidebar-wrapper,
            .topbar,
            .page-breadcrumb,
            .simplebar-content-wrapper>*:not(.page-content),
            .footer,
            .back-to-top,
            .btn-outline-primary,
            nav,
            .header-wrapper,
            .btn {
                display: none !important;
            }

            /* Position the printable content */
            .page-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100% !important;
                margin: 0 !important;
                padding: 15px !important;
            }

            /* Ensure report takes full width */
            .container,
            .card {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            /* Add page title */
            .card-body:before {
                content: "Visit Report";
                display: block;
                font-size: 24px;
                font-weight: bold;
                text-align: center;
                margin-bottom: 20px;
            }

            /* Remove background colors for print */
            .card-header {
                background: none !important;
                border-bottom: 1px solid #ddd !important;
            }

            /* Fix badge printing */
            .badge {
                border: 1px solid #000 !important;
                color: #000 !important;
                background: none !important;
            }

            /* Ensure proper page breaks */
            .card {
                page-break-inside: avoid;
            }
        }
    </style>

    <script>
        function printReport() {
            // Hide the print button before printing
            const printBtn = document.querySelector('.btn-outline-primary');
            if (printBtn) printBtn.style.display = 'none';

            // Delay to ensure styles are applied
            setTimeout(function() {
                window.print();

                // Show the button again after print dialog closes
                setTimeout(function() {
                    if (printBtn) printBtn.style.display = 'inline-flex';
                }, 100);
            }, 100);
        }
    </script>
@endsection

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
                                    <p class="card-text">Visit Date:
                                        {{ \Carbon\Carbon::parse($visit->visit_date)->format('d
                                                                                                                                                                                                                                                                                    M, Y') }}
                                    </p>
                                    <p class="card-text">Visit Time In:
                                        {{ \Carbon\Carbon::parse($visit->report->time_in)->format('h:i A') }}</p>
                                    <p class="card-text">Visit Time Out:
                                        {{ \Carbon\Carbon::parse($visit->report->time_out)->format('h:i A') }}</p>
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
                                <button onclick="printReport()" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-printer me-1"></i>Print Report
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Target Insects</h6>
                                    @php
                                        $insects = is_array($visit->report->target_insects)
                                            ? $visit->report->target_insects
                                            : json_decode($visit->report->target_insects, true);
                                        $insectQuantities = is_array($visit->report->insect_quantities)
                                            ? $visit->report->insect_quantities
                                            : json_decode($visit->report->insect_quantities, true);
                                    @endphp
                                    <ul class="list-unstyled">
                                        @foreach ($insects as $insect)
                                            <li>
                                                <i class="bx bx-check text-success me-2"></i>
                                                {{ ucfirst(str_replace('_', ' ', $insect)) }}
                                                @if (isset($insectQuantities[$insect]))
                                                    - <span class="badge bg-info">{{ $insectQuantities[$insect] }}
                                                        {{ $insectQuantities[$insect] == 1 ? 'piece' : 'pieces' }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Pesticides Used</h6>
                                    @php
                                        $pesticides = is_array($visit->report->pesticides_used)
                                            ? $visit->report->pesticides_used
                                            : json_decode($visit->report->pesticides_used, true);
                                        $quantities = is_array($visit->report->pesticide_quantities)
                                            ? $visit->report->pesticide_quantities
                                            : json_decode($visit->report->pesticide_quantities, true);
                                    @endphp
                                    <ul class="list-unstyled">
                                        @foreach ($pesticides as $pesticide)
                                            <li>
                                                <i class="bx bx-check text-success me-2"></i>
                                                {{ ucfirst(str_replace('_', ' ', $pesticide)) }}
                                                @if (isset($quantities[$pesticide]))
                                                    - <span class="badge bg-info">{{ $quantities[$pesticide]['quantity'] }}
                                                        {{ $quantities[$pesticide]['unit'] }}</span>
                                                @endif
                                            </li>
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

                            <!-- Customer Satisfaction Rating -->
                            @if (isset($visit->report->customer_satisfaction))
                                <div class="mt-4 row">
                                    <div class="col-12">
                                        <h6>Customer Satisfaction</h6>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @php
                                                    $satisfactionEmoji = '';
                                                    $satisfactionText = '';
                                                    $badgeClass = '';

                                                    switch ($visit->report->customer_satisfaction) {
                                                        case 1:
                                                            $satisfactionEmoji = '😡';
                                                            $satisfactionText = 'Very Dissatisfied';
                                                            $badgeClass = 'bg-danger';
                                                            break;
                                                        case 2:
                                                            $satisfactionEmoji = '😕';
                                                            $satisfactionText = 'Dissatisfied';
                                                            $badgeClass = 'bg-warning';
                                                            break;
                                                        case 3:
                                                            $satisfactionEmoji = '😐';
                                                            $satisfactionText = 'Neutral';
                                                            $badgeClass = 'bg-secondary';
                                                            break;
                                                        case 4:
                                                            $satisfactionEmoji = '🙂';
                                                            $satisfactionText = 'Satisfied';
                                                            $badgeClass = 'bg-info';
                                                            break;
                                                        case 5:
                                                            $satisfactionEmoji = '😄';
                                                            $satisfactionText = 'Very Satisfied';
                                                            $badgeClass = 'bg-success';
                                                            break;
                                                    }
                                                @endphp
                                                <span style="font-size: 2rem;">{{ $satisfactionEmoji }}</span>
                                            </div>
                                            <div>
                                                <span class="badge {{ $badgeClass }}">{{ $satisfactionText }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($visit->report->customer_signature)
                                <div class="mt-4 row">
                                    <div class="col-md-6">
                                        @if ($visit->report->customer_signature)
                                            <div class="mt-4 row">
                                                <div class="col-md-6">
                                                    <h6>Customer Signature</h6>
                                                    <img src="{{ $visit->report->customer_signature }}"
                                                        alt="Customer Signature" class="img-fluid"
                                                        style="max-height: 100px;">
                                                </div>
                                            </div>
                                            {{-- signature client phone --}}
                                            <div class="mt-4 row">
                                                <div class="col-md-6">
                                                    <h6>Customer Signature Phone</h6>
                                                    <p class="mb-1">{{ $visit->report->phone_signature }}</p>
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
