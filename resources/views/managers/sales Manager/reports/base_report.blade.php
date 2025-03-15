@extends('shared.dashboard')

@section('page_styles')
<style>
    @media print {
        /* Hide non-essential elements */
        .sidebar-wrapper,
        .topbar,
        .no-print,
        .footer {
            display: none !important;
        }

        /* Reset margins and padding */
        .page-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        /* Remove card styling */
        .card {
            border: none !important;
            box-shadow: none !important;
        }

        /* Ensure tables break properly */
        table {
            width: 100% !important;
            page-break-inside: auto !important;
        }
        
        tr {
            page-break-inside: avoid !important;
            page-break-after: auto !important;
        }

        th {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Ensure proper font sizes */
        body {
            font-size: 12pt !important;
            line-height: 1.3 !important;
        }

        /* Add page numbers */
        @page {
            margin: 1cm;
        }

        /* Ensure background colors print */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }

    /* Non-print styles for better screen display */
    .table-responsive {
        overflow-x: auto;
    }
    
    .table th {
        white-space: nowrap;
    }

    /* Print area styles */
    #printArea {
        background: white;
        padding: 20px;
    }

    .print-header {
        text-align: center;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h6 class="mb-0">@yield('report_title')</h6>
                    @if(request('start_date') && request('end_date'))
                        <p class="mb-0 text-secondary">Period: {{ request('start_date') }} to {{ request('end_date') }}</p>
                    @endif
                </div>
                <div class="ms-md-auto w-100 w-md-auto">
                    <div class="d-flex flex-column flex-md-row gap-2 align-items-stretch align-items-md-center">
                        <!-- Date Filter Form -->
                        <form method="GET" class="d-flex flex-column flex-md-row gap-2 no-print w-100">
                            <div class="d-flex gap-2 flex-grow-1">
                                <input type="date" name="start_date" class="form-control form-control-sm" placeholder="Start Date" value="{{ request('start_date') }}" />
                                <input type="date" name="end_date" class="form-control form-control-sm" placeholder="End Date" value="{{ request('end_date') }}" />
                            </div>
                            <button class="btn btn-sm btn-primary flex-grow-1" type="submit">Filter</button>
                        </form>
                        
                        <!-- PDF Export Form -->
                        <form action="{{ route('reports.pdf') }}" method="POST" class="d-inline" id="pdfExportForm">
                            @csrf
                            <input type="hidden" name="report_type" value="@yield('report_type', 'general')">
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <input type="hidden" name="title" value="@yield('report_title', 'Report')">
                            <input type="hidden" name="view_data" id="reportViewData">
                            <input type="hidden" name="html_content" id="reportHtmlContent">
                            <button type="button" class="btn btn-sm btn-danger w-100" id="exportPdfBtn">
                                <i class='bx bx-file-pdf'></i> Export PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body" id="printArea">
            <div class="mb-4 text-center print-header">
                <h4>@yield('report_title')</h4>
                @if(request('start_date') && request('end_date'))
                    <p>Period: {{ request('start_date') }} to {{ request('end_date') }}</p>
                @endif
            </div>
            @yield('report_content')
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle PDF export button click
                document.getElementById('exportPdfBtn').addEventListener('click', function() {
                    // Get the print area
                    const printArea = document.getElementById('printArea');
                    
                    // Create a clean version of the content for PDF export
                    const cleanContent = cleanHtmlForPdf(printArea);
                    
                    // Set the HTML content for the PDF
                    document.getElementById('reportHtmlContent').value = cleanContent;
                    
                    // Submit the form
                    document.getElementById('pdfExportForm').submit();
                });
                
                // Function to clean HTML for PDF export
                function cleanHtmlForPdf(element) {
                    // Clone the element to avoid modifying the original
                    const clone = element.cloneNode(true);
                    
                    // Remove any scripts
                    const scripts = clone.querySelectorAll('script');
                    scripts.forEach(script => script.remove());
                    
                    // Convert links to plain text
                    const links = clone.querySelectorAll('a');
                    links.forEach(link => {
                        const span = document.createElement('span');
                        span.textContent = link.textContent;
                        link.parentNode.replaceChild(span, link);
                    });
                    
                    // Remove buttons, forms, inputs
                    const buttons = clone.querySelectorAll('button, input[type="button"], input[type="submit"]');
                    buttons.forEach(button => button.remove());
                    
                    const forms = clone.querySelectorAll('form');
                    forms.forEach(form => {
                        const div = document.createElement('div');
                        div.innerHTML = form.innerHTML;
                        form.parentNode.replaceChild(div, form);
                    });
                    
                    const inputs = clone.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => input.remove());
                    
                    // Extract only the tables and their content
                    let tableHtml = '';
                    
                    // Add the title and period if present
                    const titleElement = clone.querySelector('.print-header');
                    if (titleElement) {
                        tableHtml += titleElement.innerHTML;
                    }
                    
                    // Add all tables
                    const tables = clone.querySelectorAll('table');
                    tables.forEach(table => {
                        tableHtml += table.outerHTML;
                    });
                    
                    return tableHtml;
                }
            });
        </script>
    </div>
</div>
@endsection

@section('page_scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.6.2/jQuery.print.min.js"></script>
<script>
    $(document).ready(function() {
        // Removed print button functionality
    });
</script>
@endsection