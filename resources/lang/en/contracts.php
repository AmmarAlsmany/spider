<?php

return [
    // Page titles and breadcrumbs
    'client_profile' => 'Client Profile',
    'contracts' => 'Contracts',
    
    // Empty state
    'no_contracts' => [
        'title' => 'No Contracts Found',
        'message' => 'You don\'t have any active contracts at the moment.'
    ],
    
    // Contract card
    'contract_number' => 'Contract #:number',
    'details' => [
        'start_date' => 'Start Date',
        'property_type' => 'Property Type',
        'contract_type' => 'Contract Type',
        'contract_price' => 'Contract Price',
        'payment_type' => 'Payment Type',
        'number_of_payments' => 'Number of Payments',
        'multiple_branches' => 'Multiple Branches',
        'yes' => 'Yes',
        'no' => 'No',
        'description' => 'Contract Description',
        'no_description' => 'No description available.'
    ],
    
    // Update request alerts
    'update_request' => [
        'pending' => 'Your update request is pending review.',
        'status' => 'Update request has been :status'
    ],
    
    // Actions and buttons
    'actions' => [
        'approve' => [
            'button' => 'Approve Contract',
            'title' => 'Approve Contract #:number'
        ],
        'reject' => [
            'button' => 'Not Approve',
            'title' => 'Not Approve Contract #:number',
            'reason_label' => 'Reason for Not Approving',
            'submit' => 'Submit'
        ],
        'view_details' => 'View Details',
        'request_update' => 'Request Update',
        'payment_details' => 'Payment Details',
        'visit_details' => 'Visit Details',
        'contract_history' => 'Contract History',
        'update_history' => 'Update Requests',
        'download' => 'Download Contract'
    ],
    
    // Modal titles
    'modals' => [
        'contract_history' => 'Contract History #:number',
        'update_history' => 'Update Request History',
        'contract_details' => 'Contract Details #:number'
    ],
    
    // Status labels
    'status' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'not_approved' => 'Not Approved',
        'active' => 'Active',
        'completed' => 'Completed'
    ],
    'reject_contract' => 'Reject Contract',
    'reason_for_rejection' => 'Reason for Rejection',
    'cancel' => 'Cancel',
    'reject' => 'Reject Contract',
    'request_update' => 'Request Contract Update',
    'update_request_details' => 'Update Request Details',
    'submit_request' => 'Submit Request',
];
