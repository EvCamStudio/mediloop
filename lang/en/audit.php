<?php

return [

    'page_title' => 'Regional Oversight & Audit',
    'breadcrumb' => 'Oversight & audit',
    'breadcrumb_aria' => 'Breadcrumb',
    'eyebrow' => 'Regional oversight',
    'title' => 'Logistics Oversight & Redistribution Audit',
    'subtitle' => 'Review completed redistribution transactions and the inventory status change trail across facilities. This page contains no patient data.',
    'default_facility' => 'Health Office',

    'kpi' => [
        'units_saved' => 'Units saved',
        'units_saved_meta' => 'From :count completed transfers',
        'value_saved' => 'Value saved',
        'value_saved_meta' => 'Based on the batch unit price',
        'facilities_connected' => 'Connected facilities',
        'facilities_connected_meta' => 'Outside Health Office facilities',
        'facility_unit' => 'facilities',
        'active_batches' => 'Active batches monitored',
        'active_batches_meta' => 'Batches with an active status today',
        'batch_unit' => 'batch',
    ],

    'completed' => [
        'title' => 'Successful Redistribution Transaction Summary',
        'subtitle' => 'Completed transfers with recorded stock mutations. Sorted by most recent completion time.',
        'count' => ':count completed transfer|:count completed transfers',
        'empty_title' => 'No completed transfers yet',
        'empty_copy' => 'The summary appears after a transfer is confirmed complete and stock is mutated.',
        'table_aria' => 'Completed transfer summary table, scroll horizontally to see all columns',
        'table_caption' => 'Summary of completed redistribution transfers, including medicine, facility route, quantity, value, courier, and completion time.',
        'default_courier' => 'Dedicated pharmacy courier',
        'tracking' => 'Tracking :number',
        'columns' => [
            'code' => 'Transfer code',
            'medicine_batch' => 'Medicine & batch',
            'donor_recipient' => 'Donor → recipient',
            'value' => 'Value (IDR)',
            'courier' => 'Courier & tracking',
            'completed_at' => 'Completed at (WIB)',
        ],
    ],

    'logs' => [
        'title' => 'System Audit Trail',
        'subtitle' => 'Record of status changes and inventory mutations, sorted newest first.',
        'count' => ':count entry|:count entries',
        'empty_title' => 'No audit log records yet',
        'empty_copy' => 'Every status change or inventory mutation will be recorded here.',
        'table_aria' => 'System audit trail table, scroll horizontally to see all columns',
        'table_caption' => 'System audit trail containing time, actor, related facility, action code, and change description.',
        'system_actor' => 'Automated system',
        'staff_default' => 'Officer',
        'system_activity' => 'System activity',
        'no_description' => 'No description.',
        'columns' => [
            'time' => 'Time (WIB)',
            'actor' => 'User actor',
            'facility' => 'Related facility',
            'action' => 'Action',
            'description' => 'Change description',
        ],
    ],

];
