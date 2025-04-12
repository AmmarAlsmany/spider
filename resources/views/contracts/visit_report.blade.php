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
                                    <p class="card-text">Visit Date:
                                        {{ \Carbon\Carbon::parse($visit->visit_date)->format('d M, Y') }}
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
                                @if ($visit->status == 'completed' && $visit->report)
                                    <button id="printReportBtn" class="btn btn-primary btn-sm me-2">
                                        <i class="bx bx-printer me-1"></i>Print Report
                                    </button>
                                @endif
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

@if ($visit->status == 'completed' && $visit->report)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const printButton = document.getElementById('printReportBtn');

                if (printButton) {
                    printButton.addEventListener('click', function() {
                        try {
                            // Create a print header for the report
                            const reportContainer = document.querySelector('.card-body');
                            const printHeader = document.createElement('div');
                            printHeader.className = 'print-only mb-4';
                            printHeader.innerHTML = `
                                <div class="text-center">
                                    <h3>Visit Report</h3>
                                    <p>Visit #{{ $visit->id }} - {{ date('M d, Y', strtotime($visit->visit_date)) }}</p>
                                    <p>Customer: {{ $visit->contract->customer->name }}</p>
                                    <p>Location: {{ $visit->branch_id ? $visit->branch->branch_name : 'Main Location' }}</p>
                                </div>
                            `;
                            reportContainer.prepend(printHeader);

                            // Mark elements that shouldn't be printed
                            const buttonsToHide = document.querySelectorAll('.btn');
                            buttonsToHide.forEach(btn => btn.classList.add('no-print'));

                            // Print the page
                            window.print();

                            // Clean up after print dialog closes
                            window.onafterprint = function() {
                                // Remove the print header
                                if (printHeader.parentNode) {
                                    printHeader.parentNode.removeChild(printHeader);
                                }

                                // Remove no-print class from buttons
                                buttonsToHide.forEach(btn => btn.classList.remove('no-print'));
                            };
                        } catch (e) {
                            console.error('Print setup error:', e);
                            alert('Error setting up print: ' + e.message);
                        }
                    });
                } else {
                    console.warn('Print button not found on the page');
                }
            });
        </script>

        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                .page-content {
                    visibility: visible;
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }

                .page-content * {
                    visibility: visible;
                }

                .card-body {
                    break-inside: avoid;
                }

                .no-print,
                .no-print *,
                #printReportBtn,
                .btn,
                .alert,
                .header,
                .footer,
                nav,
                aside {
                    display: none !important;
                }

                @page {
                    size: A4;
                    margin: 1cm;
                }
            }
        </style>
    @endpush
@endif
