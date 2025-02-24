<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tax Invoice</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
            padding: 1rem;
            direction: rtl;
        }

        .invoice-container {
            background: white;
            padding: 1rem;
            width: 100%;
            margin: 0 auto;
        }

        .invoice-header {
            background: linear-gradient(135deg, #ffd700, #ffeb3b);
            color: #000;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .invoice-header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: bold;
        }

        .invoice-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .grid-header {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .grid-cols-2 {
            width: 100%;
            margin-bottom: 20px;
        }

        .grid-cols-2 td {
            width: 50%;
            padding: 8px;
            vertical-align: top;
        }

        .info-table {
            width: 100%;
            margin-bottom: 1.5rem;
            border-collapse: collapse;
        }

        .info-table th,
        .info-table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
        }

        .info-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: right;
        }

        .details-section {
            margin-bottom: 1.5rem;
        }

        .amount-in-words {
            background-color: #f0f9ff;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: bold;
        }

        .payment-info {
            background-color: #fff7e6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 1.5rem;
        }

        .signature-section {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 2px dashed #e2e8f0;
        }

        .signature-grid {
            width: 100%;
        }

        .signature-grid td {
            width: 50%;
            padding: 15px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin: 50px 20% 10px 20%;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .qr-section {
            text-align: center;
            margin-top: 1.5rem;
            padding: 1rem;
            background-color: #f8fafc;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h1>فاتورة ضريبة</h1>
            <h2>Tax Invoice</h2>
        </div>

        <table class="grid-cols-2">
            <tr>
                <td>
                    <strong>Invoice Number:</strong> {{ $payment->invoice_number }}<br>
                    <strong>Date:</strong> {{ date('Y-m-d', strtotime($payment->created_at)) }}<br>
                    <strong>Due Date:</strong> {{ date('Y-m-d', strtotime($payment->due_date)) }}
                </td>
                <td>
                    <strong>Contract Number:</strong> {{ $payment->contract->contract_number }}<br>
                    <strong>Customer:</strong> {{ $payment->contract->customer->name }}<br>
                    <strong>Status:</strong> {{ ucfirst($payment->payment_status) }}<br>
                    <strong>Contract Total:</strong> {{ number_format($payment->contract->contract_price, 2) }}<br>
                    <strong>Payment Number:</strong> {{ $payment->payment_number }} of {{ $payment->contract->payments->count() }}
                </td>
            </tr>
        </table>

        <div class="details-section">
            <table class="info-table">
                <thead>
                    <tr>
                        <th>Description / الوصف</th>
                        <th>Amount / المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $payment->payment_description ?? 'Payment for contract ' . $payment->contract->contract_number }}</td>
                        <td class="text-right">{{ number_format($payment->payment_amount, 2) }}</td>
                    </tr>
                    @if(isset($payment->vat_amount) && $payment->vat_amount > 0)
                    <tr>
                        <td>VAT (15%)</td>
                        <td class="text-right">{{ number_format($payment->vat_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="font-bold">Total / المجموع</td>
                        <td class="font-bold text-right">{{ number_format($payment->payment_amount + ($payment->vat_amount ?? 0), 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="amount-in-words">
                Amount in words: {{ $words_english }}<br>
                المبلغ بالاحرف: {{ $words_arabic }}
            </div>
        </div>

        <div class="payment-info">
            <p>Payment Status: <strong>{{ ucfirst($payment->payment_status) }}</strong></p>
            @if($payment->payment_status === 'paid')
            <p>Payment Date: {{ date('Y-m-d', strtotime($payment->payment_date)) }}</p>
            @if($payment->payment_method)
            <p>Payment Method: {{ ucfirst($payment->payment_method) }}</p>
            @endif
            @endif
            <p>Contract Total: {{ number_format($payment->contract->contract_price, 2) }} SAR</p>
            <p>Total Payments: {{ $payment->contract->payments->count() }}</p>
            <p>Remaining Balance: {{ number_format($payment->contract->contract_price - $payment->contract->payments->where('payment_status', 'paid')->sum('payment_amount'), 2) }} SAR</p>
        </div>

        <div class="signature-section">
            <table class="signature-grid">
                <tr>
                    <td>
                        <div class="signature-line"></div>
                        <p>Customer Signature</p>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <p>Company Representative</p>
                    </td>
                </tr>
            </table>
        </div>

        @if(isset($payment->qr_code))
        <div class="qr-section">
            <img src="data:image/png;base64,{{ $payment->qr_code }}" alt="QR Code">
        </div>
        @endif
    </div>
</body>
</html>
