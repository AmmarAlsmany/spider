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
                            // Create a new window for printing
                            const printWindow = window.open('', '_blank', 'width=1000,height=800,scrollbars=yes');

                            if (!printWindow) {
                                alert('Please allow popups for this website to print the report.');
                                return;
                            }

                            // Create print-friendly HTML with complete styling
                            printWindow.document.write(`
                                <!DOCTYPE html>
                                <html lang="en">
                                <head>
                                    <meta charset="UTF-8">
                                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                    <title>Visit Report #{{ $visit->id }}</title>
                                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
                                    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.0/css/boxicons.min.css" rel="stylesheet">
                                    <style>
                                        body { 
                                            padding: 20px; 
                                            font-family: Arial, sans-serif;
                                        }
                                        .report-header {
                                            margin-bottom: 30px;
                                            text-align: center;
                                            border-bottom: 1px solid #dee2e6;
                                            padding-bottom: 15px;
                                        }
                                        .report-section {
                                            margin-bottom: 20px;
                                        }
                                        .section-title {
                                            font-weight: bold;
                                            margin-bottom: 10px;
                                            color: #333;
                                        }
                                        .badge {
                                            display: inline-block;
                                            padding: 0.25em 0.4em;
                                            font-size: 75%;
                                            font-weight: 700;
                                            line-height: 1;
                                            text-align: center;
                                            white-space: nowrap;
                                            vertical-align: baseline;
                                            border-radius: 0.25rem;
                                        }
                                        .bg-info {
                                            background-color: #0dcaf0 !important;
                                            color: #fff;
                                        }
                                        .bg-success {
                                            background-color: #198754 !important;
                                            color: #fff;
                                        }
                                        .bg-warning {
                                            background-color: #ffc107 !important;
                                            color: #000;
                                        }
                                        .bg-danger {
                                            background-color: #dc3545 !important;
                                            color: #fff;
                                        }
                                        .bg-secondary {
                                            background-color: #6c757d !important;
                                            color: #fff;
                                        }
                                        .list-unstyled {
                                            list-style: none;
                                            padding-left: 0;
                                        }
                                        .list-unstyled li {
                                            margin-bottom: 5px;
                                        }
                                        .text-success {
                                            color: #198754 !important;
                                        }
                                        @media print {
                                            .no-print { 
                                                display: none !important; 
                                            }
                                            body {
                                                print-color-adjust: exact;
                                                -webkit-print-color-adjust: exact;
                                            }
                                            .card {
                                                border: none !important;
                                                box-shadow: none !important;
                                            }
                                            .card-header {
                                                background-color: #f8f9fa !important;
                                                border-bottom: 1px solid #dee2e6 !important;
                                            }
                                            @page {
                                                size: A4;
                                                margin: 1cm;
                                            }
                                        }
                                    </style>
                                </head>
                                <body>
                                    <div class="container">
                                        <div class="report-header">
                                            <h3>Visit Report</h3>
                                            <p>Visit #{{ $visit->id }} - {{ date('M d, Y', strtotime($visit->visit_date)) }}</p>
                                            <p>Customer: {{ $visit->contract->customer->name }}</p>
                                            <p>Location: {{ $visit->branch_id ? $visit->branch->branch_name : 'Main Location' }}</p>
                                        </div>
                                        
                                        <div class="report-content">
                                            <!-- Visit Information -->
                                            <div class="card mb-4">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">Visit Information</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Visit Date:</strong> {{ date('M d, Y', strtotime($visit->visit_date)) }}</p>
                                                            <p><strong>Visit Time:</strong> {{ date('h:i A', strtotime($visit->visit_time)) }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Visit Number:</strong> {{ $visit->visit_number }} of {{ $visit->total_visits }}</p>
                                                            <p><strong>Contract Number:</strong> {{ $visit->contract->contract_number }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Target Insects & Pesticides -->
                                            <div class="card mb-4">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">Treatment Details</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 class="section-title">Target Insects</h6>
                                                            <ul class="list-unstyled">
                                                                @foreach (is_array($visit->report->target_insects) ? $visit->report->target_insects : json_decode($visit->report->target_insects, true) as $insect)
                                                                <li>
                                                                    <i class="bx bx-check text-success me-2"></i>
                                                                    {{ ucfirst(str_replace('_', ' ', $insect)) }}
                                                                    @php
                                                                        $insectQuantities = is_array($visit->report->insect_quantities) ? $visit->report->insect_quantities : json_decode($visit->report->insect_quantities, true);
                                                                    @endphp
                                                                    @if (isset($insectQuantities[$insect]))
                                                                    - <span class="badge bg-info">{{ $insectQuantities[$insect] }} {{ $insectQuantities[$insect] == 1 ? 'piece' : 'pieces' }}</span>
                                                                    @endif
                                                                </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="section-title">Pesticides Used with Quantities</h6>
                                                            <ul class="list-unstyled">
                                                                @foreach (is_array($visit->report->pesticides_used) ? $visit->report->pesticides_used : json_decode($visit->report->pesticides_used, true) as $pesticide)
                                                                <li>
                                                                    <i class="bx bx-check text-success me-2"></i>
                                                                    {{ ucfirst(str_replace('_', ' ', $pesticide)) }}
                                                                    @php
                                                                        $quantities = is_array($visit->report->pesticide_quantities) ? $visit->report->pesticide_quantities : json_decode($visit->report->pesticide_quantities, true);
                                                                    @endphp
                                                                    @if (isset($quantities[$pesticide]))
                                                                    - <span class="badge bg-info">{{ $quantities[$pesticide]['quantity'] }} {{ $quantities[$pesticide]['unit'] }}</span>
                                                                    @endif
                                                                </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Treatment Details -->
                                            <div class="card mb-4">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">Treatment Process & Notes</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row mb-4">
                                                        <div class="col-12">
                                                            <h6 class="section-title">Elimination Steps</h6>
                                                            <p>{{ $visit->report->elimination_steps }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 class="section-title">Recommendations & Observations</h6>
                                                            <p>{{ $visit->report->recommendations }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="section-title">Customer Notes</h6>
                                                            <p>{{ $visit->report->customer_notes ?: 'No notes provided' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Customer Satisfaction -->
                                            @if (isset($visit->report->customer_satisfaction))
                                            <div class="card mb-4">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">Customer Feedback</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <h6 class="section-title">Customer Satisfaction</h6>
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
                                                </div>
                                            </div>
                                            @endif

                                            <!-- Signatures -->
                                            <div class="card mb-4">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">Verification</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        @if ($visit->report->customer_signature)
                                                        <div class="col-md-6">
                                                            <h6 class="section-title">Customer Signature</h6>
                                                            <img src="{{ $visit->report->customer_signature }}" alt="Customer Signature"
                                                                class="img-fluid" style="max-height: 100px;">
                                                        </div>
                                                        @endif
                                                        @if ($visit->report->phone_signature)
                                                        <div class="col-md-6">
                                                            <h6 class="section-title">Phone Signature</h6>
                                                            <p>{{ $visit->report->phone_signature }}</p>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 text-center no-print">
                                            <button class="btn btn-primary" onclick="window.print(); return false;">Print Report</button>
                                            <button class="btn btn-secondary ms-2" onclick="window.close(); return false;">Close</button>
                                        </div>
                                        <div class="mt-4 text-center">
                                            <p class="small text-muted">Report generated on {{ date('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>

                                    <script>
                                        // Wait for all resources to load before enabling print
                                        window.onload = function() {
                                            // Focus the window to bring it to front
                                            window.focus();
                                        }
                                    </script>
                                </body>
                                </html>
                            `);

                            // Ensure content is fully written before proceeding
                            printWindow.document.close();

                            // Set up onload handler with error handling
                            printWindow.onload = function() {
                                try {
                                    printWindow.focus();
                                    setTimeout(function() {
                                        printWindow.print();
                                    }, 500);
                                } catch (e) {
                                    console.error('Print error:', e);
                                    alert('Error during printing: ' + e.message);
                                }
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
