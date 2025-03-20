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
                <a href="{{ route('payments.pdf', $payment->id) }}" class="btn btn-primary">
                    <i class="bx bx-download"></i> Download PDF
                </a>
                <button onclick="window.print()" class="btn btn-info">
                    <i class="bx bx-printer"></i> Print Invoice
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
            </div>
        </div>
        <div class="container">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

                body {
                    font-family: 'Cairo', sans-serif;
                }

                .invoice-container {
                    background: white;
                    border-radius: 0px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
                    font-weight: bold;
                    font-size: 1.1rem;
                }

                .info-table {
                    width: 100%;
                    margin-bottom: 0.5rem;
                    border-collapse: collapse;
                }

                .info-table th,
                .info-table td {
                    padding: 0.25rem;
                    border: 1px solid #000;
                    text-align: right;
                    font-size: 0.9rem;
                }

                .invoice-details-header {
                    background: #ffff00;
                    color: #000;
                    padding: 0.25rem;
                    margin: 0.5rem 0;
                    text-align: center;
                    font-weight: bold;
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
                    font-size: 0.9rem;
                }

                .details-table th {
                    background-color: #f0f0f0;
                }

                .summary-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 0.5rem;
                }

                .summary-table td {
                    padding: 0.25rem;
                    border: 1px solid #000;
                    text-align: right;
                    font-size: 0.9rem;
                }

                .qr-section {
                    text-align: center;
                    margin-top: 0.5rem;
                    font-size: 0.9rem;
                }

                .qr-section p {
                    margin: 0.1rem 0;
                }

                .signature-section {
                    margin-top: 0.5rem;
                    text-align: center;
                }

                /* Print styles */
                @media print {
                    @page {
                        size: A4;
                        margin: 0.5cm;
                    }

                    body {
                        padding: 0;
                        background: white;
                        font-size: 12px;
                    }

                    .invoice-container {
                        box-shadow: none;
                        padding: 0;
                    }

                    .page-breadcrumb {
                        display: none !important;
                    }

                    /* Hide dashboard elements */
                    .sidebar-wrapper,
                    .topbar,
                    .page-wrapper>.page-content> :not(.container),
                    .page-footer,
                    .back-to-top,
                    .switcher-wrapper,
                    .overlay {
                        display: none !important;
                    }

                    /* Reset page structure for printing */
                    .page-wrapper {
                        margin-left: 0 !important;
                        padding-top: 0 !important;
                    }

                    .page-content {
                        padding: 0 !important;
                        margin: 0 !important;
                    }

                    /* Ensure the invoice takes the full page */
                    .container {
                        max-width: 100% !important;
                        width: 100% !important;
                        padding: 0 !important;
                        margin: 0 !important;
                    }

                    /* Force background colors to print */
                    .invoice-header,
                    .invoice-details-header {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }

                    /* Reduce spacing for print */
                    .info-table,
                    .details-table,
                    .summary-table {
                        margin-bottom: 0.2rem;
                    }

                    .info-table th,
                    .info-table td,
                    .details-table th,
                    .details-table td,
                    .summary-table td {
                        padding: 0.15rem;
                        font-size: 0.8rem;
                    }

                    .company-logo {
                        margin-bottom: 0.3rem;
                    }

                    .company-logo img {
                        max-width: 60px;
                    }

                    .company-logo h3 {
                        margin: 0.1rem 0;
                        font-size: 1rem;
                    }

                    .signature-section table td {
                        height: 30px !important;
                    }
                }

                .company-logo {
                    text-align: center;
                    margin-bottom: 0.5rem;
                }

                .company-logo img {
                    max-width: 200px;
                    height: auto;
                }

                .company-logo h3 {
                    margin: 0.2rem 0;
                    font-size: 1.1rem;
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

                /* Optimize signature section */
                .signature-section .info-table td {
                    height: 40px;
                }

                /* Display signatures in a row instead of column */
                .signature-container {
                    display: flex;
                    justify-content: space-between;
                    margin-top: 0.5rem;
                }

                .signature-container .signature-box {
                    width: 48%;
                }
            </style>

            <div class="invoice-container">
                <div class="company-logo">
                    <img src="{{ asset('backend/assets/images/logo-icon.png') }}" alt="Spider Web Logo"
                        style="max-width: 70px;">
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
                        <td>تاريخ اصدار الفاتورة</td>
                        <td>رقم الفاتورة</td>
                        <td>تاريخ الاستحقاق</td>
                        <td>الرقم الضريبي</td>
                    </tr>
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($payment->invoice_date ?? $payment->created_at)->format('d/m/Y') }}
                        </td>
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
                            <td>ريال سعودي {{ number_format($payment->payment_amount / 1.15, 2) }}</td>
                            <td>ريال سعودي {{ number_format($payment->payment_amount / 1.15, 2) }}</td>
                            <td>الدفعة رقم {{ $payment->payment_number }} - من اصل
                                {{ $payment->total_payments }} </td>
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

                <div class="row">
                    <div class="col-md-6">
                        <div class="qr-section">
                            <p>يمكنكم إصدار المبلغ المذكور أعلاه عن طريق:</p>
                            <p>(1) شيك باسم شركة خيوط العنكبوت لمكافحة الحشرات</p>
                            <p>(2) SPIDER WEB CO. SA6905000068201414261000 تحويل المبلغ على حساب (الانماء) رقم:</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="qrcode" style="margin: 0 auto; width: 100px; height: 100px;"></div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        new QRCode(document.getElementById("qrcode"), {
                            text: "{{ $payment->company->name ?? 'Spider Web Co.' }}\nTAX: {{ $payment->company->tax_number ?? '310152424500003' }}\nInvoice: {{ str_pad($payment->invoice_number ?? $payment->id, 5, '0', STR_PAD_LEFT) }}\nDate: {{ \Carbon\Carbon::parse($payment->invoice_date ?? $payment->created_at)->format('Y-m-d') }}\nTotal: {{ number_format($payment->payment_amount, 2) }} SAR",
                            width: 100,
                            height: 100,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    });
                </script>

                <div class="signature-container">
                    <div class="signature-box">
                        <table class="info-table">
                            <tr>
                                <td>توقيع استلام العميل:</td>
                            </tr>
                            <tr>
                                <td style="height: 40px;"></td>
                            </tr>
                        </table>
                    </div>

                    <div class="signature-box">
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
        </div>
    </div>
@endsection
