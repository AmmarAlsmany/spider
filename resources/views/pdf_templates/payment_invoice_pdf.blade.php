<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Tax Invoice</title>
    <style>
        /* Reset all margins and paddings */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            /* Use a standard font instead of importing */
            font-size: 8pt;
            /* Use points for more precise control */
            line-height: 1.1;
        }

        /* Fixed height container */
        .invoice-container {
            width: 100%;
            max-width: 800px;
            max-height: 1050px;
            /* Fixed height for A4 */
            overflow: hidden;
            /* Prevent overflow */
            margin: 0 auto;
            padding: 10px;
            position: relative;
        }

        .invoice-header {
            background: #ffff00;
            color: #000;
            padding: 2pt;
            margin-bottom: 3pt;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3pt;
        }

        th,
        td {
            border: 0.5pt solid #000;
            padding: 2pt;
            font-size: 8pt;
            text-align: right;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .company-logo {
            text-align: center;
            margin-bottom: 3pt;
        }

        .company-logo img {
            max-width: 40px;
            height: auto;
        }

        .company-logo h3 {
            margin: 1pt 0;
            font-size: 9pt;
        }

        .qr-section {
            text-align: center;
            margin-top: 3pt;
        }

        .qr-section p {
            margin: 1pt 0;
        }

        .signature-section {
            margin-top: 3pt;
        }

        /* Strict page settings */
        @page {
            size: 21cm 29.7cm;
            /* A4 size in cm */
            margin: 0.5cm;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Company Logo and Name -->
        <div class="company-logo">
            <img src="{{ public_path('backend/assets/images/logo-icon.png') }}" alt="Spider Web Logo">
            <h3>Spider Web For Pest Control</h3>
            <h3>خيوط العنكبوت لمكافحة الحشرات</h3>
        </div>

        <!-- Invoice Header -->
        <div class="invoice-header">فاتورة ضريبية</div>

        <!-- Company Info -->
        <table>
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

        <!-- Invoice Details -->
        <table>
            <tr>
                <td>تاريخ اصدار الفاتورة</td>
                <td>رقم الفاتورة</td>
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

        <!-- Customer Info -->
        <table>
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

        <!-- Invoice Statement Header -->
        <div class="invoice-header">بيان الفاتورة</div>

        <!-- Invoice Line Items -->
        <table>
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
                    <td>الدفعة رقم {{ $payment->payment_number }} - من اصل {{ $payment->contract->number_Payments }}
                    </td>
                    <td>مكافحة حشرات - {{ $payment->contract->type->name }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary Table -->
        <table>
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
                <td>ريال سعودي {{ number_format(($payment->payment_amount / 1.15) * 0.15, 2) }}</td>
                <td>قيمة الضريبة</td>
            </tr>
            <tr>
                <td>Total contract price including VAT</td>
                <td>ريال سعودي {{ number_format($payment->payment_amount, 2) }}</td>
                <td>الإجمالي مبلغ العقد شامل الضريبة</td>
            </tr>
        </table>

        <!-- Payment Instructions and QR Code -->
        <div class="qr-section">
            <p>يمكنكم إصدار المبلغ المذكور أعلاه عن طريق:</p>
            <p>(1) شيك باسم شركة خيوط العنكبوت لمكافحة الحشرات</p>
            <p>(2) SPIDER WEB CO. SA6905000068201414261000 تحويل المبلغ على حساب (الانماء) رقم:</p>

            <!-- QR Code -->
            <div style="margin: 0 auto; width: 70px; height: 70px;">
                {!! str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $qrCode) !!}
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <table>
                <tr>
                    <td>توقيع استلام العميل:</td>
                </tr>
                <tr>
                    <td style="height: 15px;"></td>
                </tr>
            </table>

            <table style="margin-top: 3pt;">
                <tr>
                    <td>مسؤول المبيعات:</td>
                </tr>
                <tr>
                    <td style="height: 15px;"></td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
