<?php

return [
    'title' => 'Payment Details',
    'breadcrumb' => [
        'contracts' => 'Contracts',
        'contract_number' => 'Contract #:number',
        'payments' => 'Payments'
    ],
    'summary' => [
        'total_value' => 'Total Contract Value',
        'contract_number' => 'Contract #:number',
        'paid_amount' => 'Paid Amount',
        'pending_amount' => 'Pending Amount',
        'overdue_amount' => 'Overdue Amount',
        'currency' => 'SAR'
    ],
    'payment_progress' => [
        'title' => 'Payment Progress',
        'paid_out_of' => 'paid out of'
    ],
    'payment_schedule' => [
        'title' => 'Payment Schedule',
        'columns' => [
            'payment_date' => 'Payment Date',
            'amount' => 'Amount',
            'status' => 'Status',
            'paid_at' => 'Paid At',
            'actions' => 'Actions'
        ],
        'status' => [
            'paid' => 'Paid',
            'unpaid' => 'Unpaid',
            'overdue' => 'Overdue',
            'postponed' => 'Postponed',
            'pending' => 'Pending'
        ],
        'no_payments' => 'No payment records found.',
        'paid_on' => 'Paid on :date',
        'last_request' => 'Last Request',
        'approved_on' => 'approved on',
        'request_pending' => 'Request Pending',
        'request_postpone' => 'Request Postponement',
        'details' => 'View Details'
    ],
    'actions' => [
        'postpone' => 'Postpone Payment',
        'view_receipt' => 'View Receipt'
    ],
    'postpone_modal' => [
        'title' => 'Postpone Payment',
        'current_date' => 'Current Due Date',
        'new_date' => 'New Due Date',
        'reason' => 'Reason for Postponement',
        'submit' => 'Submit Request',
        'cancel' => 'Cancel',
        'payment_amount' => 'Payment Amount'
    ],
    'payment_details_modal' => [
        'title' => 'Payment Details',
        'close' => 'Close'
    ]
];
