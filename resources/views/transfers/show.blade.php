@extends('layouts.mediloop', ['title' => __('transfers.show.page_title', ['code' => $transfer->transfer_code])])

@section('content')
@php
    $statusLabels = [
        'PROPOSED' => ['label' => __('transfers.show.status.proposed'), 'tone' => 'pending'],
        'APPROVED' => ['label' => __('transfers.show.status.approved'), 'tone' => 'active'],
        'ACCEPTED' => ['label' => __('transfers.tabs.ready_to_ship'), 'tone' => 'active'],
        'IN_TRANSIT' => ['label' => __('transfers.tabs.in_transit'), 'tone' => 'transit'],
        'COMPLETED' => ['label' => __('transfers.tabs.completed'), 'tone' => 'inactive'],
        'CANCELLED' => ['label' => __('transfers.tabs.cancelled'), 'tone' => 'suspended'],
    ];
    $statusMeta = $statusLabels[$transfer->status] ?? ['label' => $transfer->status, 'tone' => 'inactive'];
    $stages = [
        'PROPOSED' => ['label' => __('transfers.show.stages.proposed'), 'time' => $transfer->proposed_at],
        'APPROVED' => ['label' => __('transfers.show.stages.approved'), 'time' => $transfer->approved_at],
        'ACCEPTED' => ['label' => __('transfers.show.stages.accepted'), 'time' => $transfer->accepted_at],
        'IN_TRANSIT' => ['label' => __('transfers.show.stages.in_transit'), 'time' => $transfer->shipped_at],
        'COMPLETED' => ['label' => __('transfers.show.stages.completed'), 'time' => $transfer->completed_at],
    ];
    $statusOrder = ['PROPOSED' => 1, 'APPROVED' => 2, 'ACCEPTED' => 3, 'IN_TRANSIT' => 4, 'COMPLETED' => 5];
    $currentRank = $statusOrder[$transfer->status] ?? 0;
    $isDonorStaff = $user->isSysadmin() || $user->facility_id === $transfer->donor_facility_id;
    $isRecipientStaff = $user->isSysadmin() || $user->facility_id === $transfer->recipient_facility_id;
    $canApprove = $isDonorStaff && $user->canApproveDonorTransfer();
    $canAccept = $isRecipientStaff && $user->canAcceptRecipientTransfer();
    $canDispatch = $user->isSysadmin() || ($user->facility_id === $transfer->donor_facility_id && $user->hasFacilityRole());
    $canComplete = $isRecipientStaff && $user->canConfirmPhysicalArrival();
    $canCancel = $transfer->status !== 'COMPLETED'
        && $transfer->status !== 'CANCELLED'
        && ($user->isSysadmin() || in_array($user->facility_id, [$transfer->donor_facility_id, $transfer->recipient_facility_id], true));
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="{{ __('transfers.breadcrumb_aria') }}">
        <a href="{{ route('dashboard') }}">{{ __('navigation.dashboard') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('transfers.index') }}">{{ __('transfers.breadcrumb') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ $transfer->transfer_code }}</span>
    </nav>

    @if(session('status'))
        <div class="ml-alert ml-alert-success" role="status">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="ml-alert ml-alert-error" role="alert">
            <strong>{{ __('transfers.show.errors_heading') }}</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ml-alert ml-alert-info ml-compliance-banner" role="note">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z" />
            <path d="M12 8v4m0 4h.01" />
        </svg>
        <div>
            <strong>{{ __('transfers.compliance.title') }}</strong>
            <p>{{ __('transfers.compliance.tracking') }}</p>
        </div>
    </div>

    <header class="ml-passport">
        <div class="ml-passport-main">
            <span class="ml-passport-mark" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 7h12l3 3-3 3H4" />
                    <path d="M20 17H8l-3-3 3-3" />
                </svg>
            </span>
            <div class="ml-passport-copy">
                <p class="ml-passport-eyebrow">{{ __('transfers.show.eyebrow', ['code' => $transfer->transfer_code]) }}</p>
                <div class="ml-passport-title-row">
                    <h1 class="ml-page-title">{{ $transfer->batch->medicine->name }}</h1>
                    <span class="ml-status ml-status-{{ $statusMeta['tone'] }}">{{ $statusMeta['label'] }}</span>
                </div>
                <p class="ml-passport-meta">
                    {{ number_format($transfer->quantity) }} {{ $transfer->batch->medicine->unit }}
                    <span class="ml-meta-dot">·</span>
                    {{ __('transfers.show.submitted_at', ['date' => $transfer->created_at->format('d M Y, H:i')]) }}
                    <span class="ml-meta-dot">·</span>
                    {{ __('transfers.show.score', ['score' => $transfer->match_score]) }}
                </p>
            </div>
        </div>
        <div class="ml-passport-actions">
            <a href="{{ route('transfers.index') }}" class="ml-btn-quiet">{{ __('transfers.show.back_to_list') }}</a>
        </div>
    </header>

    <section class="ml-card" aria-labelledby="progress-title">
        <div class="ml-card-head ml-card-head-split">
            <div>
                <h2 class="ml-card-title" id="progress-title">{{ __('transfers.show.progress.title') }}</h2>
                <p class="ml-card-sub">{{ __('transfers.show.progress.subtitle') }}</p>
            </div>
            <span class="ml-card-index" aria-hidden="true">{{ __('transfers.show.progress.stage_count') }}</span>
        </div>
        <div class="ml-card-body">
            <ol class="ml-transfer-stages">
                @foreach($stages as $statusKey => $stage)
                    @php
                        $stageRank = $statusOrder[$statusKey];
                        $isDone = $currentRank >= $stageRank;
                        $isCurrent = $transfer->status === $statusKey;
                    @endphp
                    <li class="ml-transfer-stage {{ $isDone ? 'is-done' : '' }} {{ $isCurrent ? 'is-current' : '' }}">
                        <span class="ml-transfer-stage-dot" aria-hidden="true">
                            @if($isDone)
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5" /></svg>
                            @else
                                {{ $loop->iteration }}
                            @endif
                        </span>
                        <span class="ml-transfer-stage-copy">
                            <span class="ml-transfer-stage-label">{{ $stage['label'] }}</span>
                            <span class="ml-transfer-stage-time">{{ $stage['time'] ? $stage['time']->format('d M Y, H:i') : __('transfers.show.awaiting') }}</span>
                        </span>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <div class="ml-two-col">
        <div>
            @if($transfer->status !== 'COMPLETED')
                <div class="ml-alert ml-alert-warning ml-invariant-note" role="status">
                    <strong>{{ __('transfers.show.invariant.pending_title') }}</strong>
                    <p>{{ __('transfers.show.invariant.pending_body') }}</p>
                </div>
            @else
                <div class="ml-alert ml-alert-success ml-invariant-note" role="status">
                    <strong>{{ __('transfers.show.invariant.completed_title') }}</strong>
                    <p>{{ __('transfers.show.invariant.completed_body', [
                        'quantity' => number_format($transfer->quantity),
                        'unit' => $transfer->batch->medicine->unit,
                        'donor' => $transfer->donorFacility->name,
                        'recipient' => $transfer->recipientFacility->name,
                        'date' => $transfer->completed_at?->format('d M Y, H:i'),
                    ]) }}</p>
                </div>
            @endif

            @if($transfer->status !== 'COMPLETED' && $transfer->status !== 'CANCELLED')
                <section class="ml-card ml-action-panel" aria-labelledby="action-title">
                    <div class="ml-card-head ml-card-head-split">
                        <div>
                            <h2 class="ml-card-title" id="action-title">{{ __('transfers.show.action.title') }}</h2>
                            <p class="ml-card-sub">{{ $user->name }} · {{ $user->job_title }}</p>
                        </div>
                        <span class="ml-role-chip">{{ \App\Enums\UserRole::tryFrom($user->role)?->label() ?? $user->role }}</span>
                    </div>
                    <div class="ml-card-body">
                        @if($transfer->status === 'PROPOSED')
                            <p class="ml-action-copy">{{ __('transfers.show.action.awaiting_approval') }}</p>
                            @if($canApprove)
                                <form method="POST" action="{{ route('transfers.approve', $transfer) }}">
                                    @csrf
                                    <button type="submit" class="ml-btn-primary">{{ __('transfers.show.action.approve') }}</button>
                                </form>
                            @else
                                <p class="ml-action-note">{{ __('transfers.show.action.approve_note') }}</p>
                            @endif
                        @elseif($transfer->status === 'APPROVED')
                            <p class="ml-action-copy">{{ __('transfers.show.action.awaiting_acceptance') }}</p>
                            @if($canAccept)
                                <form method="POST" action="{{ route('transfers.accept', $transfer) }}">
                                    @csrf
                                    <button type="submit" class="ml-btn-primary">{{ __('transfers.show.action.accept') }}</button>
                                </form>
                            @else
                                <p class="ml-action-note">{{ __('transfers.show.action.accept_note') }}</p>
                            @endif
                        @elseif($transfer->status === 'ACCEPTED')
                            <p class="ml-action-copy">{{ __('transfers.show.action.ready_to_dispatch') }}</p>
                            @if($canDispatch)
                                <form method="POST" action="{{ route('transfers.dispatch', $transfer) }}">
                                    @csrf
                                    <div class="ml-form-grid">
                                        <div class="ml-field">
                                            <label for="courier_type" class="ml-label">{{ __('transfers.show.action.courier_label') }} <span class="ml-required">*</span></label>
                                            <input id="courier_type" name="courier_type" type="text" class="ml-input" value="{{ old('courier_type', __('transfers.show.action.courier_default')) }}" required>
                                        </div>
                                        <div class="ml-field">
                                            <label for="tracking_number" class="ml-label">{{ __('transfers.show.action.tracking_label') }} <span class="ml-required">*</span></label>
                                            <input
                                                id="tracking_number"
                                                name="tracking_number"
                                                type="text"
                                                class="ml-input is-mono"
                                                value="{{ old('tracking_number') }}"
                                                placeholder="{{ __('transfers.show.action.tracking_placeholder') }}"
                                                aria-describedby="tracking-number-help"
                                                autocomplete="off"
                                                required
                                            >
                                            <p id="tracking-number-help" class="ml-help">{{ __('transfers.show.action.tracking_help') }}</p>
                                        </div>
                                    </div>
                                    <button type="submit" class="ml-btn-primary">{{ __('transfers.show.action.dispatch') }}</button>
                                </form>
                            @else
                                <p class="ml-action-note">{{ __('transfers.show.action.dispatch_note') }}</p>
                            @endif
                        @elseif($transfer->status === 'IN_TRANSIT')
                            <div class="ml-transit-banner">
                                <span class="ml-detail-label">{{ __('transfers.show.action.in_transit_label') }}</span>
                                <div class="ml-transit-meta">
                                    <span>{{ __('transfers.show.action.tracking_short') }} <strong>{{ $transfer->tracking_number }}</strong></span>
                                    <span>{{ $transfer->courier_type }}</span>
                                </div>
                            </div>
                            <p class="ml-action-copy">{{ __('transfers.show.action.verify_copy') }}</p>
                            @if($canComplete)
                                <form method="POST" action="{{ route('transfers.complete', $transfer) }}">
                                    @csrf
                                    <button type="submit" class="ml-btn-primary">{{ __('transfers.show.action.complete') }}</button>
                                </form>
                            @else
                                <p class="ml-action-note">{{ __('transfers.show.action.complete_note') }}</p>
                            @endif
                        @endif

                        @if($canCancel)
                            <div class="ml-action-cancel">
                                <form method="POST" action="{{ route('transfers.cancel', $transfer) }}" onsubmit="return confirm('{{ __('transfers.show.action.cancel_confirm') }}');">
                                    @csrf
                                    <button type="submit" class="ml-link-action ml-link-danger">{{ __('transfers.show.action.cancel') }}</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            <section class="ml-card" aria-labelledby="details-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="details-title">{{ __('transfers.show.details.title') }}</h2>
                        <p class="ml-card-sub">{{ __('transfers.show.details.subtitle') }}</p>
                    </div>
                    <span class="ml-card-index" aria-hidden="true">{{ __('transfers.show.details.index_label') }}</span>
                </div>
                <div class="ml-card-body">
                    <div class="ml-detail-grid">
                        <div>
                            <div class="ml-detail-label">{{ __('transfers.show.details.batch') }}</div>
                            <div class="ml-detail-value is-mono">{{ $transfer->batch->batch_number }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">{{ __('common.quantity') }}</div>
                            <div class="ml-detail-value">{{ number_format($transfer->quantity) }} {{ $transfer->batch->medicine->unit }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">{{ __('transfers.show.details.donor') }}</div>
                            <div class="ml-detail-value">{{ $transfer->donorFacility->name }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">{{ __('transfers.show.details.recipient') }}</div>
                            <div class="ml-detail-value">{{ $transfer->recipientFacility->name }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">{{ __('transfers.show.details.distance') }}</div>
                            <div class="ml-detail-value">{{ $transfer->distance_km }} km</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">{{ __('transfers.show.details.intake_cap') }}</div>
                            <div class="ml-detail-value">{{ $transfer->recipient_intake_cap }} {{ __('common.units') }}</div>
                        </div>
                        <div class="is-full">
                            <div class="ml-detail-label">{{ __('common.notes') }}</div>
                            <div class="ml-detail-value">{{ $transfer->notes ?: __('transfers.show.details.notes_empty') }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="ml-card" aria-labelledby="audit-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="audit-title">{{ __('transfers.show.audit.title') }}</h2>
                        <p class="ml-card-sub">{{ __('transfers.show.audit.subtitle') }}</p>
                    </div>
                    <span class="ml-card-index" aria-hidden="true">{{ __('transfers.show.audit.index_label') }}</span>
                </div>
                @if($transfer->auditLogs->isEmpty())
                    <div class="ml-empty-state">
                        <p class="ml-empty-title">{{ __('transfers.show.audit.empty_title') }}</p>
                        <p class="ml-empty-copy">{{ __('transfers.show.audit.empty_copy') }}</p>
                    </div>
                @else
                    <ol class="ml-audit-list">
                        @foreach($transfer->auditLogs as $log)
                            <li class="ml-audit-item">
                                <span class="ml-audit-dot" aria-hidden="true"></span>
                                <div class="ml-audit-time">{{ $log->created_at->format('d M Y · H:i') }}</div>
                                <div class="ml-audit-body">
                                    <strong>{{ $log->actor->name ?? __('transfers.show.audit.system') }}</strong>
                                    <span>{{ $log->description }}</span>
                                    <code>{{ $log->action }}</code>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>
        </div>

        <aside>
            <section class="ml-card ml-sticky" aria-labelledby="summary-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="summary-title">{{ __('transfers.show.summary.title') }}</h2>
                </div>
                <div class="ml-card-body">
                    <div class="ml-route-vertical">
                        <div>
                            <span class="ml-detail-label">{{ __('transfers.show.summary.donor') }}</span>
                            <div class="ml-detail-value">{{ $transfer->donorFacility->name }}</div>
                        </div>
                        <span class="ml-route-line" aria-hidden="true"></span>
                        <div>
                            <span class="ml-detail-label">{{ __('transfers.show.summary.recipient') }}</span>
                            <div class="ml-detail-value">{{ $transfer->recipientFacility->name }}</div>
                        </div>
                    </div>

                    <div class="ml-score-list">
                        <div class="ml-score-row">
                            <span>{{ __('transfers.show.scores.need') }}</span>
                            <strong>{{ $transfer->need_score }}%</strong>
                        </div>
                        <div class="ml-score-bar" aria-hidden="true"><span style="width: {{ $transfer->need_score }}%"></span></div>
                        <div class="ml-score-row">
                            <span>{{ __('transfers.show.scores.proximity') }}</span>
                            <strong>{{ $transfer->proximity_score }}%</strong>
                        </div>
                        <div class="ml-score-bar" aria-hidden="true"><span style="width: {{ $transfer->proximity_score }}%"></span></div>
                        <div class="ml-score-row">
                            <span>{{ __('transfers.show.scores.logistics') }}</span>
                            <strong>{{ $transfer->logistics_score }}%</strong>
                        </div>
                        <div class="ml-score-bar" aria-hidden="true"><span style="width: {{ $transfer->logistics_score }}%"></span></div>
                        <div class="ml-score-total">
                            <span>{{ __('transfers.show.scores.match') }}</span>
                            <strong>{{ $transfer->match_score }}%</strong>
                        </div>
                    </div>

                    <p class="ml-help">{{ __('transfers.show.summary.note') }}</p>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
