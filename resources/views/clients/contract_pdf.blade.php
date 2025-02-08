<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract #{{ $contract->contract_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .contract-info {
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .detail-row {
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            border-top: 1px solid #333;
            padding-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Contract Agreement</h1>
        <h2>Contract #{{ $contract->contract_number }}</h2>
    </div>

    <div class="contract-info">
        <div class="section">
            <div class="section-title">Contract Details</div>
            <div class="detail-row">
                <span class="label">Start Date:</span>
                {{ \Carbon\Carbon::parse($contract->contract_start_date)->format('F d, Y') }}
            </div>
            <div class="detail-row">
                <span class="label">Property Type:</span>
                {{ $contract->Property_type }}
            </div>
            <div class="detail-row">
                <span class="label">Contract Value:</span>
                {{ number_format($contract->contract_price, 2) }} SAR
            </div>
        </div>
        Warranty: {{ $contract->warranty }} Months
        

        <div class="section">
            <div class="section-title">Payment Information</div>
            <div class="detail-row">
                <span class="label">Payment Type:</span>
                {{ $contract->Payment_type }}
            </div>
            <div class="detail-row">
                <span class="label">Number of Payments:</span>
                {{ $contract->number_Payments }}
            </div>
        </div>

        <div class="section">
            <div class="section-title">Contract Description</div>
            <p>{{ $contract->contract_description ?: 'No description available.' }}</p>
        </div>

        @if($contract->is_multi_branch)
        <div class="section">
            <div class="section-title">Branch Information</div>
            <p>This contract covers multiple branches.</p>
            @foreach($contract->branchs as $branch)
            <div class="detail-row">
                <span class="label">Branch {{ $loop->iteration }}:</span>
                {{ $branch->name }} - {{ $branch->address }}
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p>Client Signature</p>
            <p>{{ $client->name }}</p>
            <p>Date: _________________</p>
        </div>
        <div class="signature-box">
            <p>Company Representative</p>
            <p>{{ $contract->salesRepresentative->name }}</p>
            <p>Date: _________________</p>
        </div>
    </div>

    <div class="footer">
        <p>This document is electronically generated and is valid without signature.</p>
        <p>Generated on {{ now()->format('F d, Y H:i:s') }}</p>
    </div>
</body>
</html>
