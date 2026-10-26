@extends('layouts.mediloop', ['title' => __('batches.edit.page_title', ['batch' => $batch->batch_number])])

@section('content')
@php
    $statusTone = match ($batch->status) {
        'ACTIVE' => 'active',
        'RESERVED' => 'pending',
        'DEPLETED' => 'inactive',
        'EXPIRED' => 'suspended',
        default => 'inactive',
    };
    $statusLabel = match ($batch->status) {
        'ACTIVE' => __('batches.status.active'),
        'RESERVED' => __('batches.status.allocated'),
        'DEPLETED' => __('batches.status.depleted'),
        'EXPIRED' => __('batches.status.expired'),
        default => $batch->status,
    };
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="{{ __('batches.breadcrumb_aria') }}">
        <a href="{{ route('dashboard') }}">{{ __('navigation.dashboard') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('batches.index') }}">{{ __('batches.inventory_label') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ __('batches.edit.breadcrumb', ['batch' => $batch->batch_number]) }}</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">{{ __('batches.inventory_label') }}</p>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">{{ __('batches.edit.heading') }}</h1>
                <span class="ml-facility-chip">{{ $batch->facility->name }}</span>
            </div>
            <p class="ml-page-sub">
                {{ __('batches.edit.subtitle', ['batch' => $batch->batch_number]) }}
            </p>
        </div>
        <div class="ml-page-actions">
            <a href="{{ route('batches.index') }}" class="ml-btn-quiet">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M19 12H5m0 0l7 7m-7-7l7-7" />
                </svg>
                {{ __('batches.back_to_inventory') }}
            </a>
        </div>
    </header>

    <div class="ml-alert ml-alert-info" role="note">
        <strong>{{ __('batches.edit.audit_title') }}</strong>
        <p class="ml-alert-copy">
            {{ __('batches.edit.audit_body', ['name' => $user->name]) }}
        </p>
    </div>

    <form method="POST" action="{{ route('batches.update', $batch) }}" novalidate data-validate>
        @csrf
        @method('PUT')

        <x-form.summary />

        <div class="ml-two-col">
            <div>
                <section class="ml-card" aria-labelledby="edit-batch-title">
                    <div class="ml-card-head">
                        <h2 class="ml-card-title" id="edit-batch-title">{{ __('batches.edit.form_title') }}</h2>
                        <p class="ml-card-sub">{{ __('batches.edit.form_sub') }}</p>
                    </div>
                    <div class="ml-card-body">
                        <div class="ml-form-grid is-tight">
                            <div class="ml-field">
                                <label for="current_stock" class="ml-label">{{ __('batches.create.current_stock') }} <span class="ml-required">*</span></label>
                                <input
                                    type="number"
                                    id="current_stock"
                                    data-error-required="{{ __('batches.create.current_stock_required') }}"
                                    name="current_stock"
                                    class="ml-input is-mono @error('current_stock') is-invalid @enderror"
                                    value="{{ old('current_stock', $batch->current_stock) }}"
                                    min="0"
                                    required
                                >
                                <x-form.error for="current_stock" />
                                <p class="ml-help">{{ __('batches.create.current_stock_help') }}</p>
                            </div>

                            <div class="ml-field">
                                <label for="daily_burn_rate" class="ml-label">{{ __('batches.create.daily_burn_rate') }} <span class="ml-required">*</span></label>
                                <input
                                    type="number"
                                    step="0.01"
                                    id="daily_burn_rate"
                                    data-error-required="{{ __('batches.create.daily_burn_rate_required') }}"
                                    name="daily_burn_rate"
                                    class="ml-input is-mono @error('daily_burn_rate') is-invalid @enderror"
                                    value="{{ old('daily_burn_rate', $batch->daily_burn_rate) }}"
                                    min="0"
                                    required
                                >
                                <x-form.error for="daily_burn_rate" />
                                <p class="ml-help">{{ __('batches.create.daily_burn_rate_help') }}</p>
                            </div>

                            <div class="ml-field">
                                <label for="expiry_date" class="ml-label">{{ __('batches.create.expiry_date') }} <span class="ml-required">*</span></label>
                                <input
                                    type="date"
                                    id="expiry_date"
                                    data-error-required="{{ __('batches.create.expiry_date_required') }}"
                                    name="expiry_date"
                                    class="ml-input @error('expiry_date') is-invalid @enderror"
                                    value="{{ old('expiry_date', $batch->expiry_date->toDateString()) }}"
                                    required
                                >
                                <x-form.error for="expiry_date" />
                                <p class="ml-help">{{ __('batches.create.expiry_date_help') }}</p>
                            </div>

                            <div class="ml-field">
                                <label for="unit_price_idr" class="ml-label">{{ __('batches.create.batch_unit_price') }} <span class="ml-required">*</span></label>
                                <input
                                    type="number"
                                    id="unit_price_idr"
                                    data-error-required="{{ __('batches.edit.unit_price_required') }}"
                                    name="unit_price_idr"
                                    class="ml-input is-mono @error('unit_price_idr') is-invalid @enderror"
                                    value="{{ old('unit_price_idr', $batch->unit_price_idr) }}"
                                    min="0"
                                    required
                                >
                                <x-form.error for="unit_price_idr" />
                                <p class="ml-help">{{ __('batches.edit.unit_price_help') }}</p>
                            </div>

                            <div class="ml-field">
                                <label for="storage_location" class="ml-label">{{ __('batches.create.storage_location') }} <span class="ml-optional">({{ __('common.optional') }})</span></label>
                                <input
                                    type="text"
                                    id="storage_location"
                                    name="storage_location"
                                    class="ml-input @error('storage_location') is-invalid @enderror"
                                    value="{{ old('storage_location', $batch->storage_location) }}"
                                    placeholder="{{ __('batches.edit.storage_location_placeholder') }}"
                                >
                                <x-form.error for="storage_location" />
                                <p class="ml-help">{{ __('batches.create.storage_location_help') }}</p>
                            </div>

                            <div class="ml-field">
                                <label for="status" class="ml-label">{{ __('batches.edit.status_label') }} <span class="ml-required">*</span></label>
                                <select id="status" name="status" class="ml-select @error('status') is-invalid @enderror" required>
                                    <option value="ACTIVE" @selected(old('status', $batch->status) === 'ACTIVE')>{{ __('batches.edit.status_active') }}</option>
                                    <option value="RESERVED" @selected(old('status', $batch->status) === 'RESERVED')>{{ __('batches.status.allocated') }}</option>
                                    <option value="DEPLETED" @selected(old('status', $batch->status) === 'DEPLETED')>{{ __('batches.status.depleted') }}</option>
                                    <option value="EXPIRED" @selected(old('status', $batch->status) === 'EXPIRED')>{{ __('batches.status.expired') }}</option>
                                </select>
                                <x-form.error for="status" />
                                <p class="ml-help">{{ __('batches.edit.status_help') }}</p>
                            </div>
                        </div>

                        <div class="ml-action-row">
                            <a href="{{ route('batches.index') }}" class="ml-btn-quiet">{{ __('common.cancel') }}</a>
                            <button type="submit" class="ml-btn-primary">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('common.save_changes') }}
                            </button>
                        </div>
                    </div>
                </section>
            </div>

            <aside>
                <section class="ml-card ml-sticky" aria-labelledby="batch-summary-title">
                    <div class="ml-card-head">
                        <h2 class="ml-card-title" id="batch-summary-title">{{ __('batches.edit.summary_title') }}</h2>
                        <p class="ml-card-sub">{{ __('batches.edit.summary_sub') }}</p>
                    </div>
                    <div class="ml-card-body">
                        <div class="ml-summary-medicine">{{ $batch->medicine->name }}</div>
                        <p class="ml-cell-meta">
                            {{ $batch->medicine->dosage_form }}, {{ __('batches.edit.unit_label') }} {{ $batch->medicine->unit }}@if($batch->medicine->requires_cold_chain), {{ __('batches.edit.cold_chain_suffix') }} @endif
                        </p>

                        <div class="ml-detail-grid ml-summary-details">
                            <div>
                                <div class="ml-detail-label">{{ __('batches.edit.batch_number_label') }}</div>
                                <div class="ml-detail-value is-mono">{{ $batch->batch_number }}</div>
                            </div>
                            <div>
                                <div class="ml-detail-label">{{ __('common.status') }}</div>
                                <div class="ml-detail-value">
                                    <span class="ml-status ml-status-{{ $statusTone }}">{{ $statusLabel }}</span>
                                </div>
                            </div>
                            <div>
                                <div class="ml-detail-label">{{ __('batches.facility_label') }}</div>
                                <div class="ml-detail-value">{{ $batch->facility->name }}</div>
                            </div>
                            <div>
                                <div class="ml-detail-label">{{ __('batches.expiry_label') }}</div>
                                <div class="ml-detail-value">{{ $batch->expiry_date->format('d M Y') }}</div>
                            </div>
                            <div>
                                <div class="ml-detail-label">{{ __('batches.edit.current_stock_label') }}</div>
                                <div class="ml-detail-value">{{ number_format($batch->current_stock, 0, ',', '.') }} {{ $batch->medicine->unit }}</div>
                            </div>
                            <div>
                                <div class="ml-detail-label">{{ __('batches.edit.unit_price_label') }}</div>
                                <div class="ml-detail-value">Rp {{ number_format($batch->unit_price_idr, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="ml-forecast-note">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ __('batches.edit.summary_note') }}</span>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </form>
</div>
@endsection
