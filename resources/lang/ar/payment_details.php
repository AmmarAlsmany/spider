<?php

return [
    'title' => 'تفاصيل الدفع',
    'breadcrumb' => [
        'contracts' => 'العقود',
        'contract_number' => 'عقد رقم :number',
        'payments' => 'المدفوعات'
    ],
    'summary' => [
        'total_value' => 'القيمة الإجمالية للعقد',
        'contract_number' => 'عقد رقم :number',
        'paid_amount' => 'المبلغ المدفوع',
        'pending_amount' => 'المبلغ المعلق',
        'overdue_amount' => 'المبلغ المتأخر',
        'currency' => 'ريال'
    ],
    'payment_progress' => [
        'title' => 'تقدم الدفع',
        'paid_out_of' => 'مدفوع من أصل'
    ],
    'payment_schedule' => [
        'title' => 'جدول الدفع',
        'columns' => [
            'payment_date' => 'تاريخ الدفع',
            'amount' => 'المبلغ',
            'status' => 'الحالة',
            'paid_at' => 'تم الدفع في',
            'actions' => 'الإجراءات'
        ],
        'status' => [
            'paid' => 'مدفوع',
            'unpaid' => 'غير مدفوع',
            'overdue' => 'متأخر',
            'postponed' => 'مؤجل',
            'pending' => 'معلق'
        ],
        'no_payments' => 'لا توجد سجلات دفع.',
        'paid_on' => 'تم الدفع في :date',
        'last_request' => 'آخر طلب',
        'approved_on' => 'تمت الموافقة في',
        'request_pending' => 'الطلب معلق',
        'request_postpone' => 'طلب تأجيل',
        'details' => 'عرض التفاصيل'
    ],
    'actions' => [
        'postpone' => 'تأجيل الدفع',
        'view_receipt' => 'عرض الإيصال'
    ],
    'postpone_modal' => [
        'title' => 'تأجيل الدفع',
        'current_date' => 'تاريخ الاستحقاق الحالي',
        'new_date' => 'تاريخ الاستحقاق الجديد',
        'reason' => 'سبب التأجيل',
        'submit' => 'تقديم الطلب',
        'cancel' => 'إلغاء',
        'payment_amount' => 'مبلغ الدفعة'
    ],
    'payment_details_modal' => [
        'title' => 'تفاصيل الدفعة',
        'close' => 'إغلاق'
    ]
];
