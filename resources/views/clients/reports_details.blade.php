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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-4 d-flex align-items-center">
                            <div>
                                <h5 class="mb-0">Visit Details</h5>
                                <p class="mb-0 text-muted">Visit #{{ $visit->id }}</p>
                            </div>
                            <div class="ms-auto">
                                @if ($visit->status == 'completed' && $visit->report)
                                    <button id="printReportBtn" class="btn btn-primary btn-sm me-2">
                                        <i class="bx bx-printer me-1"></i>Print Report
                                    </button>
                                @endif
                                <a href="{{ route('client.contract.visit.details', $visit->contract->id) }}"
                                    class="btn btn-secondary btn-sm">
                                    <i class="bx bx-arrow-back me-1"></i>Back to Visits
                                </a>
                            </div>
                        </div>

                        <!-- Visit Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="border card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Visit Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="35%">Visit Date</th>
                                                <td>{{ date('M d, Y', strtotime($visit->visit_date)) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Visit Time</th>
                                                <td>{{ date('h:i A', strtotime($visit->visit_time)) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    @if ($visit->status == 'completed')
                                                        <span class="badge bg-success">
                                                            <i class="bx bx-check-circle me-1"></i>Completed
                                                        </span>
                                                    @elseif($visit->status == 'cancelled')
                                                        <span class="badge bg-danger">
                                                            <i class="bx bx-x-circle me-1"></i>Cancelled
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i class="bx bx-time me-1"></i>Pending
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Visit Number</th>
                                                <td>{{ $visit->visit_number }} of {{ $visit->total_visits }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Customer Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="35%">Contract Number</th>
                                                <td>
                                                    <a href="{{ route('client.contract.details', $visit->contract->id) }}"
                                                        class="text-primary">
                                                        {{ $visit->contract->contract_number }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Customer Name</th>
                                                <td>{{ $visit->contract->customer->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Location</th>
                                                <td>
                                                    @if ($visit->branch_id)
                                                        {{ $visit->branch->branch_name }}
                                                        <small
                                                            class="d-block text-muted">{{ $visit->branch->branch_address }}</small>
                                                    @else
                                                        Main Location
                                                        <small
                                                            class="d-block text-muted">{{ $visit->contract->customer->address }}</small>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Contact Number</th>
                                                <td>{{ $visit->contract->customer->phone }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Visit Report (if completed) -->
                        @if ($visit->status == 'completed' && $visit->report)
                            <div class="mt-4 row">
                                <div class="col-12">
                                    <div class="border card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Visit Report</h6>
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
                                                                    - <span
                                                                        class="badge bg-info">{{ $insectQuantities[$insect] }}
                                                                        {{ $insectQuantities[$insect] == 1 ? 'piece' : 'pieces' }}</span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Pesticides Used with Quantities</h6>
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
                                                                    - <span
                                                                        class="badge bg-info">{{ $quantities[$pesticide]['quantity'] }}
                                                                        {{ $quantities[$pesticide]['unit'] }}</span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="mt-4 row">
                                                <div class="col-12">
                                                    <h6>Elimination Steps</h6>
                                                    <p class="mb-0">{{ $visit->report->elimination_steps }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-4 row">
                                                <div class="col-md-6">
                                                    <h6>Recommendations & Observations</h6>
                                                    <p>{{ $visit->report->recommendations }}</p>
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
                                                                <span
                                                                    style="font-size: 2rem;">{{ $satisfactionEmoji }}</span>
                                                            </div>
                                                            <div>
                                                                <span
                                                                    class="badge {{ $badgeClass }}">{{ $satisfactionText }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($visit->report->customer_signature)
                                                <div class="mt-4 row">
                                                    <div class="col-md-6">
                                                        <h6>Customer Signature</h6>
                                                        <img src="{{ $visit->report->customer_signature }}"
                                                            alt="Customer Signature" class="img-fluid"
                                                            style="max-height: 100px;">
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($visit->report->phone_signature)
                                                <div class="mt-4 row">
                                                    <div class="col-md-6">
                                                        <h6>Phone Signature</h6>
                                                        <p>{{ $visit->report->phone_signature }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if ($visit->status == 'completed' && $visit->report)
    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const printButton = document.getElementById('printReportBtn');

                if (printButton) {
                    printButton.addEventListener('click', function() {
                        try {
                            // Add print-specific CSS to the current page
                            const style = document.createElement('style');
                            style.id = 'print-style';
                            style.innerHTML = `
                                @media print {
                                    body * {
                                        visibility: hidden;
                                    }
                                    .page-content, .page-content * {
                                        visibility: visible;
                                    }
                                    .card-body {
                                        break-inside: avoid;
                                    }
                                    .page-content {
                                        position: absolute;
                                        left: 0;
                                        top: 0;
                                        width: 100%;
                                    }
                                    .no-print, .no-print * {
                                        display: none !important;
                                    }
                                    .header, .footer, nav, aside, button, .btn {
                                        display: none !important;
                                    }
                                    @page {
                                        size: A4;
                                        margin: 1cm;
                                    }
                                }`;
                            document.head.appendChild(style);

                            // Hide elements that shouldn't be printed
                            const elementsToHide = document.querySelectorAll('.no-print');
                            Array.from(elementsToHide).forEach(el => {
                                el.classList.add('d-none');
                            });

                            // Add a header to the report for printing only
                            const reportContainer = document.querySelector('.card-body');
                            const printHeader = document.createElement('div');
                            printHeader.className = 'print-only mb-4 d-none';
                            printHeader.innerHTML = `
                                <div class="text-center">
                                    <h3>Visit Report</h3>
                                    <p>Visit #{{ $visit->id }} - {{ date('M d, Y', strtotime($visit->visit_date)) }}</p>
                                    <p>Customer: {{ $visit->contract->customer->name }}</p>
                                    <p>Location: {{ $visit->branch_id ? $visit->branch->branch_name : 'Main Location' }}</p>
                                </div>
                            `;
                            reportContainer.prepend(printHeader);

                            // Remove the d-none class for printing
                            printHeader.classList.remove('d-none');

                            // Print the page
                            window.print();

                            // After printing, restore the page
                            setTimeout(() => {
                                // Remove the print style
                                const printStyle = document.getElementById('print-style');
                                if (printStyle) {
                                    printStyle.parentNode.removeChild(printStyle);
                                }

                                // Remove the print header
                                if (printHeader.parentNode) {
                                    printHeader.parentNode.removeChild(printHeader);
                                }

                                // Show elements again
                                Array.from(elementsToHide).forEach(el => {
                                    el.classList.remove('d-none');
                                });
                            }, 1000);
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
    @endsection
@endif
