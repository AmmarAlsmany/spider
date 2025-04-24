<?php

return [
    // Page titles and breadcrumbs
    'client_profile' => 'ملف العميل',
    'contracts' => 'العقود',
    
    // Empty state
    'no_contracts' => [
        'title' => 'لا توجد عقود',
        'message' => 'ليس لديك أي عقود نشطة في الوقت الحالي.'
    ],
    
    // Contract card
    'contract_number' => 'عقد رقم :number',
    'details' => [
        'start_date' => 'تاريخ البدء',
        'property_type' => 'نوع العقار',
        'contract_type' => 'نوع العقد',
        'contract_price' => 'سعر العقد',
        'payment_type' => 'طريقة الدفع',
        'number_of_payments' => 'عدد الدفعات',
        'multiple_branches' => 'فروع متعددة',
        'yes' => 'نعم',
        'no' => 'لا',
        'description' => 'وصف العقد',
        'no_description' => 'لا يوجد وصف متاح.'
    ],
    
    // Update request alerts
    'update_request' => [
        'pending' => 'طلب التحديث الخاص بك قيد المراجعة.',
        'status' => 'تم :status طلب التحديث'
    ],
    
    // Actions and buttons
    'actions' => [
        'approve' => [
            'button' => 'الموافقة على العقد',
            'title' => 'الموافقة على العقد رقم :number'
        ],
        'reject' => [
            'button' => 'عدم الموافقة',
            'title' => 'عدم الموافقة على العقد رقم :number',
            'reason_label' => 'سبب عدم الموافقة',
            'submit' => 'إرسال'
        ],
        'view_details' => 'عرض التفاصيل',
        'request_update' => 'طلب تحديث',
        'payment_details' => 'تفاصيل الدفع',
        'visit_details' => 'تفاصيل الزيارات',
        'contract_history' => 'سجل العقد',
        'update_history' => 'طلبات التحديث',
        'download' => 'تحميل العقد'
    ],
    
    // Modal titles
    'modals' => [
        'contract_history' => 'سجل العقد رقم :number',
        'update_history' => 'سجل طلبات التحديث',
        'contract_details' => 'تفاصيل العقد رقم :number'
    ],
    
    // Status labels
    'status' => [
        'pending' => 'معلق',
        'approved' => 'تمت الموافقة',
        'not_approved' => 'لم تتم الموافقة',
        'active' => 'نشط',
        'completed' => 'مكتمل'
    ],
    
    // Legacy translations (keeping for backward compatibility)
    'reject_contract' => 'رفض العقد',
    'reason_for_rejection' => 'سبب الرفض',
    'cancel' => 'إلغاء',
    'reject' => 'رفض العقد',
    'request_update' => 'طلب تحديث العقد',
    'update_request_details' => 'تفاصيل طلب التحديث',
    'submit_request' => 'إرسال الطلب',
    'active_contract' => 'العقود النشطة'
];
