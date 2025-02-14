<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Contract Details</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .contract-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        .label {
            font-weight: bold;
            color: #555;
            min-width: 200px;
        }
        .value {
            color: #333;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .signatures {
            margin-top: 50px;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="contract-title">
            Contract Agreement
            <br>
            عقد اتفاق
        </div>
        <div>Contract #{{ $contract->contract_number }}</div>
        <div>Date: {{ $contract->created_at->format('Y-m-d') }}</div>
    </div>

    <div class="section">
        <div class="section-title">Client Information / معلومات العميل</div>
        <div class="info-row">
            <span class="label">Name / الاسم:</span>
            <span class="value">{{ $contract->client->name }}</span>
        </div>
        <div class="info-row">
            <span class="label">Phone / رقم الهاتف:</span>
            <span class="value">{{ $contract->client->phone }}</span>
        </div>
        @if($contract->client->email)
        <div class="info-row">
            <span class="label">Email / البريد الإلكتروني:</span>
            <span class="value">{{ $contract->client->email }}</span>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Contract Details / تفاصيل العقد</div>
        <div class="info-row">
            <span class="label">Contract Type / نوع العقد:</span>
            <span class="value">{{ $contract->type->name }}</span>
        </div>
        <div class="info-row">
            <span class="label">Start Date / تاريخ البداية:</span>
            <span class="value">{{ $contract->contract_start_date->format('Y-m-d') }}</span>
        </div>
        <div class="info-row">
            <span class="label">End Date / تاريخ النهاية:</span>
            <span class="value">{{ $contract->contract_end_date->format('Y-m-d') }}</span>
        </div>
        <div class="info-row">
            <span class="label">Total Amount / المبلغ الإجمالي:</span>
            <span class="value">{{ number_format($contract->total_amount, 2) }} SAR</span>
        </div>
        
    </div>

    @if($contract->notes)
    <div class="section">
        <div class="section-title">Notes / ملاحظات</div>
        <div class="info-row">
            <span class="value">{{ $contract->notes }}</span>
        </div>
    </div>
    @endif

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Client Signature / توقيع العميل</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Company Signature / توقيع الشركة</div>
        </div>
    </div>

    <div class="footer">
        <p>This document was automatically generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>تم إنشاء هذا المستند تلقائيًا في {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
