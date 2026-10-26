<?php

return [

    'page_title' => 'Logistics Summary',
    'eyebrow' => 'Facility control center',
    'title' => 'Logistics summary',
    'subtitle' => 'Monitor expiry risk and redistribution opportunities across facilities.',
    'facility_fallback' => 'Health facility',
    'manage_stock' => 'Manage stock',
    'input_batch' => 'Input batch',

    'facility_missing' => [
        'title' => 'Account not linked to a facility',
        'body' => 'Cross-facility stock data is hidden until your account is linked to a verified facility. Contact the platform administrator to complete verification.',
    ],

    'kpi' => [
        'at_risk' => 'Batches at risk',
        'batch_unit' => 'batch',
        'high_risk' => ':count high risk',
        'monitored' => ':count monitored',
        'active_stock' => 'Active stock',
        'batches_recorded' => ':count batch recorded|:count batches recorded',
        'risk_value' => 'Value at risk',
        'risk_value_meta' => 'Projected value that can be saved',
        'network' => 'Facility network',
        'facility_unit' => 'facilities',
        'network_meta' => 'Connected for redistribution',
    ],

    'forecast' => [
        'title' => 'Expiry risk forecast',
        'subtitle' => 'Days to expiry compared with the facility daily consumption rate.',
        'view_all' => 'View all stock',
        'table_caption' => 'List of active medicine batches with stock, stock depletion, expiry risk, and redistribution actions.',
        'cold_chain' => 'Requires cold chain',
        'batch_expiry' => 'Batch :batch · Exp :date',
        'expires_in' => 'Expires in :days days',
        'stock_never' => 'never runs out',
        'stock_out_in' => 'stock runs out in :days days',
        'find_recipient' => 'Find recipient',
        'empty_title' => 'No stock data yet',
        'empty_copy' => 'The medicine data you enter is analysed immediately to detect expiry waste risk.',
        'empty_cta' => 'Input medicine stock',
        'columns' => [
            'medicine_batch' => 'Medicine & batch',
            'physical_stock' => 'Physical stock',
            'shelf_life' => 'Batch shelf life',
        ],
    ],

    'match' => [
        'title' => 'Redistribution match recommendations',
        'subtitle_lead' => 'Candidates for',
        'subtitle_tail' => '(batch :batch). Score: need 50% · distance 30% · logistics 20%.',
        'score_title' => 'Score = need 50% + distance 30% + logistics 20%',
        'capacity' => 'Capacity :count units',
        'cta' => 'Propose transfer',
        'empty_candidates' => 'No candidate recipient facilities currently meet the cold-chain and distance requirements.',
        'empty_no_risk' => 'No batches are at risk of expiring. Add batch stock to get automatic transfer recommendations.',
        'note' => 'Recommendations are advisory. Every transfer still requires approval from facility staff.',
    ],

];
