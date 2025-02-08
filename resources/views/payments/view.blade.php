@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">View Invoice</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">View Invoice</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bx bx-printer"></i> Print
            </button>
        </div>
    </div>
    <div class="container">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

            body {
                font-family: 'Cairo', sans-serif;
                background-color: #f8f9fa;
                padding: 1rem;
            }

            @media (min-width: 768px) {
                body {
                    padding: 2rem;
                }
            }

            .invoice-container {
                background: white;
                border-radius: 15px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                padding: 1rem;
                width: 100%;
                max-width: 800px;
                margin: 0 auto;
            }

            @media (min-width: 640px) {
                .invoice-container {
                    padding: 2rem;
                }
            }

            .invoice-header {
                background: linear-gradient(135deg, #ffd700, #ffeb3b);
                color: #000;
                border-radius: 8px;
                padding: 1rem;
                margin-bottom: 1.5rem;
                text-align: center;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .info-table {
                width: 100%;
                margin-bottom: 1.5rem;
                border-collapse: separate;
                border-spacing: 0;
                border-radius: 8px;
                overflow-x: auto;
                display: block;
            }

            @media (min-width: 768px) {
                .info-table {
                    display: table;
                }
            }

            .info-table th,
            .info-table td {
                padding: 0.5rem;
                border: 1px solid #e2e8f0;
                white-space: nowrap;
            }

            @media (min-width: 640px) {
                .info-table th,
                .info-table td {
                    padding: 0.75rem 1rem;
                    white-space: normal;
                }
            }

            .details-section {
                background-color: #fff;
                border-radius: 8px;
                padding: 1rem;
                margin-bottom: 1.5rem;
                border: 1px solid #e2e8f0;
            }

            @media (min-width: 640px) {
                .details-section {
                    padding: 1.5rem;
                }
            }

            .grid-cols-2 {
                display: grid;
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            @media (min-width: 640px) {
                .grid-cols-2 {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 2rem;
                }
            }

            .total-row {
                background-color: #f7fafc;
                font-weight: 600;
            }

            .qr-section {
                text-align: center;
                margin-top: 1.5rem;
                padding: 1rem;
                background-color: #f8fafc;
                border-radius: 8px;
            }

            .signature-section {
                margin-top: 1.5rem;
                padding-top: 1rem;
                border-top: 2px dashed #e2e8f0;
            }

            .signature-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            @media (min-width: 640px) {
                .signature-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 2rem;
                }
            }

            .payment-info {
                background-color: #fff7e6;
                border-radius: 8px;
                padding: 1rem;
                margin-top: 1.5rem;
            }

            .amount-in-words {
                background-color: #f0f9ff;
                padding: 0.75rem;
                border-radius: 8px;
                margin: 1rem 0;
                font-weight: 600;
                font-size: 0.875rem;
            }

            @media (min-width: 640px) {
                .amount-in-words {
                    padding: 1rem;
                    font-size: 1rem;
                }
            }

            /* Print styles */
            @media print {
                body {
                    padding: 0;
                    background: white;
                }

                .invoice-container {
                    box-shadow: none;
                    padding: 0;
                }

                .page-breadcrumb {
                    display: none !important;
                }
            }
        </style>

        <div class="invoice-container">
            <div class="invoice-header">
                <h1 class="mb-2 text-2xl font-bold text-center">فاتورة ضريبة</h1>
                <h1 class="mb-2 text-xl font-bold text-center">Tax Invoice</h1>
                <p class="text-lg">رقم الفاتورة / Invoice Number: {{ $payment->invoice_number }}</p>
                <p class="text-sm">تاريخ الفاتورة / Invoice Date: {{ $payment->created_at->format('Y-m-d') }}</p>
            </div>

            <div class="details-section">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h3 class="mb-2 font-bold">From/ Spider Web for Pest Control</h3>
                        <p class="text-sm">من/ شركة خيوط العنكبوت لمكافحة الحشرات</p>
                        <p>Address / <a href="https://www.google.com/maps/search/?api=1&query=6410+Anas+bin+Malik+Al+Yasmeen+District+Riyadh+13325" target="_blank" class="text-blue-600 hover:text-blue-800">6410 Anas bin Malik - Al-Yasmeen District - Unit No. 1 - Riyadh 13325 - 3504</a></p>
                        <p class="mt-2">
                            Phone / <a href="tel:+966920033095" class="text-blue-600 hover:text-blue-800">920033095</a>
                        </p>
                    </div>
                    <div>
                        <h3 class="mb-2 font-bold">To / {{ $payment->customer->name }}</h3>
                        <p class="text-sm">إلى / {{ $payment->customer->name_ar }}</p>
                        <p>Address / <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($payment->customer->address) }}" target="_blank" class="text-blue-600 hover:text-blue-800">{{ $payment->customer->address }}</a></p>
                        @if($payment->customer->phone)
                        <p class="mt-2">
                            Phone / <a href="tel:{{ $payment->customer->phone }}" class="text-blue-600 hover:text-blue-800">{{ $payment->customer->phone }}</a>
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="details-section">
                <table class="w-full info-table">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="text-right">Description / الوصف</th>
                            <th class="text-right">Amount / المبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-right">
                                {{ $payment->payment_description ?: 'Payment for Contract #' . $payment->contract->contract_number }}
                            </td>
                            <td class="text-right">SAR {{ number_format($payment->payment_amount, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td class="font-bold text-right">Total Amount / المبلغ الإجمالي</td>
                            <td class="font-bold text-right">SAR {{ number_format($payment->payment_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="amount-in-words">
                    Amount in words: 
                    @php
                        function numberToWords($number) {
                            $ones = array(
                                0 => "", 1 => "one", 2 => "two", 3 => "three", 4 => "four",
                                5 => "five", 6 => "six", 7 => "seven", 8 => "eight", 9 => "nine",
                                10 => "ten", 11 => "eleven", 12 => "twelve", 13 => "thirteen",
                                14 => "fourteen", 15 => "fifteen", 16 => "sixteen",
                                17 => "seventeen", 18 => "eighteen", 19 => "nineteen"
                            );
                            $tens = array(
                                2 => "twenty", 3 => "thirty", 4 => "forty", 5 => "fifty",
                                6 => "sixty", 7 => "seventy", 8 => "eighty", 9 => "ninety"
                            );
                            
                            if ($number == 0) {
                                return "zero";
                            }
                            
                            $words = "";
                            
                            if ($number >= 1000) {
                                $words .= numberToWords(floor($number/1000)) . " thousand ";
                                $number %= 1000;
                            }
                            
                            if ($number >= 100) {
                                $words .= $ones[floor($number/100)] . " hundred ";
                                $number %= 100;
                            }
                            
                            if ($number >= 20) {
                                $words .= $tens[floor($number/10)] . " ";
                                $number %= 10;
                            }
                            
                            if ($number > 0) {
                                $words .= $ones[$number];
                            }
                            
                            return trim($words);
                        }
                        
                        $amount = (int)$payment->payment_amount;
                        $words = ucwords(numberToWords($amount));
                    @endphp
                    {{ $words }} Saudi Riyals Only
                </div>
            </div>

            <div class="payment-info">
                <p class="text-sm">Payment Status: <span class="font-bold {{ $payment->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                    {{ ucfirst($payment->payment_status) }}
                </span></p>
                @if($payment->payment_status === 'paid')
                <p class="text-sm">Paid Date: {{ $payment->paid_at }}</p>
                <p class="text-sm">Payment Method: {{ ucfirst($payment->payment_method) }}</p>
                @else
                <p class="text-sm">Due Date: {{ $payment->due_date }}</p>
                @endif
            </div>

            <div class="mt-4 payment-info">
                <p class="text-sm">يمكنكم إصدار المبلغ المذكور أعلاه عن طريق:</p>
                <p class="text-sm">1) إصدار الشيك باسم شركة خيوط العنكبوت لمكافحة الحشرات</p>
                <p class="text-sm">2) تحويل المبلغ على حساب (الإنماء) رقم: SA6905000068201414261000</p>
            </div>

            <div class="signature-section">
                <div class="signature-grid">
                    <div class="text-center">
                        <p class="mb-8">Customer Signature / توقيع العميل</p>
                        <div class="mx-auto w-48 border-t-2 border-gray-300"></div>
                    </div>
                    <div class="text-center">
                        <p class="mb-8">Company Signature / توقيع الشركة</p>
                        <div class="mx-auto w-48 border-t-2 border-gray-300"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection