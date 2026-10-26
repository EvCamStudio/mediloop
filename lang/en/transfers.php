<?php

return [

    'breadcrumb' => 'Redistribution',
    'breadcrumb_aria' => 'Breadcrumb',
    'batch_label' => 'Batch :batch',
    'default_storage_location' => 'Recipient Pharmacy Warehouse',

    'units' => [
        'per_day' => 'units/day',
    ],

    'risk' => [
        'high' => 'High risk',
    ],

    'tabs' => [
        'awaiting_donor' => 'Awaiting donor',
        'awaiting_recipient' => 'Awaiting recipient',
        'ready_to_ship' => 'Ready to ship',
        'in_transit' => 'In transit',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    'table' => [
        'transfer' => 'Transfer',
        'medicine_quantity' => 'Medicine & quantity',
        'facility_route' => 'Facility route',
        'score' => 'Score',
    ],

    'compliance' => [
        'title' => 'Pharmacy regulatory compliance',
        'proposal' => 'MediLoop is a decision-support system, not a marketplace. Redistribution still requires approval from the donor Head of Pharmacy and physical receipt verification by the recipient Facility Head.',
        'tracking' => 'MediLoop is a decision-support system, not a marketplace. Every transfer authorization still requires approval from an authorized officer and physical receipt verification.',
    ],

    'index' => [
        'page_title' => 'Medicine Redistribution List',
        'eyebrow' => 'Inter-facility coordination',
        'title' => 'Redistribution & medicine grants',
        'subtitle' => 'Track proposal status from donor authorization through physical stock mutation.',
        'propose_from_stock' => 'Propose from stock',
        'tabs_aria' => 'Transfer status filter',
        'list_title' => 'Medicine transfer list',
        'table_caption' => 'Transfer list and workflow status',
        'empty_title' => 'No transfers in this category yet',
        'empty_copy' => 'Transfer proposals appear after an at-risk stock batch is proposed from the dashboard.',
        'empty_cta' => 'Back to dashboard',
        'air_distance' => 'air distance',
        'track' => 'Track',
    ],

    'propose' => [
        'page_title' => 'Candidate Recommendation & Transfer Proposal',
        'breadcrumb' => 'Batch :batch proposal',
        'eyebrow' => 'Redistribution recommendation',
        'title' => 'Find recipients for :medicine',
        'subtitle' => 'Candidates are ranked by need, distance, and logistics readiness.',

        'source' => [
            'title' => 'Source batch',
            'meta' => ':facility · batch :batch',
            'help' => 'Projected waste is calculated from the difference between stock and consumption rate up to the expiry date.',
        ],

        'metrics' => [
            'donor_stock' => 'Donor stock',
            'burn_rate' => 'Burn rate',
            'remaining_expiry' => 'Remaining shelf life',
            'projected_waste' => 'Projected waste',
        ],

        'candidates' => [
            'title' => 'Candidate recipients',
            'weights' => '50% need · 30% air distance · 20% logistics',
            'count' => ':count facility|:count facilities',
            'sort_aria' => 'Sort candidates',
            'sort_best' => 'Best score',
            'sort_nearest' => 'Nearest',
            'sort_note' => 'Air distance (Haversine), not road travel distance.',
            'empty_title' => 'No candidate recipients yet',
            'empty_copy' => 'No facility currently meets the cold-chain and distance requirements.',
            'need' => 'Need',
            'distance' => 'Distance',
            'logistics' => 'Logistics',
            'capacity' => 'Capacity',
            'select' => 'Select',
        ],

        'form' => [
            'title' => 'Propose transfer',
            'subtitle' => 'The proposal is forwarded to the donor Head of Pharmacy.',
            'recipient_facility' => 'Recipient facility',
            'quantity' => 'Unit quantity',
            'quantity_required' => 'Unit quantity is required.',
            'quantity_min' => 'The minimum quantity is 1 unit.',
            'quantity_max' => 'The quantity exceeds the available stock (:max units).',
            'quantity_help' => 'Up to :max :unit available at the donor.',
            'notes' => 'Justification notes',
            'notes_maxlength' => 'Notes may not exceed 500 characters.',
            'notes_placeholder' => 'Explain the recipient need and the reason for redistribution.',
            'notes_default' => 'Proposing redistribution of surplus :medicine stock to prevent destruction of expired medicines and support availability at nearby facilities.',
            'proposer' => 'Proposer:',
            'submit' => 'Propose transfer',
        ],
    ],

    'show' => [
        'page_title' => 'Transfer Tracking #:code',
        'eyebrow' => 'Medicine transfer · :code',
        'errors_heading' => 'Check the following fields:',
        'awaiting' => 'Awaiting',
        'submitted_at' => 'Submitted :date',
        'score' => 'Score :score%',
        'back_to_list' => 'Back to list',

        'status' => [
            'proposed' => 'Awaiting donor authorization',
            'approved' => 'Awaiting recipient acceptance',
        ],

        'stages' => [
            'proposed' => 'Proposal',
            'approved' => 'Donor authorization',
            'accepted' => 'Recipient acceptance',
            'in_transit' => 'Shipment',
            'completed' => 'Receipt & mutation',
        ],

        'progress' => [
            'title' => 'Transfer progress',
            'subtitle' => 'Each stage requires authorization from the responsible party.',
            'stage_count' => '5 STAGES',
        ],

        'invariant' => [
            'pending_title' => 'Donor stock not deducted yet',
            'pending_body' => 'Donor and recipient stock mutations only run when the recipient confirms physical receipt. Until then, donor stock stays intact.',
            'completed_title' => 'Stock mutation complete',
            'completed_body' => ':quantity :unit were deducted from :donor and added to :recipient on :date.',
        ],

        'action' => [
            'title' => 'Actions for this stage',
            'awaiting_approval' => 'Awaiting approval from the Head of Pharmacy at the donor facility.',
            'approve' => 'Approve medicine release',
            'approve_note' => 'Only the Head of Pharmacy at the donor facility is authorized to approve this stage.',
            'awaiting_acceptance' => 'The donor has approved. Awaiting acceptance from the recipient facility.',
            'accept' => 'Confirm acceptance',
            'accept_note' => 'Only the Pharmacist or Facility Head of the recipient facility is authorized.',
            'ready_to_dispatch' => 'The recipient is ready. Enter courier details to dispatch the medicines.',
            'courier_label' => 'Courier / transport type',
            'courier_default' => 'Refrigerated pharmacy courier (cold box)',
            'tracking_label' => 'Tracking number / delivery note',
            'tracking_placeholder' => 'Example: EXP-DKI-2026-001',
            'tracking_help' => 'Enter the number from the physical tracking slip or delivery note.',
            'dispatch' => 'Dispatch shipment',
            'dispatch_note' => 'Only donor facility staff can dispatch the shipment.',
            'in_transit_label' => 'Currently in transit',
            'tracking_short' => 'Tracking',
            'verify_copy' => 'The recipient checks the physical condition, packaging seals, and cold temperature before confirming receipt.',
            'complete' => 'Confirm physical receipt & stock mutation',
            'complete_note' => 'Awaiting confirmation from the recipient Facility Head before stock is mutated.',
            'cancel_confirm' => 'Cancel this transfer? This action cannot be undone.',
            'cancel' => 'Cancel transfer',
        ],

        'details' => [
            'title' => 'Logistics details',
            'subtitle' => 'Route, quantity, and candidate matching results.',
            'index_label' => 'DATA',
            'batch' => 'Batch',
            'donor' => 'Donor',
            'recipient' => 'Recipient',
            'distance' => 'Air distance',
            'intake_cap' => 'Recipient intake cap',
            'notes_empty' => 'No additional notes.',
        ],

        'audit' => [
            'title' => 'Audit trail',
            'subtitle' => 'Authorization and status change history.',
            'index_label' => 'LOG',
            'empty_title' => 'No audit records yet',
            'empty_copy' => 'Transfer activity will appear here.',
            'system' => 'System',
        ],

        'summary' => [
            'title' => 'Transfer summary',
            'donor' => 'Donor',
            'recipient' => 'Recipient',
            'note' => 'Recommendations are advisory. Every transfer requires approval from facility staff.',
        ],

        'scores' => [
            'need' => 'Need (50%)',
            'proximity' => 'Distance (30%)',
            'logistics' => 'Logistics (20%)',
            'match' => 'Match score',
        ],
    ],

    'messages' => [
        'propose_forbidden' => 'You can only propose redistribution for your own facility.',
        'batch_unavailable' => 'This batch is not available for redistribution.',
        'quantity_exceeds_stock' => 'The transfer quantity exceeds the available stock.',
        'recipient_invalid' => 'The recipient facility must be active and different from the donor facility.',
        'access_denied' => 'You do not have access to this transfer.',
        'not_proposed_stage' => 'This transfer is not at the donor approval stage.',
        'approve_forbidden' => 'Only the Head of Pharmacy at the donor facility can approve the medicine release.',
        'not_approved_stage' => 'This transfer has not been approved by the donor facility.',
        'accept_forbidden' => 'Only representatives of the recipient facility can accept this transfer.',
        'not_accepted_stage' => 'The shipment can only be dispatched after the recipient facility accepts the transfer.',
        'dispatch_forbidden' => 'Only donor facility staff can dispatch the shipment.',
        'not_in_transit_stage' => 'Physical receipt can only be confirmed while the medicines are in transit.',
        'complete_forbidden' => 'Only the head or director of the recipient facility can validate the physical receipt of medicines.',
        'cancel_not_allowed' => 'A transfer at this stage cannot be cancelled.',
        'cancel_forbidden' => 'You are not authorized to cancel this transfer.',
        'cancel_reason_default' => 'Cancelled by user.',

        'proposed_flash' => 'Transfer proposal :code was submitted and is awaiting approval from the Head of Pharmacy.',
        'approved_flash' => 'The donor approved the transfer proposal. Awaiting recipient confirmation.',
        'accepted_flash' => 'The transfer was accepted by the recipient facility. The donor facility can now dispatch the shipment.',
        'in_transit_flash' => 'Shipment status updated to In Transit.',
        'completed_flash' => 'Physical receipt verified. Medicine stock has been mutated atomically.',
        'cancelled_flash' => 'The transfer was cancelled.',

        'audit_proposed' => 'Transfer :code proposed by :name for :quantity units to :recipient.',
        'audit_approved' => 'Formal approval of the medicine release was granted by :name (:job_title).',
        'audit_accepted' => 'Recipient facility (:facility) accepted the medicine allocation.',
        'audit_dispatched' => 'Medicines dispatched using :courier with tracking/control number :tracking.',
        'audit_completed' => 'Transfer :code successfully completed. Received :quantity units of :medicine (Batch: :batch).',
        'audit_cancelled' => 'Transfer :code was cancelled. Reason: :reason',
    ],

];
