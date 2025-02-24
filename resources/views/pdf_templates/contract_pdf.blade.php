<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract Agreement</title>
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
        .contract-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 20px;
        }
        .contract-header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .contract-number {
            font-size: 18px;
            color: #34495e;
            font-weight: bold;
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
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .payment-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        .visit-schedule {
            margin-top: 20px;
        }
        .contract-footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header-logo">
        <img src="{{ public_path('backend/assets/images/logo-img.png') }}" alt="Spider Web Services Logo">
        <h2>Spider Web Services</h2>
    </div>

    <div class="contract-header">
        <h1>SERVICE CONTRACT AGREEMENT</h1>
        <p class="contract-number">Contract Number: {{ $contract->contract_number }}</p>
    </div>

    <div class="section">
        <h3 class="section-title">Client Information</h3>
        <table>
            <tr>
                <th width="30%">Client Name</th>
                <td>{{ $contract->customer->name }}</td>
            </tr>
            <tr>
                <th>Phone</th>
                <td>{{ $contract->customer->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Mobile</th>
                <td>{{ $contract->customer->mobile ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $contract->customer->email ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3 class="section-title">Contract Details</h3>
        <table>
            <tr>
                <th width="30%">Contract Type</th>
                <td>{{ $contract->type->name ?? 'Standard' }}</td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td>{{ date('F d, Y', strtotime($contract->contract_start_date)) }}</td>
            </tr>
            <tr>
                <th>End Date</th>
                <td>{{ date('F d, Y', strtotime($contract->contract_end_date)) }}</td>
            </tr>
            <tr>
                <th>Duration</th>
                <td>{{ \Carbon\Carbon::parse($contract->contract_start_date)->diffInMonths($contract->contract_end_date) }} months</td>
            </tr>
            <tr>
                <th>Status</th>
                <td><strong>{{ ucfirst($contract->contract_status) }}</strong></td>
            </tr>
        </table>
    </div>

    @if($contract->branchs && count($contract->branchs) > 0)
    <div class="section">
        <h3 class="section-title">Service Locations</h3>
        <table>
            <thead>
                <tr>
                    <th>Branch Name</th>
                    <th>Manager</th>
                    <th>Phone</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contract->branchs as $branch)
                <tr>
                    <td>{{ $branch->branch_name }}</td>
                    <td>{{ $branch->branch_manager }}</td>
                    <td>{{ $branch->branch_manager_phone }}</td>
                    <td>{{ $branch->branch_address }}, {{ $branch->branch_city }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($contract->payments && count($contract->payments) > 0)
    <div class="section">
        <h3 class="section-title">Payment Information</h3>
        <div class="payment-info">
            <table>
                <thead>
                    <tr>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contract->payments as $payment)
                    <tr>
                        <td>{{ date('F d, Y', strtotime($payment->payment_date)) }}</td>
                        <td>{{ number_format($payment->payment_amount, 2) }}</td>
                        <td>{{ ucfirst($payment->payment_status) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($contract->visitSchedules && count($contract->visitSchedules) > 0)
    <div class="section">
        <h3 class="section-title">Visit Schedule</h3>
        <table class="visit-schedule">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Team</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contract->visitSchedules as $visit)
                <tr>
                    <td>{{ date('F d, Y', strtotime($visit->visit_date)) }}</td>
                    <td>{{ $visit->visit_type }}</td>
                    <td>{{ ucfirst($visit->status) }}</td>
                    <td>{{ $visit->team->name }}</td>
                    <td>{{ $visit->notes ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <h3 class="section-title">Terms and Conditions</h3>
        <div class="contract-details">
            {!! $contract->details !!}
        </div>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p>Client Signature</p>
            <p>Date: _________________</p>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <p>Company Representative</p>
            <p>Date: _________________</p>
        </div>
    </div>

    <div class="contract-footer">
        <p>Generated on {{ date('F d, Y \a\t h:i A') }}</p>
        <p>This is an official contract document. Please retain for your records.</p>
    </div>
</body>
</html>
