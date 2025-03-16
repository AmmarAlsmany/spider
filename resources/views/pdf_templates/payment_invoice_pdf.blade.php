<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Tax Invoice</title>
    <style>
        @page {
            margin: 1cm;
        }

        @font-face {
            font-family: 'Cairo';
            src: url({{ storage_path('fonts/Cairo-Regular.ttf') }}) format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Cairo';
            src: url({{ storage_path('fonts/Cairo-Medium.ttf') }}) format("truetype");
            font-weight: 500;
            font-style: normal;
        }

        @font-face {
            font-family: 'Cairo';
            src: url({{ storage_path('fonts/Cairo-SemiBold.ttf') }}) format("truetype");
            font-weight: 600;
            font-style: normal;
        }

        @font-face {
            font-family: 'Cairo';
            src: url({{ storage_path('fonts/Cairo-Bold.ttf') }}) format("truetype");
            font-weight: 700;
            font-style: normal;
        }

        body {
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        .invoice-container {
            background: white;
            padding: 1rem;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .invoice-header {
            background: #ffff00;
            color: #000;
            padding: 0.5rem;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .info-table {
            width: 100%;
            margin-bottom: 1rem;
            border-collapse: collapse;
        }

        .info-table th,
        .info-table td {
            padding: 0.5rem;
            border: 1px solid #000;
            text-align: right;
        }

        .invoice-details-header {
            background: #ffff00;
            color: #000;
            padding: 0.5rem;
            margin: 1rem 0;
            text-align: center;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td {
            padding: 0.5rem;
            border: 1px solid #000;
            text-align: right;
        }

        .details-table th {
            background-color: #f0f0f0;
            font-weight: 600;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .summary-table td {
            padding: 0.5rem;
            border: 1px solid #000;
            text-align: right;
        }

        .company-logo {
            text-align: center;
            margin-bottom: 1rem;
        }

        .company-logo img {
            max-width: 70px;
            height: auto;
        }

        .company-logo h3 {
            margin: 5px 0;
            font-size: 16px;
            font-weight: 600;
        }

        .qr-section {
            text-align: center;
            margin-top: 1rem;
        }

        .qr-section p {
            margin: 5px 0;
            font-size: 14px;
        }

        .signature-section {
            margin-top: 1rem;
            text-align: center;
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
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="company-logo">
            <img src="{{ public_path('backend/assets/images/logo-icon.png') }}" alt="Spider Web Logo" style="max-width: 70px;">
            <h3>Spider Web For Pest Control</h3>
            <h3>خيوط العنكبوت لمكافحة الحشرات</h3>
        </div>

        <div class="invoice-header">
            فاتورة ضريبية
        </div>

        <table class="info-table">
            <tr>
                <td>From/ Spider Web for Pest Control</td>
                <td>من/ شركة خيوط العنكبوت لمكافحة الحشرات</td>
            </tr>
            <tr>
                <td>العنوان / {{ $payment->company->address_ar ?? '6410 انس بن مالك - حي الياسمين - وحدة رقم 1 - الرياض 13325 - 3504' }}</td>
                <td>Address / {{ $payment->company->address ?? '6410 Anas bin Malik - Al-Yasmeen District - Unit No. 1 - Riyadh 13325 - 3504' }}</td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td>تاريخ اصدار الفاتورة</td>
                <td>رقم الفاتورة</td>
                <td>تاريخ الاستحقاق</td>
                <td>الرقم الضريبي</td>
            </tr>
            <tr>
                <td>{{ \Carbon\Carbon::parse($payment->invoice_date ?? $payment->created_at)->format('d/m/Y') }}</td>
                <td>{{ str_pad($payment->invoice_number ?? $payment->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ \Carbon\Carbon::parse($payment->due_date ?? $payment->created_at->addDays(30))->format('d/m/Y') }}</td>
                <td>{{ $payment->company->tax_number ?? '310152424500003' }}</td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td>/To</td>
                <td>الى/ شركة {{ $payment->customer->name_ar ?? ($payment->customer->name ?? 'الفرازدق التجارية') }}</td>
            </tr>
            <tr>
                <td>Address/</td>
                <td>العنوان / {{ $payment->customer->address ?? 'Hy Al-Frazd - Unit No. 134 - Riyadh 13313' }}</td>
            </tr>
            <tr>
                <td>Tax Number/</td>
                <td>الرقم الضريبي / {{ $payment->customer->tax_number ?? 'NA' }}</td>
            </tr>
        </table>

        <div class="invoice-details-header">
            بيان الفاتورة
        </div>

        <table class="details-table">
            <thead>
                <tr>
                    <th>الإجمالي</th>
                    <th>سعر الوحدة</th>
                    <th>الوصف</th>
                    <th>البند</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>ريال سعودي {{ number_format($payment->payment_amount, 2) }}</td>
                    <td>ريال سعودي {{ number_format($payment->payment_amount, 2) }}</td>
                    <td>الدفعة رقم {{ $payment->payment_number }} - من اصل {{ $payment->contract->number_Payments }}</td>
                    <td>مكافحة حشرات - {{ $payment->contract->type->name }}</td>
                </tr>
            </tbody>
        </table>

        <table class="summary-table">
            <tr>
                <td>Contract Number</td>
                <td>{{ $payment->contract->contract_number }}</td>
                <td>رقم العقد</td>
            </tr>
            <tr>
                <td>Service Statement</td>
                <td>مكافحة الحشرات - {{ $payment->contract->type->name }}</td>
                <td>بيان الخدمة</td>
            </tr>
            <tr>
                <td>Total contract price without VAT</td>
                <td>ريال سعودي {{ number_format($payment->payment_amount / 1.15, 2) }}</td>
                <td>إجمالي مبلغ العقد بدون الضريبة</td>
            </tr>
            <tr>
                <td>VAT 15%</td>
                <td>ريال سعودي {{ number_format($payment->payment_amount / 1.15 * 0.15, 2) }}</td>
                <td>قيمة الضريبة</td>
            </tr>
            <tr>
                <td>Total contract price including VAT</td>
                <td>ريال سعودي {{ number_format($payment->payment_amount, 2) }}</td>
                <td>الإجمالي مبلغ العقد شامل الضريبة</td>
            </tr>
        </table>

        <div class="qr-section">
            <p>يمكنكم إصدار المبلغ المذكور أعلاه عن طريق:</p>
            <p>(1) شيك باسم شركة خيوط العنكبوت لمكافحة الحشرات</p>
            <p>(2) SPIDER WEB CO. SA6905000068201414261000 تحويل المبلغ على حساب (الانماء) رقم:</p>
            <div style="margin: 0 auto; width: 150px; height: 150px;">
                {!! str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $qrCode) !!}
            </div>
        </div>

        <div class="signature-section">
            <table class="info-table">
                <tr>
                    <td>توقيع استلام العميل:</td>
                </tr>
                <tr>
                    <td style="height: 50px;"></td>
                </tr>
            </table>
            
            <table class="info-table" style="margin-top: 10px;">
                <tr>
                    <td>مسؤول المبيعات:</td>
                </tr>
                <tr>
                    <td style="height: 50px;"></td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
