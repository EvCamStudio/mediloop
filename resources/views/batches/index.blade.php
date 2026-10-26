@extends('layouts.mediloop', ['title' => __('batches.page_title')])

@section('content')
@php
    $riskLabels = [
        'CRITICAL' => __('batches.risk.critical'),
        'HIGH' => __('batches.risk.high'),
        'MEDIUM' => __('batches.risk.medium'),
        'LOW' => __('batches.risk.low'),
    ];

    $filterParams = array_filter([
        'search' => $currentSearch !== '' ? $currentSearch : null,
        'facility_id' => $user->isDinkesAuditor() ? request('facility_id') : null,
    ], fn ($value) => $value !== null && $value !== '');

    $statusTabs = [
        ['label' => __('common.all'), 'value' => ''],
        ['label' => __('batches.status.active'), 'value' => 'ACTIVE'],
        ['label' => __('batches.status.reserved'), 'value' => 'RESERVED'],
        ['label' => __('batches.status.depleted'), 'value' => 'DEPLETED'],
        ['label' => __('batches.status.expired'), 'value' => 'EXPIRED'],
    ];

    $hasFilters = $currentSearch !== ''
        || $currentStatus !== ''
        || ($user->isDinkesAuditor() && request()->filled('facility_id'));
@endphp

<div class="ml-page-admin">
    @if(session('status'))
        <div class="ml-alert ml-alert-success" role="status">
            <div class="ml-alert-row">
                <svg class="ml-alert-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
                <button type="button" class="ml-alert-close" onclick="this.closest('.ml-alert').remove()" aria-label="{{ __('batches.close_notification') }}">&times;</button>
            </div>
        </div>
    @endif

    @if($errors->has('delete_error'))
        <div class="ml-alert ml-alert-error" role="alert">
            <div class="ml-alert-row">
                <svg class="ml-alert-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ $errors->first('delete_error') }}</span>
            </div>
        </div>
    @endif

    <nav class="ml-breadcrumb" aria-label="{{ __('batches.breadcrumb_aria') }}">
        <a href="{{ route('dashboard') }}">{{ __('navigation.dashboard') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ __('batches.breadcrumb_batches') }}</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">{{ __('batches.eyebrow') }}</p>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">{{ __('batches.title') }}</h1>
                <span class="ml-facility-chip">{{ $facility?->name ?? __('batches.facility_fallback') }}</span>
            </div>
            <p class="ml-page-sub">{{ __('batches.subtitle') }}</p>
        </div>

        @if(! $user->isDinkesAuditor())
            <div class="ml-page-actions">
                <a href="{{ route('batches.create') }}" class="ml-btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('batches.input_batch') }}</span>
                </a>
            </div>
        @endif
    </header>

    <div class="ml-kpi-grid">
        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">{{ __('batches.kpi.at_risk_label') }}</span>
                <span class="ml-kpi-icon is-risk" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 9v4M12 17h.01" />
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($atRiskCount ?? 0, 0, ',', '.') }} <span>{{ trans_choice('batches.units.batch_word', $atRiskCount ?? 0) }}</span></div>
            <div class="ml-kpi-meta">{{ __('batches.kpi.at_risk_meta') }}</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">{{ __('batches.kpi.stock_label') }}</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($totalStockUnits ?? 0, 0, ',', '.') }} <span>{{ __('common.unit') }}</span></div>
            <div class="ml-kpi-meta">{{ trans_choice('batches.kpi.stock_meta', $totalBatchesCount ?? 0, ['count' => number_format($totalBatchesCount ?? 0, 0, ',', '.')]) }}</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">{{ __('batches.kpi.waste_value_label') }}</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                    </svg>
                </span>
            </div>
            <div class="ml-kpi-value ml-kpi-value-currency">Rp {{ number_format($totalWasteValue ?? 0, 0, ',', '.') }}</div>
            <div class="ml-kpi-meta">{{ trans_choice('batches.kpi.waste_units_meta', $totalWasteUnits ?? 0, ['count' => number_format($totalWasteUnits ?? 0, 0, ',', '.')]) }}</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">{{ __('batches.kpi.waste_prevented_label') }}</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        <path d="M9 12l2 2 4-4" />
                    </svg>
                </span>
            </div>
            <div class="ml-kpi-value">~{{ number_format($estimatedWasteKg ?? 0, 1, ',', '.') }} <span>{{ __('batches.units.kg_label') }}</span></div>
            <div class="ml-kpi-meta">{{ __('batches.kpi.waste_prevented_meta') }}</div>
        </article>
    </div>

    @if(isset($topRiskBatch) && count($topCandidates) > 0)
        @php
            $topRisk = (string) $topRiskBatch->risk_level;
            $topRiskLabel = $riskLabels[$topRisk] ?? ucfirst(strtolower($topRisk));
        @endphp

        <section class="ml-section ml-rec-band" aria-labelledby="match-title">
            <div class="ml-section-header ml-section-header-split">
                <div>
                    <span class="ml-recommendation-flag">{{ __('batches.recommendation.flag') }}</span>
                    <h2 class="ml-section-title" id="match-title">
                        {{ trans_choice('batches.recommendation.title', (int) $topRiskBatch->projected_waste, ['medicine' => $topRiskBatch->medicine->name, 'count' => number_format($topRiskBatch->projected_waste)]) }}
                    </h2>
                    <p class="ml-section-sub">
                        {{ __('batches.batch_label') }} <span class="ml-cell-mono">{{ $topRiskBatch->batch_number }}</span>
                        &middot; {{ trans_choice('batches.recommendation.expires_in', max(0, (int) $topRiskBatch->days_to_expiry), ['count' => max(0, (int) $topRiskBatch->days_to_expiry)]) }}
                        &middot; {{ __('batches.recommendation.score_formula') }}
                    </p>
                </div>
                <span class="ml-risk ml-risk-{{ strtolower($topRisk) }}">{{ $topRiskLabel }}</span>
            </div>

            <div class="ml-rec-grid">
                @foreach($topCandidates as $index => $candidate)
                    @php
                        $intakeCap = (int) ($candidate['recipient_intake_cap'] ?? $candidate['intake_cap'] ?? 0);
                    @endphp
                    <article class="ml-rec-card">
                        <div class="ml-rec-top">
                            <span class="ml-rec-rank" aria-hidden="true">{{ $index + 1 }}</span>
                            <div class="ml-rec-body">
                                <div class="ml-rec-name">{{ $candidate['facility']->name }}</div>
                                <div class="ml-rec-meta">
                                    {{ $candidate['facility']->type }} &middot; {{ $candidate['facility']->city ?? __('batches.location_not_set') }}
                                </div>
                            </div>
                            <span class="ml-rec-score" title="{{ __('batches.recommendation.score_title') }}">
                                {{ $candidate['match_score'] }}%
                            </span>
                        </div>

                        <div class="ml-rec-facts">
                            <span>{{ __('batches.recommendation.distance', ['distance' => $candidate['distance_km']]) }}</span>
                            <span>{{ __('batches.recommendation.allocation_cap') }} <strong>{{ number_format($intakeCap) }} {{ $topRiskBatch->medicine->unit }}</strong></span>
                        </div>

                        @if(! $user->isDinkesAuditor())
                            <a href="{{ route('transfers.propose', $topRiskBatch) }}" class="ml-btn-primary ml-btn-sm ml-rec-cta">
                                <span>{{ __('batches.recommendation.send_proposal') }}</span>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M5 12h14M13 6l6 6-6 6" />
                                </svg>
                            </a>
                        @endif
                    </article>
                @endforeach
            </div>

            <p class="ml-rec-note">{{ __('batches.recommendation.note') }}</p>
        </section>
    @endif

    <nav class="ml-registry-tabs" aria-label="{{ __('batches.status_filter_aria') }}">
        @foreach($statusTabs as $tab)
            @php
                $tabParams = $tab['value'] === ''
                    ? $filterParams
                    : array_merge($filterParams, ['status' => $tab['value']]);
                $isActive = $currentStatus === $tab['value'];
            @endphp
            <a
                href="{{ route('batches.index', $tabParams) }}"
                class="ml-registry-tab {{ $isActive ? 'is-active' : '' }}"
                @if($isActive) aria-current="page" @endif
            >
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>

    <section class="ml-registry-panel" aria-labelledby="inventory-title">
        <div class="ml-section-header ml-section-header-split">
            <div>
                <h2 class="ml-section-title" id="inventory-title">{{ __('batches.inventory.title') }}</h2>
                <p class="ml-section-sub">
                    {{ trans_choice('batches.inventory.subtitle', $batches->total(), ['count' => number_format($batches->total(), 0, ',', '.')]) }}
                </p>
            </div>
            @if(! $batches->isEmpty())
                <div class="ml-inventory-legend" role="group" aria-label="{{ __('batches.inventory.legend_aria') }}">
                    <span class="ml-risk ml-risk-critical">{{ __('batches.inventory.legend_critical') }}</span>
                    <span class="ml-risk ml-risk-high">{{ __('batches.inventory.legend_high') }}</span>
                    <span class="ml-risk ml-risk-medium">{{ __('batches.inventory.legend_medium') }}</span>
                    <span class="ml-risk ml-risk-low">{{ __('batches.inventory.legend_low') }}</span>
                </div>
            @endif
        </div>

        <form method="GET" action="{{ route('batches.index') }}" class="ml-registry-toolbar" aria-label="{{ __('batches.toolbar.aria') }}">
            @if($currentStatus !== '')
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @endif

            <div class="ml-registry-search">
                <label for="filter-search" class="ml-sr-only">{{ __('batches.toolbar.search_label') }}</label>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" />
                    <path d="M20 20l-3.5-3.5" />
                </svg>
                <input id="filter-search" name="search" type="search" class="ml-input" value="{{ $currentSearch }}" placeholder="{{ __('batches.toolbar.search_placeholder') }}">
            </div>

            @if($user->isDinkesAuditor() && isset($facilitiesList))
                <div class="ml-registry-filter">
                    <label for="filter-facility" class="ml-sr-only">{{ __('batches.toolbar.facility_label') }}</label>
                    <select id="filter-facility" name="facility_id" class="ml-select">
                        <option value="">{{ __('batches.toolbar.all_facilities') }}</option>
                        @foreach($facilitiesList as $facilityOption)
                            <option value="{{ $facilityOption->id }}" @selected((string) request('facility_id') === (string) $facilityOption->id)>
                                {{ $facilityOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="ml-registry-toolbar-actions">
                <button type="submit" class="ml-btn-primary">{{ __('common.apply') }}</button>
                @if($hasFilters)
                    <a href="{{ route('batches.index') }}" class="ml-btn-quiet">{{ __('common.reset') }}</a>
                @endif
            </div>
        </form>

        @if($batches->isEmpty())
            <div class="ml-empty-state">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <p class="ml-empty-title">{{ __('batches.empty.title') }}</p>
                <p class="ml-empty-copy">
                    @if($hasFilters)
                        {{ __('batches.empty.filtered') }}
                    @else
                        {{ __('batches.empty.default') }}
                    @endif
                </p>
                @if($hasFilters)
                    <a href="{{ route('batches.index') }}" class="ml-btn-quiet">{{ __('batches.clear_filters') }}</a>
                @elseif(! $user->isDinkesAuditor())
                    <a href="{{ route('batches.create') }}" class="ml-btn-primary">{{ __('batches.input_batch') }}</a>
                @endif
            </div>
        @else
            <div class="ml-table-responsive">
                <table class="ml-table ml-table-airy ml-table-inventory">
                    <caption class="ml-sr-only">
                        {{ __('batches.table.caption') }}
                    </caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('batches.table.medicine') }}</th>
                            <th scope="col">{{ __('batches.table.batch_location') }}</th>
                            <th scope="col" class="is-right">{{ __('common.stock') }}</th>
                            <th scope="col">{{ __('batches.table.expiry_runway') }}</th>
                            <th scope="col" class="is-right">{{ __('batches.table.projected_waste') }}</th>
                            <th scope="col" class="is-right"><span class="ml-sr-only">{{ __('common.actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batches as $batch)
                            @php
                                $risk = (string) $batch->risk_level;
                                $riskLabel = $riskLabels[$risk] ?? ucfirst(strtolower($risk));
                                $riskTone = strtolower($risk);
                                $daysToExpiry = (int) $batch->days_to_expiry;
                                $daysToDeplete = is_finite($batch->days_to_deplete) ? max(0, (float) $batch->days_to_deplete) : null;
                                $projectedWaste = (int) $batch->projected_waste;
                                $timelineDays = max(abs($daysToExpiry), (int) round($daysToDeplete ?? 0), 1);
                                $depletePercent = $daysToDeplete === null ? 100 : min(100, round($daysToDeplete / $timelineDays * 100, 1));
                                $expiryPercent = min(100, round(max(0, $daysToExpiry) / $timelineDays * 100, 1));
                                $isUrgent = in_array($risk, ['CRITICAL', 'HIGH'], true);
                                $simId = 'sim-'.$batch->id;
                            @endphp
                            <tr>
                                <td data-label="{{ __('batches.table.medicine_short') }}">
                                    <div class="ml-cell-title">{{ $batch->medicine->name }}</div>
                                    <div class="ml-cell-meta">
                                        {{ $batch->medicine->dosage_form }} &middot; {{ $batch->medicine->category }}
                                        @if($batch->medicine->requires_cold_chain)
                                            <span class="ml-cold-chain" role="img" title="{{ __('batches.cold_chain_required') }}" aria-label="{{ __('batches.cold_chain_required') }}">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path d="M12 2v20M2 12h20M4.9 4.9l14.2 14.2M19.1 4.9L4.9 19.1" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td data-label="{{ __('batches.table.batch_location') }}">
                                    <span class="ml-batch-code">{{ $batch->batch_number }}</span>
                                    <div class="ml-cell-meta">
                                        <svg class="ml-inline-icon" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $batch->storage_location ?? __('batches.default_storage_location') }}
                                    </div>
                                </td>

                                <td class="ml-cell-stock" data-label="{{ __('common.stock') }}">
                                    <span class="ml-stock-value">{{ number_format($batch->current_stock) }}</span>
                                    <span class="ml-stock-unit">{{ $batch->medicine->unit }}</span>
                                    <div class="ml-cell-meta">~{{ trans_choice('batches.units.per_day', $batch->daily_burn_rate, ['count' => $batch->daily_burn_rate]) }}</div>
                                </td>

                                <td data-label="{{ __('batches.table.expiry_runway') }}">
                                    <div class="ml-runway-text">
                                        <span class="{{ $isUrgent ? 'is-risk' : 'is-safe' }}">
                                            {{ $daysToExpiry <= 0 ? __('batches.already_expired') : trans_choice('batches.days_remaining', $daysToExpiry, ['count' => number_format($daysToExpiry)]) }}
                                        </span>
                                        <span class="ml-runway-sep" aria-hidden="true">&middot;</span>
                                        <span>
                                            {{ $daysToDeplete === null ? __('batches.stock_never_depletes') : __('batches.stock_depletes_in', ['days' => number_format($daysToDeplete, 0)]) }}
                                        </span>
                                    </div>

                                    <div class="ml-runway" aria-hidden="true">
                                        <span class="ml-runway-fill" style="width: {{ $depletePercent }}%"></span>
                                        <span class="ml-runway-mark {{ $daysToExpiry <= 60 ? 'is-urgent' : '' }}" style="left: {{ $expiryPercent }}%"></span>
                                    </div>

                                    <button
                                        type="button"
                                        id="sim-trigger-{{ $simId }}"
                                        class="ml-simulator-toggle"
                                        onclick="toggleSimulator('{{ $simId }}')"
                                        aria-expanded="false"
                                        aria-controls="{{ $simId }}"
                                    >
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                            <path d="M12 5v14M5 12h14" />
                                        </svg>
                                        {{ __('batches.simulator.toggle') }}
                                    </button>
                                </td>

                                <td class="is-right" data-label="{{ __('batches.table.projected_waste') }}">
                                    <div class="ml-waste-cell">
                                        <span class="ml-risk ml-risk-{{ $riskTone }}">{{ $riskLabel }}</span>
                                        @if($projectedWaste > 0)
                                            <span class="ml-waste-value">{{ trans_choice('batches.units.unit', $projectedWaste, ['count' => number_format($projectedWaste)]) }}</span>
                                            <span class="ml-cell-meta">Rp {{ number_format($projectedWaste * (float) $batch->unit_price_idr, 0, ',', '.') }}</span>
                                        @else
                                            <span class="ml-waste-value is-safe">{{ trans_choice('batches.units.unit', 0, ['count' => number_format(0)]) }}</span>
                                            <span class="ml-cell-meta">{{ __('batches.no_projected_waste') }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="is-right" data-label="{{ __('common.actions') }}">
                                    <div class="ml-inventory-actions">
                                        @if($projectedWaste > 0 && $batch->current_stock > 0 && ! $user->isDinkesAuditor())
                                            <a href="{{ route('transfers.propose', $batch) }}" class="ml-btn-primary ml-btn-sm">
                                                <span>{{ __('batches.propose') }}</span>
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                                    <path d="M5 12h14M13 6l6 6-6 6" />
                                                </svg>
                                            </a>
                                        @endif

                                        @if(! $user->isDinkesAuditor())
                                            <a href="{{ route('batches.edit', $batch) }}" class="ml-btn-quiet ml-btn-sm">{{ __('common.edit') }}</a>

                                            <form method="POST" action="{{ route('batches.destroy', $batch) }}" onsubmit='return confirm(@js(__('batches.confirm_delete', ['batch' => $batch->batch_number])))'>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ml-btn-quiet ml-btn-sm is-danger">{{ __('common.delete') }}</button>
                                            </form>
                                        @else
                                            <span class="ml-cell-meta">{{ __('common.read_only') }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <tr class="ml-simulator-row" id="{{ $simId }}" hidden>
                                <td colspan="6">
                                    <div class="ml-simulator">
                                        <div class="ml-simulator-head">
                                            <div>
                                                <p class="ml-page-eyebrow">{{ __('batches.simulator.eyebrow') }}</p>
                                                <h3 class="ml-simulator-title">{{ __('batches.simulator.title', ['medicine' => $batch->medicine->name]) }}</h3>
                                                <p class="ml-simulator-sub">{{ __('batches.simulator.subtitle') }}</p>
                                            </div>
                                            <button type="button" class="ml-icon-btn" onclick="toggleSimulator('{{ $simId }}')" aria-label="{{ __('batches.simulator.close_aria') }}">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path d="M18 6L6 18M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="ml-simulator-grid">
                                            <div>
                                                <label for="rate-{{ $simId }}" class="ml-simulator-label">
                                                    {{ __('batches.simulator.daily_rate') }}
                                                    <output id="label-{{ $simId }}" class="ml-simulator-current">{{ $batch->daily_burn_rate }}</output>
                                                    {{ __('batches.units.per_day_label') }}
                                                </label>
                                                <input
                                                    id="rate-{{ $simId }}"
                                                    type="range"
                                                    class="ml-range"
                                                    min="0.5"
                                                    max="{{ max(20, (float) $batch->daily_burn_rate * 3) }}"
                                                    step="0.5"
                                                    value="{{ $batch->daily_burn_rate }}"
                                                    aria-describedby="scale-{{ $simId }}"
                                                    oninput="runSimulation('{{ $simId }}', {{ (int) $batch->current_stock }}, {{ $daysToExpiry }}, this.value)"
                                                >
                                                <div class="ml-simulator-scale" id="scale-{{ $simId }}">
                                                    <span>0,5 {{ __('batches.units.per_day_label') }}</span>
                                                    <span>{{ __('batches.simulator.current', ['count' => $batch->daily_burn_rate]) }}</span>
                                                    <span>{{ trans_choice('batches.units.per_day', max(20, (float) $batch->daily_burn_rate * 3), ['count' => max(20, (float) $batch->daily_burn_rate * 3)]) }}</span>
                                                </div>
                                            </div>

                                            <div class="ml-simulator-metrics">
                                                <div class="ml-simulator-metric">
                                                    <span class="ml-simulator-metric-label">{{ __('batches.simulator.projected_waste') }}</span>
                                                    <span id="waste-{{ $simId }}" class="ml-simulator-metric-value {{ $projectedWaste > 0 ? 'is-risk' : 'is-safe' }}">
                                                        {{ trans_choice('batches.units.unit', $projectedWaste, ['count' => number_format($projectedWaste)]) }}
                                                    </span>
                                                </div>
                                                <div class="ml-simulator-metric">
                                                    <span class="ml-simulator-metric-label">{{ __('batches.simulator.stock_depleted') }}</span>
                                                    <span id="deplete-{{ $simId }}" class="ml-simulator-metric-value">
                                                        {{ $daysToDeplete === null ? __('batches.simulator.never_depletes') : trans_choice('batches.days_remaining', (int) $daysToDeplete, ['count' => number_format($daysToDeplete, 0)]) }}
                                                    </span>
                                                </div>
                                                <div class="ml-simulator-metric">
                                                    <span class="ml-simulator-metric-label">{{ __('batches.simulator.risk_status') }}</span>
                                                    <span id="risk-{{ $simId }}" class="ml-risk ml-risk-{{ $riskTone }}">{{ $riskLabel }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="ml-pagination-wrap">
                {{ $batches->links() }}
            </div>
        @endif
    </section>
</div>
@endsection

@section('scripts')
<script>
    function toggleSimulator(id) {
        const row = document.getElementById(id);
        if (!row) {
            return;
        }

        const trigger = document.getElementById('sim-trigger-' + id);
        const isOpening = row.hidden;

        row.hidden = !isOpening;

        if (trigger) {
            trigger.setAttribute('aria-expanded', String(isOpening));
        }
    }

    function runSimulation(id, currentStock, daysToExpiry, newBurnRate) {
        const burnRate = Number.parseFloat(newBurnRate);
        const numberFormatter = new Intl.NumberFormat('id-ID');
        const decimalFormatter = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 1 });

        document.getElementById('label-' + id).textContent = decimalFormatter.format(burnRate);

        const remainingDays = Math.max(0, daysToExpiry);
        const expectedConsumption = Math.floor(burnRate * remainingDays);
        const projectedWaste = Math.max(0, currentStock - expectedConsumption);
        const daysToDeplete = burnRate > 0 ? currentStock / burnRate : null;
        const wasteRatio = currentStock > 0 ? projectedWaste / currentStock : 0;

        let newRisk = 'LOW';
        let newRiskLabel = @json(__('batches.risk.low'));

        if (projectedWaste > 0) {
            if (remainingDays <= 30) {
                newRisk = 'CRITICAL';
                newRiskLabel = @json(__('batches.risk.critical'));
            } else if (remainingDays <= 60 || wasteRatio >= 0.25) {
                newRisk = 'HIGH';
                newRiskLabel = @json(__('batches.risk.high'));
            } else if (remainingDays <= 90) {
                newRisk = 'MEDIUM';
                newRiskLabel = @json(__('batches.risk.medium'));
            }
        }

        const wasteElement = document.getElementById('waste-' + id);
        wasteElement.textContent = numberFormatter.format(projectedWaste) + ' ' + @json(__('common.units'));
        wasteElement.className = 'ml-simulator-metric-value ' + (projectedWaste > 0 ? 'is-risk' : 'is-safe');

        const depleteElement = document.getElementById('deplete-' + id);
        depleteElement.textContent = daysToDeplete === null
            ? @json(__('batches.simulator.never_depletes'))
            : decimalFormatter.format(daysToDeplete) + ' ' + @json(__('common.day'));

        const riskElement = document.getElementById('risk-' + id);
        riskElement.textContent = newRiskLabel;
        riskElement.className = 'ml-risk ml-risk-' + newRisk.toLowerCase();
    }
</script>
@endsection
