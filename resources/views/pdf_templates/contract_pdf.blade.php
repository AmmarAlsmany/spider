<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Contract Agreement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-logo img {
            max-height: 80px;
            margin-bottom: 10px;
        }

        .header-logo h2 {
            margin: 0;
            color: #2c3e50;
        }

        .contract-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 20px;
        }

        .contract-header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .contract-number {
            font-size: 18px;
            color: #34495e;
            font-weight: bold;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background-color: #fff;
        }

        th,
        td {
            border: 1px solid #e0e0e0;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .payment-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }

        .visit-schedule {
            margin-top: 20px;
        }

        .contract-footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .signatures {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            margin-bottom: 10px;
        }

        .branch-visits {
            margin-bottom: 20px;
        }

        .branch-name {
            color: #0056b3;
            margin-bottom: 5px;
            font-size: 14px;
            border-bottom: 1px solid #0056b3;
            padding-bottom: 3px;
        }

        .branch-address {
            color: #555;
            font-style: italic;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .branch-separator {
            height: 1px;
            background-color: #ddd;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="header-logo">
        <img src="{{ public_path('backend/assets/images/logo-img.png') }}" alt="Spider Web Services Logo" width="100"
            height="100">
        <h2>Spider Web Services</h2>
    </div>

    <div class="contract-header">
        <h1>SERVICE CONTRACT AGREEMENT</h1>
        <p class="contract-number">Contract Number: {{ $contract->contract_number }}</p>
    </div>

    <div class="section">
        <h3 class="section-title">Client Information</h3>
        <table>
            <tr>
                <th width="30%">Client Name</th>
                <td>{{ $contract->customer->name }}</td>
            </tr>
            <tr>
                <th>Phone</th>
                <td>{{ $contract->customer->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Mobile</th>
                <td>{{ $contract->customer->mobile ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $contract->customer->email ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3 class="section-title">Contract Details</h3>
        <table>
            <tr>
                <th width="30%">Contract Type</th>
                <td>{{ $contract->type->name ?? 'Standard' }}</td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td>{{ date('F d, Y', strtotime($contract->contract_start_date)) }}</td>
            </tr>
            <tr>
                <th>End Date</th>
                <td>{{ date('F d, Y', strtotime($contract->contract_end_date)) }}</td>
            </tr>
            <tr>
                <th>Duration</th>
                <td>{{ \Carbon\Carbon::parse($contract->contract_start_date)->diffInMonths($contract->contract_end_date) }}
                    months</td>
            </tr>
            <tr>
                <th>Status</th>
                <td><strong>{{ ucfirst($contract->contract_status) }}</strong></td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $contract->contract_description }}</td>
            </tr>
        </table>
    </div>

    {{-- if the contract type is equipment we need to show the equipment details --}}

    @if ($contract->type->name == 'Buy equipment')
        <div class="section">
            <h3 class="section-title">Equipment Details</h3>
            <table>
                <tr>
                    <th width="30%">Equipment Type</th>
                    <td>{{ $contract->equipment->equipmentType->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Model</th>
                    <td>{{ $contract->equipment->equipment_model ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Quantity</th>
                    <td>{{ $contract->equipment->equipment_quantity ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Unit Price</th>
                    <td>SAR {{ number_format($contract->equipment->unit_price ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th>Total Price (without VAT)</th>
                    <td>SAR {{ number_format($contract->equipment->total_price ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th>VAT Amount (15%)</th>
                    <td>SAR {{ number_format($contract->equipment->vat_amount ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th>Total with VAT</th>
                    <td>SAR {{ number_format($contract->equipment->total_with_vat ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>{{ $contract->equipment->equipment_description ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Warranty Period</th>
                    <td>{{ $contract->warranty ?? 0 }} months</td>
                </tr>
            </table>
        </div>
    @endif

    @if ($contract->branchs && count($contract->branchs) > 0)
        <div class="section">
            <h3 class="section-title">Service Locations</h3>
            <table>
                <thead>
                    <tr>
                        <th>Branch Name</th>
                        <th>Manager</th>
                        <th>Phone</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contract->branchs as $branch)
                        <tr>
                            <td>{{ $branch->branch_name }}</td>
                            <td>{{ $branch->branch_manager_name }}</td>
                            <td>{{ $branch->branch_manager_phone }}</td>
                            <td>{{ $branch->branch_address }}, {{ $branch->branch_city }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($contract->payments && count($contract->payments) > 0)
        <div class="section">
            <h3 class="section-title">Payment Information</h3>
            <div class="payment-info">
                <table>
                    <thead>
                        <tr>
                            <th>Payment Date</th>
                            <th>Amount without VAT</th>
                            <th>VAT</th>
                            <th>Amount with VAT</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contract->payments as $payment)
                            <tr>
                                <td>{{ date('F d, Y', strtotime($payment->due_date)) }}</td>
                                <td>{{ number_format($payment->payment_amount / 1.15, 2) }}</td>
                                <td>{{ number_format($payment->payment_amount - $payment->payment_amount / 1.15, 2) }}
                                </td>
                                <td>{{ number_format($payment->payment_amount, 2) }}</td>
                                <td>{{ ucfirst($payment->payment_status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($contract->visitSchedules && count($contract->visitSchedules) > 0)
        <div class="section">
            <h3 class="section-title">Visit Schedule</h3>

            @php
                // Group visits by branch
                $visitsByBranch = $contract->visitSchedules->groupBy('branch_id');
            @endphp

            @foreach ($visitsByBranch as $branchId => $branchVisits)
                @php
                    $branch = App\Models\branchs::find($branchId);
                @endphp

                <div class="branch-visits">
                    <h4 class="branch-name">Branch: {{ $branch->branch_name }}</h4>
                    <p class="branch-address">{{ $branch->branch_address }}, {{ $branch->branch_city }}</p>

                    <table class="visit-schedule">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Team</th>
                                <th>Team Recommendations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($branchVisits as $visit)
                                <tr>
                                    <td>{{ date('F d, Y', strtotime($visit->visit_date)) }}</td>
                                    <td>{{ $visit->visit_type }}</td>
                                    <td>{{ ucfirst($visit->status) }}</td>
                                    <td>{{ $visit->team->name }}</td>
                                    <td>{{ $visit->report->recommendations ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if (!$loop->last)
                    <div class="branch-separator"></div>
                @endif
            @endforeach
        </div>
    @endif

    <div class="section" style="page-break-inside: avoid;">
        <h3 class="section-title">Terms and Conditions / الشروط العامة</h3>
        <div class="contract-details" style="margin-top: -10px;">
            <div style="border: 1px solid #ddd; border-radius: 5px; overflow: hidden; margin-bottom: 15px;">
                <!-- Arabic Terms Header -->
                <div
                    style="background-color: #2c3e50; color: white; padding: 5px; text-align: center; font-weight: bold; font-size: 14px;">
                    الشروط والأحكام
                </div>

                <!-- Arabic Terms -->
                <div class="standard-terms"
                    style="direction: rtl; text-align: right; padding: 8px; font-family: 'Arial, sans-serif'; background-color: #f9f9f9; font-size: 9px;">
                    <ol style="padding-right: 15px; margin: 0;">
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            تلتزم سبايدر ويب باستخدام مواد ذات كفاءة عالية وعديمة الرائحة وآمنة جداً على الإنسان.
                        </li>
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            العقد لايشمل توفير المصائد بأنواعها وطوارد الطيور الا اذا ذكر ذلك في العقد. 
                        </li>
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            يجب أن تكون غرف التفتيش قابلة للفتح لضمان الحصول على أفضل النتائج.
                        </li>
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            تلتزم سبايدر ويب بزيارة مجانية بعد 15 يوم من الزيارة الأولى
                        </li>
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            يلتزم العميل بجدول الزيارات الزمني المحدد من قبل سبايدر ويب لضمان الحصول على أفضل النتائج
                            ولايحق له
                            المطالبة بالتعويض في حال تأجيل الزيارة أكثر من مرة.
                        </li>
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            العقد غير قابل للإلغاء بعد اعتمادة ومباشرة الزيارات.
                        </li>
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            يلتزم العميل أو من ينوب عنه بالتواجد مع الفرقة أثناء أداء الخدمة وبذلك تخلي سبايدر ويب
                            مسؤوليتها عن
                            فقدان محتويات المكان.
                        </li>
                        <li style="margin-bottom: 0px;">
                            عرض السعر المقدم يعتبر جزء من العقد ولا يمكن التخلي عنه.
                        </li>
                    </ol>
                </div>
            </div>

            <div style="border: 1px solid #ddd; border-radius: 5px; overflow: hidden; margin-bottom: 15px;">
                <!-- English Terms Header -->
                <div
                    style="background-color: #2c3e50; color: white; padding: 5px; text-align: center; font-weight: bold; font-size: 14px;">
                    Terms and Conditions
                </div>

                <!-- English Terms -->
                <div class="standard-terms-english" style="padding: 8px; background-color: #f9f9f9; font-size: 9px;">
                    <ol style="padding-left: 15px; margin: 0;">
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            Spider Web is committed to using materials of high efficiency, environmentally friendly and
                            very
                            safe for humans.
                        </li>
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            The contract does not include providing traps of all kinds and bird control materials unless
                            specified in the contract.
                        </li>
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            Inspection rooms must be accessible to ensure the best results.
                        </li>
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            Spider Web is committed to a free visit 15 days after the first visit.
                        </li>
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            The client is obligated to follow the schedule of visits set by Spider Web to ensure the
                            best
                            results and has no right to claim compensation if the visit is postponed more than once.
                        </li>
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            The contract is non-cancellable after approval and commencement of visits.
                        </li>
                        <li style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                            The client or representative must be present with the team during service delivery, thereby
                            relieving Spider Web of responsibility for loss of location contents.
                        </li>
                        <li style="margin-bottom: 0px;">
                            The price offer is part of the contract and cannot be waived.
                        </li>
                    </ol>
                </div>
            </div>

            @if ($contract->details)
                <div style="border: 1px solid #ddd; border-radius: 5px; overflow: hidden; margin-top: 15px;">
                    <div
                        style="background-color: #34495e; color: white; padding: 5px; text-align: center; font-weight: bold; font-size: 14px;">
                        Additional Terms / شروط إضافية
                    </div>
                    <div style="padding: 8px; background-color: #f9f9f9; font-size: 9px;">
                        {!! $contract->details !!}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p>Client Signature</p>
            <p>Date: _________________</p>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <p>Company Representative</p>
            <p>Date: _________________</p>
        </div>
    </div>

    <div class="contract-footer">
        <p>Generated on {{ date('F d, Y \a\t h:i A') }}</p>
        <p>This is an official contract document. Please retain for your records.</p>
    </div>
</body>

</html>
