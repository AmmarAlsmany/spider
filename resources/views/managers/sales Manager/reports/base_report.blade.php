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
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">@yield('report_title')</h6>
                </div>
                <div class="ms-auto">
                    <form method="GET" class="gap-2 d-flex no-print">
                        <input type="date" name="start_date" class="form-control" placeholder="Start Date" value="{{ request('start_date') }}" />
                        <input type="date" name="end_date" class="form-control" placeholder="End Date" value="{{ request('end_date') }}" />
                        <button class="btn btn-primary" type="submit">Filter</button>
                        <button type="button" class="btn btn-success" id="printButton">
                            <i class='bx bx-printer'></i> Print
                        </button>
                    </form>
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
    </div>
</div>
@endsection

@section('page_scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.6.2/jQuery.print.min.js"></script>
<script>
    $(document).ready(function() {
        $("#printButton").on('click', function() {
            $("#printArea").print({
                globalStyles: true,
                mediaPrint: true,
                stylesheet: "https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css",
                iframe: false,
                noPrintSelector: ".no-print",
                deferred: $.Deferred().done(function() { console.log('Printing completed.'); })
            });
        });
    });
</script>
@endsection