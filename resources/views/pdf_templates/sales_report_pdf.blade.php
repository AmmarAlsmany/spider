<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .header-logo img {
            max-height: 80px;
            margin-bottom: 10px;
        }
        .header-logo h2 {
            margin: 0;
            color: #2c3e50;
        }
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 20px;
        }
        .report-header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #e0e0e0;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stats-box {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            padding: 15px;
            border-radius: 5px;
        }
        .report-footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header-logo">
        <img src="{{ public_path('backend/assets/images/logo-img.png') }}" alt="Spider Web Services Logo">
        <h2>Spider Web Services</h2>
    </div>

    <div class="report-header">
        <h1>Sales Report</h1>
        <p>Period: {{ $periodInfo['period_label'] }}</p>
    </div>

    <div class="stats-grid">
        <div class="stats-box">
            <h3 class="section-title">Contract Statistics</h3>
            <table>
                <tr>
                    <th>Total Contracts</th>
                    <td class="text-right">{{ $contractStats['total_contracts'] }}</td>
                </tr>
                <tr>
                    <th>Active Contracts</th>
                    <td class="text-right">{{ $contractStats['active_contracts'] }}</td>
                </tr>
                <tr>
                    <th>Pending Contracts</th>
                    <td class="text-right">{{ $contractStats['pending_contracts'] }}</td>
                </tr>
                <tr>
                    <th>Completed Contracts</th>
                    <td class="text-right">{{ $contractStats['completed_contracts'] }}</td>
                </tr>
                <tr>
                    <th>Cancelled Contracts</th>
                    <td class="text-right">{{ $contractStats['cancelled_contracts'] }}</td>
                </tr>
            </table>
        </div>

        <div class="stats-box">
            <h3 class="section-title">Financial Summary</h3>
            <table>
                <tr>
                    <th>Total Contract Value</th>
                    <td class="text-right">{{ number_format($financialSummary['total_contract_value'], 2) }}</td>
                </tr>
                <tr>
                    <th>Total Paid</th>
                    <td class="text-right">{{ number_format($financialSummary['total_paid'], 2) }}</td>
                </tr>
                <tr>
                    <th>Total Pending</th>
                    <td class="text-right">{{ number_format($financialSummary['total_pending'], 2) }}</td>
                </tr>
                <tr>
                    <th>Total Overdue</th>
                    <td class="text-right">{{ number_format($financialSummary['total_overdue'], 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Contracts</h3>
        <table>
            <thead>
                <tr>
                    <th>Contract Number</th>
                    <th>Customer</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Status</th>
                    <th>Created Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contracts->flatten() as $contract)
                <tr>
                    <td>{{ $contract->contract_number }}</td>
                    <td>{{ $contract->customer->name }}</td>
                    <td>{{ $contract->type->name }}</td>
                    <td class="text-right">{{ number_format($contract->contract_price, 2) }}</td>
                    <td>{{ ucfirst($contract->contract_status) }}</td>
                    <td>{{ date('Y-m-d', strtotime($contract->created_at)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($payments->isNotEmpty())
    <div class="section">
        <h3 class="section-title">Payments</h3>
        <table>
            <thead>
                <tr>
                    <th>Due Date</th>
                    <th>Contract</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments->flatten() as $payment)
                <tr>
                    <td>{{ date('Y-m-d', strtotime($payment->due_date)) }}</td>
                    <td>{{ $payment->contract->contract_number }}</td>
                    <td class="text-right">{{ number_format($payment->payment_amount, 2) }}</td>
                    <td>{{ ucfirst($payment->payment_status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="report-footer">
        <p>Generated on {{ date('F d, Y \a\t h:i A') }}</p>
        <p>This is an official sales report document.</p>
    </div>
</body>
</html>
