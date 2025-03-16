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
                font-weight: bold;
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
                font-weight: bold;
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

            .qr-section {
                text-align: center;
                margin-top: 1rem;
            }

            .signature-section {
                margin-top: 1rem;
                text-align: center;
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
                
                /* Hide dashboard elements */
                .sidebar-wrapper, 
                .topbar, 
                .page-wrapper > .page-content > :not(.container),
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
                .invoice-header, .invoice-details-header {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
            }

            .company-logo {
                text-align: center;
                margin-bottom: 1rem;
            }

            .company-logo img {
                max-width: 250px;
                height: auto;
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

        <div class="invoice-container">
            <div class="company-logo">
                <img src="{{ asset('backend/assets/images/logo-icon.png') }}" alt="Spider Web Logo" style="max-width: 70px;">
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
                    <td>الى/ شركة {{ $payment->customer->name_ar ?? $payment->customer->name ?? 'الفرازدق التجارية' }}</td>
                </tr>
                <tr>
                    <td>Address/</td>
                    <td>العنوان / {{$payment->customer->address ?? 'Hy Al-Frazd - Unit No. 134 - Riyadh 13313' }}</td>
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
                        <td> الدفعة رقم {{ $payment->payment_number }} - من اصل {{ $payment->contract->number_Payments }} </td>
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
                {{-- <tr>
                    <td>leverage the amount</td>
                    <td>{{ convertNumberToArabicWords($payment->payment_amount) }}</td>
                    <td>المبلغ بالحروف</td>
                </tr> --}}
            </table>
            
            <div class="qr-section">
                <p>يمكنكم إصدار المبلغ المذكور أعلاه عن طريق:</p>
                <p>(1) شيك باسم شركة خيوط العنكبوت لمكافحة الحشرات</p>
                <p>(2) SPIDER WEB CO. SA6905000068201414261000  تحويل المبلغ على حساب (الانماء) رقم:</p>
                <div id="qrcode" style="margin: 0 auto; width: 150px; height: 150px;"></div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        new QRCode(document.getElementById("qrcode"), {
                            text: "{{ $payment->company->name ?? 'Spider Web Co.' }}\nTAX: {{ $payment->company->tax_number ?? '310152424500003' }}\nInvoice: {{ str_pad($payment->invoice_number ?? $payment->id, 5, '0', STR_PAD_LEFT) }}\nDate: {{ \Carbon\Carbon::parse($payment->invoice_date ?? $payment->created_at)->format('Y-m-d') }}\nTotal: {{ number_format($payment->payment_amount + ($payment->payment_amount * 0.15), 2) }} SAR",
                            width: 150,
                            height: 150,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    }); 
                </script>
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
    </div>
</div>
@endsection