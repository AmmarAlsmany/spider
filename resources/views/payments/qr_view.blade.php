<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Details</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
        body {
            font-family: 'Cairo', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-md">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold mb-2">فاتورة ضريبة</h1>
                <h2 class="text-xl font-bold">Tax Invoice</h2>
            </div>
            
            <div class="space-y-4">
                <div class="border-b pb-2">
                    <div class="flex justify-between">
                        <span class="font-semibold">Invoice Number:</span>
                        <span>{{ $payment->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">رقم الفاتورة:</span>
                        <span>{{ $payment->invoice_number }}</span>
                    </div>
                </div>

                <div class="border-b pb-2">
                    <div class="flex justify-between">
                        <span class="font-semibold">Amount:</span>
                        <span>{{ number_format($payment->payment_amount, 2) }} SAR</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">المبلغ:</span>
                        <span>{{ number_format($payment->payment_amount, 2) }} ريال</span>
                    </div>
                </div>

                <div class="border-b pb-2">
                    <div class="flex justify-between">
                        <span class="font-semibold">Date:</span>
                        <span>{{ $payment->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">التاريخ:</span>
                        <span>{{ $payment->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>

                @if($payment->customer)
                <div class="border-b pb-2">
                    <div class="flex justify-between">
                        <span class="font-semibold">Customer:</span>
                        <span>{{ $payment->customer->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">العميل:</span>
                        <span>{{ $payment->customer->name }}</span>
                    </div>
                </div>
                @endif

                <div class="mt-6 text-center text-sm text-gray-500">
                    <p>This is a digital copy of the invoice</p>
                    <p>هذه نسخة رقمية من الفاتورة</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
