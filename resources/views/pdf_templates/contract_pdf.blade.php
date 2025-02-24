<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .contract-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .contract-info {
            margin-bottom: 20px;
        }
        .contract-details {
            margin-bottom: 30px;
        }
        .contract-footer {
            margin-top: 50px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="contract-header">
        <h1>Contract Details</h1>
        <p>Contract #: {{ $contract->contract_number }}</p>
    </div>

    <div class="contract-info">
        <h2>Contract Information</h2>
        <table>
            <tr>
                <th>Client Name</th>
                <td>{{ $contract->customer->name }}</td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td>{{ $contract->contract_start_date }}</td>
            </tr>
            <tr>
                <th>End Date</th>
                <td>{{ $contract->contract_end_date }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $contract->contract_status }}</td>
            </tr>
        </table>
    </div>

    <div class="contract-details">
        <h2>Contract Details</h2>
        {!! $contract->details !!}
    </div>

    <div class="contract-footer">
        <p>Generated on {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
