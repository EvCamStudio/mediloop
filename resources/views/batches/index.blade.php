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

    @if($errors->has('csv_file'))
        <div class="ml-alert ml-alert-danger" role="alert" style="margin-bottom: 1.25rem; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.875rem 1.25rem; border-radius: 0.75rem;">
            <div class="ml-alert-row" style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span><strong>Gagal Impor CSV:</strong> {{ $errors->first('csv_file') }}</span>
                </div>
                <button type="button" onclick="this.closest('.ml-alert-danger').remove()" aria-label="Tutup notifikasi" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #991b1b;">&times;</button>
            </div>
        </div>
    @endif

    @if($errors->has('csv_file'))
        <div class="ml-alert ml-alert-danger" role="alert" style="margin-bottom: 1.25rem; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.875rem 1.25rem; border-radius: 0.75rem;">
            <div class="ml-alert-row" style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span><strong>Gagal Impor CSV:</strong> {{ $errors->first('csv_file') }}</span>
                </div>
                <button type="button" onclick="this.closest('.ml-alert-danger').remove()" aria-label="Tutup notifikasi" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #991b1b;">&times;</button>
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
            <div class="ml-page-actions flex items-center gap-2">
                <button type="button" onclick="document.getElementById('ml-csv-modal').style.display='flex'" class="ml-btn-quiet inline-flex items-center gap-1.5">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span>Import Stok (CSV)</span>
                </button>
                <a href="{{ route('batches.create') }}" class="ml-btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('batches.input_batch') }}</span>
                </a>
            </div>
        @endif
    </header>

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
                            <th scope="col" style="min-width: 220px;">{{ __('batches.table.medicine') }}</th>
                            <th scope="col" style="min-width: 220px;">{{ __('batches.table.batch_location') }}</th>
                            <th scope="col" style="min-width: 150px;">{{ __('common.stock') }}</th>
                            <th scope="col" style="min-width: 220px;">{{ __('batches.table.expiry_runway') }}</th>
                            <th scope="col" style="min-width: 170px;">{{ __('batches.table.projected_waste') }}</th>
                            <th scope="col" class="is-right" style="min-width: 230px;"><span class="ml-sr-only">{{ __('common.actions') }}</span></th>
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
                                    <div class="ml-batch-cell">
                                        <span class="ml-batch-code">{{ $batch->batch_number }}</span>
                                        <div class="ml-cell-meta">{{ $batch->storage_location ?? __('batches.default_storage_location') }}</div>
                                    </div>
                                </td>

                                <td data-label="{{ __('common.stock') }}">
                                    <div class="ml-stock-cell">
                                        <div class="ml-stock-main">
                                            <span class="ml-stock-value">{{ number_format($batch->current_stock) }}</span>
                                            <span class="ml-stock-unit">{{ $batch->medicine->unit }}</span>
                                        </div>
                                        <div class="ml-cell-meta">~{{ trans_choice('batches.units.per_day', $batch->daily_burn_rate, ['count' => $batch->daily_burn_rate]) }}</div>
                                    </div>
                                </td>
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

                                <td data-label="{{ __('batches.table.projected_waste') }}">
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

                                <td class="is-right" data-label="{{ __('common.actions') }}" style="white-space: nowrap;">
                                    <div class="ml-inventory-actions">
                                        @if($batch->current_stock > 0 && ! $user->isDinkesAuditor())
                                            <a href="{{ route('transfers.propose', $batch) }}" class="ml-btn-primary ml-btn-sm" title="{{ __('batches.propose') }}">
                                                <span>{{ __('batches.propose') }}</span>
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                                    <path d="M5 12h14M13 6l6 6-6 6" />
                                                </svg>
                                            </a>
                                        @elseif($batch->current_stock > 0 && ! $user->isDinkesAuditor())
                                            <a href="{{ route('transfers.propose', $batch) }}" class="ml-btn-quiet ml-btn-sm" title="Usulkan redistribusi batch ini">
                                                <span>Usulkan</span>
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

    <!-- Modal CSV Import -->
    <div id="ml-csv-modal" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none; position: fixed; inset: 0; z-index: 999; align-items: center; justify-content: center;">
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);" onclick="document.getElementById('ml-csv-modal').style.display='none'"></div>

        <div style="position: relative; z-index: 1000; width: 100%; max-width: 540px; margin: 1rem; background: #fff; border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden; text-align: left;">
            <!-- Header -->
            <div style="background: #f8fafc; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #0b1f4b;" id="modal-title">Impor Data Stok Obat (CSV)</h3>
                    <p style="margin: 0.25rem 0 0; font-size: 0.75rem; color: #52627f;">Unggah berkas spreadsheet/CSV inventaris untuk sinkronisasi massal</p>
                </div>
                <button type="button" onclick="document.getElementById('ml-csv-modal').style.display='none'" style="background: none; border: none; cursor: pointer; color: #94a3b8; padding: 0.25rem; border-radius: 0.5rem;">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Form -->
            <form id="ml-csv-import-form" method="POST" action="{{ route('batches.importCsv') }}" enctype="multipart/form-data" onsubmit="return handleCsvSubmit(event)" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                @csrf

                <div id="ml-csv-client-error" style="display: none; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600;"></div>

                <!-- Instructions & Template Box -->
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.75rem; padding: 0.875rem; display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; font-size: 0.75rem;">
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <p style="margin: 0; font-weight: 700; color: #1d4ed8;">Format Kolom CSV yang Didukung:</p>
                        <p style="margin: 0; color: #475569; font-size: 0.72rem; line-height: 1.4;">
                            <code>nama_obat</code>, <code>nomor_batch</code>, <code>stok_saat_ini</code>, <code>tanggal_kadaluwarsa</code>, <code>laju_pemakaian_harian</code>
                        </p>
                    </div>
                    <a href="{{ route('batches.csvTemplate') }}" style="flex-shrink: 0; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; background: #fff; border: 1px solid #93c5fd; color: #1d4ed8; font-weight: 700; border-radius: 0.5rem; font-size: 0.7rem; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh Template</span>
                    </a>
                </div>

                <!-- Dropzone File Input using LABEL -->
                <label for="csv_file_input" id="csv-dropzone" style="display: block; border: 2px dashed #bfdbfe; background: #f8fafc; border-radius: 0.75rem; padding: 1.5rem; text-align: center; cursor: pointer; transition: all 0.2s;">
                    <svg id="csv-icon-upload" style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.5rem; color: #1d4ed8;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p style="margin: 0; font-size: 0.85rem; font-weight: 700; color: #0b1f4b;" id="csv-prompt-text">Klik di sini untuk memilih berkas CSV</p>
                    <p style="margin: 0.25rem 0 0; font-size: 0.72rem; color: #64748b;">Mendukung format .csv atau .txt (Maksimal 5 MB)</p>
                    <div id="selected-file-details" style="display: none; margin-top: 0.75rem; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 0.5rem 0.75rem; border-radius: 0.5rem; color: #166534; font-size: 0.75rem; font-weight: 600;">
                        <span id="selected-file-name"></span>
                    </div>
                </label>
                <input type="file" name="csv_file" id="csv_file_input" accept=".csv,.txt" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0;" onchange="handleFileChange(this)">

                <!-- Action Buttons -->
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem; padding-top: 0.5rem;">
                    <button type="button" onclick="document.getElementById('ml-csv-modal').style.display='none'" style="padding: 0.5rem 1rem; font-size: 0.75rem; font-weight: 700; color: #64748b; background: none; border: none; cursor: pointer; border-radius: 0.5rem;">
                        Batal
                    </button>
                    <button type="submit" id="ml-csv-submit-btn" class="ml-btn-primary" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1.25rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span id="ml-csv-submit-text">Mulai Impor &amp; Kalkulasi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function handleFileChange(input) {
        const errorBox = document.getElementById('ml-csv-client-error');
        if (errorBox) errorBox.style.display = 'none';

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const sizeKb = (file.size / 1024).toFixed(1);
            const nameEl = document.getElementById('selected-file-name');
            const detailsEl = document.getElementById('selected-file-details');
            const dropzone = document.getElementById('csv-dropzone');
            const promptText = document.getElementById('csv-prompt-text');

            if (nameEl) nameEl.textContent = 'Berkas dipilih: ' + file.name + ' (' + sizeKb + ' KB)';
            if (detailsEl) detailsEl.style.display = 'block';
            if (dropzone) {
                dropzone.style.borderColor = '#22c55e';
                dropzone.style.background = '#f0fdf4';
            }
            if (promptText) promptText.textContent = 'Klik untuk mengganti berkas';
        }
    }

    function handleCsvSubmit(e) {
        const input = document.getElementById('csv_file_input');
        const errorBox = document.getElementById('ml-csv-client-error');

        if (!input.files || input.files.length === 0) {
            e.preventDefault();
            if (errorBox) {
                errorBox.textContent = 'Silakan pilih berkas CSV terlebih dahulu dari komputer Anda.';
                errorBox.style.display = 'block';
            } else {
                alert('Silakan pilih berkas CSV terlebih dahulu dari komputer Anda.');
            }
            return false;
        }

        const btn = document.getElementById('ml-csv-submit-btn');
        const txt = document.getElementById('ml-csv-submit-text');
        if (btn && txt) {
            btn.disabled = true;
            btn.style.opacity = '0.7';
            txt.textContent = 'Sedang Memproses Impor & Kalkulasi...';
        }
        return true;
    }

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

    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('ml-csv-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
    });
</script>
@endsection
