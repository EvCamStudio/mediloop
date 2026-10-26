<?php

return [

    'page_title' => 'Analytics & Predictive Statistics',
    'eyebrow' => 'Decision-support analytics',
    'title' => 'Analytics & Predictive Visualization',
    'subtitle' => 'Calculated from consumption burn rate, projected expiry waste, and redistribution efficiency.',
    'filter_label' => 'Facility',
    'all_facilities' => 'All facilities (regional)',
    'apply_filter' => 'Apply filter',
    'currency' => 'Rupiah (IDR)',

    'kpi' => [
        'inventory_label' => 'Total inventory value',
        'inventory_units' => ':count physical unit|:count physical units',
        'inventory_batches' => ':count batch|:count batches',
        'waste_label' => 'Potential waste loss',
        'waste_units' => ':count unit at risk|:count units at risk',
        'waste_ratio' => ':ratio of stock',
        'efficiency_label' => 'Inventory efficiency score',
        'efficiency_note' => 'Based on medicine consumption rate',
        'saved_label' => 'Redistribution savings',
        'saved_units' => ':count unit saved|:count units saved',
        'saved_status' => 'Completed transfers',
    ],

    'units' => [
        'unit' => 'unit',
        'units' => 'units',
        'batch' => 'batch',
        'batches' => 'batches',
        'per_day' => 'units/day',
    ],

    'risk' => [
        'title' => 'Medicine risk distribution',
        'subtitle' => 'Batch proportion based on remaining expiry days and consumption rate',
        'batch_count' => ':count batch|:count batches',
        'critical' => 'Critical',
        'high' => 'High',
        'medium' => 'Medium',
        'low' => 'Safe',
        'critical_with_code' => 'Critical (Critical)',
        'high_with_code' => 'High (High)',
        'medium_with_code' => 'Medium (Medium)',
        'low_with_code' => 'Safe (Low)',
    ],

    'category' => [
        'title' => 'Potential waste value by medicine category (Rp)',
        'subtitle' => 'Estimated financial loss when surplus is not redistributed to another facility',
    ],

    'horizon' => [
        'title' => 'Expiry time horizon',
        'subtitle' => 'Number of batches approaching their expiry date',
        'under_30' => 'Urgent (≤ 30 days)',
        'days_31_60' => 'Warning (31–60 days)',
        'days_61_90' => 'Watch (61–90 days)',
        'over_90' => 'Safe (> 90 days)',
    ],

    'bar_chart' => [
        'title' => 'Wastage by medicine category',
        'subtitle' => 'Estimated projected waste value per medicine name',
        'aria' => 'Bar chart of projected waste by medicine category',
    ],

    'surplus' => [
        'title' => 'Highest surplus index (needs immediate reallocation)',
        'subtitle' => 'Medicines whose depletion time far exceeds the remaining expiry window (surplus ratio > 1)',
        'manage_stock' => 'Manage stock →',
        'empty' => 'No medicine has a significant surplus yet.',
        'medicine_batch' => 'Medicine & batch',
        'stock_burn' => 'Stock / daily rate',
        'deplete_vs_expiry' => 'Depletion vs expiry',
        'surplus_ratio' => 'Surplus ratio',
        'potential_waste' => 'Potential waste',
        'days_to_deplete' => ':days days to deplete',
        'days_to_expiry' => ':days days to expiry',
        'ratio' => ':ratio× surplus',
    ],

    'charts' => [
        'waste_value' => 'Estimated waste (Rp)',
        'batches' => 'Batches',
        'batch' => 'batch',
        'risk_aria' => 'Donut chart of medicine batch risk distribution',
        'category_aria' => 'Bar chart of potential waste value by medicine category',
        'horizon_aria' => 'Bar chart of batches by expiry time horizon',
    ],

    'empty' => [
        'title' => 'No active batch data yet',
        'description' => 'Analytics will appear after your facility records active medicine batches.',
        'cta' => 'Input a batch',
    ],

];
