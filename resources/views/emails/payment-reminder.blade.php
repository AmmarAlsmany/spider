<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            background-color: #0d6efd;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .details {
            background-color: white;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 20px 0;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details table th, .details table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .details table th {
            width: 40%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Reminder</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $payment->customer->name }},</p>
            
            <p>We hope this email finds you well. This is a friendly reminder that payment for the following invoice is due soon:</p>
            
            <div class="details">
                <table>
                    <tr>
                        <th>Invoice Number:</th>
                        <td>{{ $payment->invoice_number }}</td>
                    </tr>
                    <tr>
                        <th>Contract Number:</th>
                        <td>{{ $payment->contract->contract_number }}</td>
                    </tr>
                    <tr>
                        <th>Amount Due:</th>
                        <td>{{ number_format($payment->payment_amount, 2) }} SAR</td>
                    </tr>
                    <tr>
                        <th>Due Date:</th>
                        <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('F j, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Days Remaining:</th>
                        <td>{{ \Carbon\Carbon::parse($payment->due_date)->diffInDays(\Carbon\Carbon::now()) }}</td>
                    </tr>
                </table>
            </div>
            
            <p>Please ensure that payment is made by the due date to avoid any late fees or service interruptions. If you have already made this payment, please disregard this reminder.</p>
            
            <p>For your convenience, you can make payment through any of the following methods:</p>
            
            <ul>
                <li>Bank Transfer</li>
                <li>Credit Card Payment</li>
                <li>Online Payment Portal</li>
            </ul>
            
            <p>If you have any questions or concerns regarding this invoice, please don't hesitate to contact our finance department at finance@spiderweb.com or call us at +123-456-7890.</p>
            
            <p>Thank you for your prompt attention to this matter.</p>
            
            <p>Best regards,<br>
            Finance Department<br>
            Spider Web</p>
            
            <a href="{{ url('/customer/payments/' . $payment->id) }}" class="button">View Invoice Online</a>
        </div>
        
        <div class="footer">
            <p>This is an automated reminder. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Spider Web. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
