<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Tax Invoice</title>
    <style>
        @page {
            margin: 0.5cm;
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
            font-size: 12px;
            line-height: 1.3;
        }

        .invoice-container {
            background: white;
            padding: 0.5rem;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .invoice-header {
            background: #ffff00;
            color: #000;
            padding: 0.25rem;
            margin-bottom: 0.5rem;
            text-align: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .info-table {
            width: 100%;
            margin-bottom: 0.4rem;
            border-collapse: collapse;
        }

        .info-table th,
        .info-table td {
            padding: 0.25rem;
            border: 1px solid #000;
            text-align: right;
            font-size: 0.85rem;
        }

        .invoice-details-header {
            background: #ffff00;
            color: #000;
            padding: 0.25rem;
            margin: 0.4rem 0;
            text-align: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td {
            padding: 0.25rem;
            border: 1px solid #000;
            text-align: right;
            font-size: 0.85rem;
        }

        .details-table th {
            background-color: #f0f0f0;
            font-weight: 600;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.4rem;
        }

        .summary-table td {
            padding: 0.25rem;
            border: 1px solid #000;
            text-align: right;
            font-size: 0.85rem;
        }

        .company-logo {
            text-align: center;
            margin-bottom: 0.4rem;
        }

        .company-logo img {
            max-width: 60px;
            height: auto;
        }

        .company-logo h3 {
            margin: 2px 0;
            font-size: 14px;
            font-weight: 600;
        }

        .qr-section {
            text-align: center;
            margin-top: 0.4rem;
        }

        .qr-section p {
            margin: 2px 0;
            font-size: 12px;
        }

        .signature-section {
            margin-top: 0.4rem;
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

        /* Display signatures in a row instead of column */
        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 0.4rem;
        }

        .signature-container .signature-box {
            width: 48%;
        }

        .signature-section .info-table td {
            height: 40px;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="company-logo">
            <img src="{{ public_path('backend/assets/images/logo-icon.png') }}" alt="Spider Web Logo"
                style="max-width: 60px;">
            <h3>Spider Web For Pest Control</h3>
            <h3>خيوط العنكبوت لمكافحة الحشرات</h3>
        </div>

        <div class="invoice-header">
            Claim Financial - مطالبة مالية
        </div>

        <table class="info-table">
            <tr>
                <td>From/ Spider Web for Pest Control</td>
                <td>من/ شركة خيوط العنكبوت لمكافحة الحشرات</td>
            </tr>
            <tr>
                <td>العنوان /
                    {{ $payment->company->address_ar ?? '6410 انس بن مالك - حي الياسمين - وحدة رقم 1 - الرياض 13325 - 3504' }}
                </td>
                <td>Address /
                    {{ $payment->company->address ?? '6410 Anas bin Malik - Al-Yasmeen District - Unit No. 1 - Riyadh 13325 - 3504' }}
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td>تاريخ اصدار المطالبة</td>
                <td>رقم المطالبة</td>
                <td>تاريخ الاستحقاق</td>
                <td>الرقم الضريبي</td>
            </tr>
            <tr>
                <td>{{ \Carbon\Carbon::parse($payment->invoice_date ?? $payment->created_at)->format('d/m/Y') }}</td>
                <td>{{ str_pad($payment->invoice_number ?? $payment->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ \Carbon\Carbon::parse($payment->due_date ?? $payment->created_at->addDays(30))->format('d/m/Y') }}
                </td>
                <td>{{ $payment->company->tax_number ?? '310152424500003' }}</td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td>/To</td>
                <td>الى/ شركة {{ $payment->customer->name_ar ?? ($payment->customer->name ?? 'الفرازدق التجارية') }}
                </td>
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
            Claim Details - بيان المطالبة
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
                    <td>ريال سعودي {{ number_format($payment->payment_amount / 1.15, 2) }}</td>
                    <td>ريال سعودي {{ number_format($payment->payment_amount / 1.15, 2) }}</td>
                    <td>الدفعة رقم {{ $payment->payment_number }} - من اصل {{ $payment->total_payments }}
                    </td>
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
                <td>إجمالي المبلغ بدون الضريبة</td>
            </tr>
            <tr>
                <td>VAT 15%</td>
                <td>ريال سعودي {{ number_format(($payment->payment_amount / 1.15) * 0.15, 2) }}</td>
                <td>قيمة الضريبة</td>
            </tr>
            <tr>
                <td>Total contract price including VAT</td>
                <td>ريال سعودي {{ number_format($payment->payment_amount, 2) }}</td>
                <td>إجمالي المبلغ شامل الضريبة</td>
            </tr>
            <tr>
                <td>leverage the amount</td>
                <td>{{ $words['arabic'] }}</td>
                <td>المبلغ بالحروف</td>
            </tr>
        </table>

        <div style="display: flex; flex-direction: row-reverse; justify-content: space-between; margin-top: 0.4rem;">
            <div style="width: 65%; text-align: right;">
                <div class="qr-section" style="text-align: right;">
                    <p>يمكنكم إصدار المبلغ المذكور أعلاه عن طريق:</p>
                    <p>(1) شيك باسم شركة خيوط العنكبوت لمكافحة الحشرات</p>
                    <p>(2) SPIDER WEB CO. SA6905000068201414261000 تحويل المبلغ على حساب (الانماء) رقم:</p>
                </div>
            </div>
            <div style="width: 30%; display: flex; justify-content: center; align-items: center;">
                <div style="width: 100px; height: 100px;">
                    {!!  preg_replace('/<\?xml.*\?>/', '', $qrCode) !!}
                </div>
            </div>
        </div>

        <div class="signature-container"
            style="display: flex; flex-direction: row; justify-content: space-between; margin-top: 0.4rem;">
            <div class="signature-box" style="width: 48%;">
                <table class="info-table">
                    <tr>
                        <td>توقيع استلام العميل:</td>
                    </tr>
                    <tr>
                        <td style="height: 40px;"></td>
                    </tr>
                </table>
            </div>

            <div class="signature-box" style="width: 48%;">
                <table class="info-table">
                    <tr>
                        <td>مسؤول المبيعات:</td>
                    </tr>
                    <tr>
                        <td style="height: 40px;"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
