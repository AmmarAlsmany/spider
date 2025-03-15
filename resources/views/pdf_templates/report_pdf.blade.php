<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Report' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

        body {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
        }

        .report-container {
            background: white;
            padding: 1rem;
            width: 100%;
            margin: 0 auto;
        }

        .company-logo {
            text-align: center;
            margin-bottom: 1rem;
        }

        .company-logo img {
            max-width: 200px;
            height: auto;
        }

        .report-header {
            background: #ffff00;
            color: #000;
            padding: 0.5rem;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .report-period {
            text-align: center;
            margin-bottom: 1rem;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }
        
        .fw-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="company-logo">
            <img src="{{ public_path('backend/assets/images/logo-icon.png') }}" alt="Spider Web Logo">
            <h3>خيوط العنكبوت لمكافحة الحشرات</h3>
            <h3>Spider Web For Pest Control</h3>
        </div>
        
        <div class="report-header">
            {{ $title ?? 'Report' }}
        </div>
        
        @if($startDate && $endDate)
        <div class="report-period">
            <p>Period: {{ $startDate }} to {{ $endDate }}</p>
        </div>
        @endif
        
        @if($reportType == 'contracts')
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Contract Number</th>
                        <th>Customer</th>
                        <th>Sales Agent</th>
                        <th>Contract Value</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($viewData['contracts'] as $index => $contract)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $contract['contract_number'] }}</td>
                        <td>{{ $contract['customer_name'] }}</td>
                        <td>{{ $contract['sales_rep_name'] }}</td>
                        <td>{{ number_format($contract['contract_price'], 2) }}</td>
                        <td>{{ $contract['start_date'] }}</td>
                        <td>{{ $contract['end_date'] }}</td>
                        <td>{{ ucfirst($contract['status']) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No contracts found for this period</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right fw-bold">Total Value:</td>
                        <td colspan="4" class="fw-bold">{{ number_format($viewData['total_value'] ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @elseif($reportType == 'payments')
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Payment Number</th>
                        <th>Contract</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($viewData['payments'] as $index => $payment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $payment['payment_number'] }}</td>
                        <td>{{ $payment['contract_number'] }}</td>
                        <td>{{ $payment['customer_name'] }}</td>
                        <td>{{ number_format($payment['payment_amount'], 2) }}</td>
                        <td>{{ $payment['payment_date'] }}</td>
                        <td>{{ ucfirst($payment['status']) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No payments found for this period</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right fw-bold">Total Amount:</td>
                        <td colspan="3" class="fw-bold">{{ number_format($viewData['total_amount'] ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            {!! $viewData['custom_content'] ?? 'No data available' !!}
        @endif
    </div>
</body>
</html>
