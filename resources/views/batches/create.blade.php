@extends('layouts.mediloop', ['title' => __('batches.create.page_title')])

@section('content')
@php
    $facilityLabel = $user->isSysadmin()
        ? __('batches.create.admin_access')
        : ($user->facility?->name ?? __('batches.create.registered_facility'));
    $selectedFacilityId = old('facility_id', $user->facility_id ?? $facilities->first()?->id);
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="{{ __('batches.breadcrumb_aria') }}">
        <a href="{{ route('dashboard') }}">{{ __('navigation.dashboard') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('batches.index') }}">{{ __('batches.inventory_label') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ __('batches.create.breadcrumb') }}</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">{{ __('batches.inventory_label') }}</p>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">{{ __('batches.create.heading') }}</h1>
                <span class="ml-facility-chip">{{ $facilityLabel }}</span>
            </div>
            <p class="ml-page-sub">
                {{ __('batches.create.subtitle') }}
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

    <div class="ml-alert ml-alert-info ml-compliance-banner" role="note">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z" />
            <path d="M12 8v4m0 4h.01" />
        </svg>
        <div>
            <strong>{{ __('batches.create.compliance_title') }}</strong>
            <p>
                {{ __('batches.create.compliance_body') }}
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('batches.store') }}" id="batchForm" novalidate data-validate>
        @csrf

        <x-form.summary />

        <div class="ml-two-col">
            <div>
                @if($user->isSysadmin())
                    <section class="ml-card" aria-labelledby="placement-title">
                        <div class="ml-card-head">
                            <h2 class="ml-card-title" id="placement-title">{{ __('batches.create.facility_section_title') }}</h2>
                            <p class="ml-card-sub">{{ __('batches.create.facility_section_sub') }}</p>
                        </div>
                        <div class="ml-card-body">
                            <div class="ml-field is-last">
                                <label for="facility_id" class="ml-label">{{ __('batches.create.facility_label') }} <span class="ml-required">*</span></label>
                                <select id="facility_id" name="facility_id" class="ml-select @error('facility_id') is-invalid @enderror" required>
                                    @foreach($facilities as $facility)
                                        <option value="{{ $facility->id }}" @selected((string) $selectedFacilityId === (string) $facility->id)>
                                            {{ $facility->name }} ({{ $facility->type_label }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-form.error for="facility_id" />
                                <p class="ml-help">{{ __('batches.create.facility_help') }}</p>
                            </div>
                        </div>
                    </section>
                @endif

                <section class="ml-card" aria-labelledby="medicine-title">
                    <div class="ml-card-head">
                        <div class="ml-step-head">
                            <div class="ml-step-title">
                                <span class="ml-step-index" aria-hidden="true">1</span>
                                <div>
                                    <h2 class="ml-card-title" id="medicine-title">{{ __('batches.create.medicine_section_title') }}</h2>
                                    <p class="ml-card-sub">{{ __('batches.create.medicine_section_sub') }}</p>
                                </div>
                            </div>
                            <label class="ml-form-toggle" for="toggleNewMedicine">
                                <input
                                    type="checkbox"
                                    id="toggleNewMedicine"
                                    name="is_new_medicine"
                                    value="1"
                                    aria-controls="existingMedicineSection newMedicineSection"
                                    @checked(old('is_new_medicine'))
                                >
                                <span>{{ __('batches.create.new_medicine_toggle') }}</span>
                            </label>
                        </div>
                    </div>
                    <div class="ml-card-body">
                        <div class="ml-form-stack">
                            <div id="existingMedicineSection" @if(old('is_new_medicine')) hidden @endif>
                                <label for="medicine_id" class="ml-label">{{ __('batches.create.medicine_label') }} <span class="ml-required">*</span></label>
                                <select id="medicine_id" name="medicine_id" class="ml-select @error('medicine_id') is-invalid @enderror" required data-error-required="{{ __('batches.create.medicine_required') }}">
                                    <option value="">{{ __('batches.create.medicine_placeholder') }}</option>
                                    @foreach($medicines as $medicine)
                                        <option
                                            value="{{ $medicine->id }}"
                                            data-price="{{ $medicine->unit_price_idr }}"
                                            data-unit="{{ $medicine->unit }}"
                                            data-cold="{{ $medicine->requires_cold_chain ? '1' : '0' }}"
                                            @selected((string) old('medicine_id') === (string) $medicine->id)
                                        >
                                            {{ $medicine->name }} ({{ $medicine->dosage_form }}), {{ $medicine->unit }}, Rp {{ number_format($medicine->unit_price_idr, 0, ',', '.') }}@if($medicine->requires_cold_chain), {{ __('batches.create.cold_chain_suffix') }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                <x-form.error for="medicine_id" />
                                <p class="ml-help">{{ __('batches.create.medicine_help', ['toggle' => __('batches.create.new_medicine_toggle')]) }}</p>
                            </div>

                            <div id="newMedicineSection" class="ml-subpanel" @if(! old('is_new_medicine')) hidden @endif>
                                <div class="ml-subpanel-head">
                                    <span class="ml-subpanel-title">{{ __('batches.create.new_medicine_title') }}</span>
                                </div>

                                <div class="ml-form-grid is-tight">
                                    <div class="ml-field">
                                        <label for="new_medicine_name" class="ml-label">{{ __('batches.create.medicine_name') }} <span class="ml-required">*</span></label>
                                        <input
                                            type="text"
                                            id="new_medicine_name"
                                            name="new_medicine_name"
                                            class="ml-input @error('new_medicine_name') is-invalid @enderror" required data-error-required="{{ __('batches.create.medicine_name_required') }}"
                                            value="{{ old('new_medicine_name') }}"
                                            placeholder="{{ __('batches.create.medicine_name_placeholder') }}"
                                        >
                                        <x-form.error for="new_medicine_name" />
                                    </div>

                                    <div class="ml-field">
                                        <label for="new_generic_name" class="ml-label">{{ __('batches.create.generic_name') }} <span class="ml-required">*</span></label>
                                        <input
                                            type="text"
                                            id="new_generic_name"
                                            name="new_generic_name"
                                            class="ml-input @error('new_generic_name') is-invalid @enderror" required data-error-required="{{ __('batches.create.generic_name_required') }}"
                                            value="{{ old('new_generic_name') }}"
                                            placeholder="{{ __('batches.create.generic_name_placeholder') }}"
                                        >
                                        <x-form.error for="new_generic_name" />
                                    </div>

                                    <div class="ml-field">
                                        <label for="new_category" class="ml-label">{{ __('batches.create.category') }} <span class="ml-required">*</span></label>
                                        <select id="new_category" name="new_category" class="ml-select @error('new_category') is-invalid @enderror" required data-error-required="{{ __('batches.create.category_required') }}">
                                            <option value="Antibiotic" @selected(old('new_category') === 'Antibiotic')>{{ __('batches.create.category_antibiotic') }}</option>
                                            <option value="Cardiovascular" @selected(old('new_category') === 'Cardiovascular')>{{ __('batches.create.category_cardiovascular') }}</option>
                                            <option value="Endocrine" @selected(old('new_category') === 'Endocrine')>{{ __('batches.create.category_endocrine') }}</option>
                                            <option value="Analgesic" @selected(old('new_category') === 'Analgesic')>{{ __('batches.create.category_analgesic') }}</option>
                                            <option value="Emergency" @selected(old('new_category') === 'Emergency')>{{ __('batches.create.category_emergency') }}</option>
                                            <option value="Respiratory" @selected(old('new_category') === 'Respiratory')>{{ __('batches.create.category_respiratory') }}</option>
                                            <option value="Other" @selected(old('new_category') === 'Other')>{{ __('batches.create.category_other') }}</option>
                                        </select>
                                        <x-form.error for="new_category" />
                                    </div>

                                    <div class="ml-field">
                                        <label for="new_dosage_form" class="ml-label">{{ __('batches.create.dosage_form') }} <span class="ml-required">*</span></label>
                                        <input
                                            type="text"
                                            id="new_dosage_form"
                                            name="new_dosage_form"
                                            class="ml-input @error('new_dosage_form') is-invalid @enderror" required data-error-required="{{ __('batches.create.dosage_form_required') }}"
                                            value="{{ old('new_dosage_form') }}"
                                            placeholder="{{ __('batches.create.dosage_form_placeholder') }}"
                                        >
                                        <x-form.error for="new_dosage_form" />
                                    </div>

                                    <div class="ml-field">
                                        <label for="new_unit" class="ml-label">{{ __('batches.create.package_unit') }} <span class="ml-required">*</span></label>
                                        <input
                                            type="text"
                                            id="new_unit"
                                            name="new_unit"
                                            class="ml-input @error('new_unit') is-invalid @enderror" required data-error-required="{{ __('batches.create.package_unit_required') }}"
                                            value="{{ old('new_unit', 'Box') }}"
                                            placeholder="{{ __('batches.create.package_unit_placeholder') }}"
                                        >
                                        <x-form.error for="new_unit" />
                                    </div>

                                    <div class="ml-field">
                                        <label for="new_unit_price_idr" class="ml-label">{{ __('batches.create.unit_price') }} <span class="ml-required">*</span></label>
                                        <input
                                            type="number"
                                            id="new_unit_price_idr"
                                            name="new_unit_price_idr"
                                            class="ml-input is-mono @error('new_unit_price_idr') is-invalid @enderror" required data-error-required="{{ __('batches.create.unit_price_required') }}"
                                            value="{{ old('new_unit_price_idr', 50000) }}"
                                            min="0"
                                        >
                                        <x-form.error for="new_unit_price_idr" />
                                    </div>

                                    <div class="ml-field is-full">
                                        <label class="ml-checkbox-row">
                                            <input
                                                type="checkbox"
                                                id="new_requires_cold_chain"
                                                name="new_requires_cold_chain"
                                                value="1"
                                                @checked(old('new_requires_cold_chain'))
                                            >
                                            <span>{{ __('batches.create.cold_chain') }}</span>
                                        </label>
                                    </div>

                                    <div class="ml-field is-full">
                                        <label for="new_handling_instructions" class="ml-label">{{ __('batches.create.handling_instructions') }} <span class="ml-optional">({{ __('common.optional') }})</span></label>
                                        <input
                                            type="text"
                                            id="new_handling_instructions"
                                            name="new_handling_instructions"
                                            class="ml-input @error('new_handling_instructions') is-invalid @enderror"
                                            value="{{ old('new_handling_instructions') }}"
                                            placeholder="{{ __('batches.create.handling_instructions_placeholder') }}"
                                        >
                                        <x-form.error for="new_handling_instructions" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ml-card" aria-labelledby="batch-title">
                    <div class="ml-card-head">
                        <div class="ml-step-head">
                            <div class="ml-step-title">
                                <span class="ml-step-index" aria-hidden="true">2</span>
                                <div>
                                    <h2 class="ml-card-title" id="batch-title">{{ __('batches.create.batch_section_title') }}</h2>
                                    <p class="ml-card-sub">{{ __('batches.create.batch_section_sub') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ml-card-body">
                        <div class="ml-form-grid is-tight">
                            <div class="ml-field">
                                <label for="batch_number" class="ml-label">{{ __('batches.create.batch_number') }} <span class="ml-required">*</span></label>
                                <input
                                    type="text"
                                    id="batch_number"
                                    data-error-required="{{ __('batches.create.batch_number_required') }}"
                                    name="batch_number"
                                    class="ml-input is-mono is-uppercase @error('batch_number') is-invalid @enderror"
                                    value="{{ old('batch_number') }}"
                                    placeholder="{{ __('batches.create.batch_number_placeholder') }}"
                                    autocomplete="off"
                                    spellcheck="false"
                                    required
                                >
                                <x-form.error for="batch_number" />
                                <p class="ml-help">{{ __('batches.create.batch_number_help') }}</p>
                            </div>

                            <div class="ml-field">
                                <label for="expiry_date" class="ml-label">{{ __('batches.create.expiry_date') }} <span class="ml-required">*</span></label>
                                <input
                                    type="date"
                                    id="expiry_date"
                                    data-error-required="{{ __('batches.create.expiry_date_required') }}"
                                    name="expiry_date"
                                    class="ml-input @error('expiry_date') is-invalid @enderror"
                                    value="{{ old('expiry_date') }}"
                                    required
                                >
                                <x-form.error for="expiry_date" />
                                <p class="ml-help">{{ __('batches.create.expiry_date_help') }}</p>
                            </div>

                            <div class="ml-field">
                                <label for="current_stock" class="ml-label">{{ __('batches.create.current_stock') }} <span class="ml-required">*</span></label>
                                <input
                                    type="number"
                                    id="current_stock"
                                    data-error-required="{{ __('batches.create.current_stock_required') }}"
                                    name="current_stock"
                                    class="ml-input is-mono @error('current_stock') is-invalid @enderror"
                                    value="{{ old('current_stock', 100) }}"
                                    min="1"
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
                                    value="{{ old('daily_burn_rate', 2.0) }}"
                                    min="0"
                                    required
                                >
                                <x-form.error for="daily_burn_rate" />
                                <p class="ml-help">{{ __('batches.create.daily_burn_rate_help') }}</p>
                            </div>

                            <div class="ml-field">
                                <label for="unit_price_idr" class="ml-label">{{ __('batches.create.batch_unit_price') }}</label>
                                <input
                                    type="number"
                                    id="unit_price_idr"
                                    name="unit_price_idr"
                                    class="ml-input is-mono @error('unit_price_idr') is-invalid @enderror"
                                    value="{{ old('unit_price_idr') }}"
                                    min="0"
                                    placeholder="{{ __('batches.create.batch_unit_price_placeholder') }}"
                                >
                                <x-form.error for="unit_price_idr" />
                                <p class="ml-help">{{ __('batches.create.batch_unit_price_help') }}</p>
                            </div>

                            <div class="ml-field">
                                <label for="storage_location" class="ml-label">{{ __('batches.create.storage_location') }} <span class="ml-optional">({{ __('common.optional') }})</span></label>
                                <input
                                    type="text"
                                    id="storage_location"
                                    name="storage_location"
                                    class="ml-input @error('storage_location') is-invalid @enderror"
                                    value="{{ old('storage_location') }}"
                                    placeholder="{{ __('batches.create.storage_location_placeholder') }}"
                                >
                                <x-form.error for="storage_location" />
                                <p class="ml-help">{{ __('batches.create.storage_location_help') }}</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <aside>
                <section class="ml-card ml-sticky" aria-labelledby="forecast-title">
                    <div class="ml-card-head">
                        <h2 class="ml-card-title" id="forecast-title">{{ __('batches.create.forecast_title') }}</h2>
                        <p class="ml-card-sub">{{ __('batches.create.forecast_sub') }}</p>
                    </div>
                    <div class="ml-card-body">
                        <div class="ml-forecast-grid">
                            <div class="ml-forecast-cell">
                                <span class="ml-forecast-label">{{ __('batches.create.forecast_days_to_expiry') }}</span>
                                <span id="previewDaysExpiry" class="ml-forecast-value">- <small>{{ __('common.day') }}</small></span>
                            </div>
                            <div class="ml-forecast-cell">
                                <span class="ml-forecast-label">{{ __('batches.create.forecast_days_to_deplete') }}</span>
                                <span id="previewDaysDeplete" class="ml-forecast-value">- <small>{{ __('common.day') }}</small></span>
                            </div>
                            <div class="ml-forecast-cell is-wide">
                                <span class="ml-forecast-label">{{ __('batches.create.forecast_potential_waste') }}</span>
                                <span id="previewWaste" class="ml-forecast-value">- <small>{{ __('common.unit') }}</small></span>
                            </div>
                        </div>

                        <div class="ml-forecast-risk" aria-live="polite">
                            <span class="ml-forecast-label">{{ __('batches.create.forecast_risk') }}</span>
                            <span id="previewRisk" class="ml-risk ml-risk-low">{{ __('batches.risk.low') }}</span>
                            <p id="previewRiskNote" class="ml-forecast-hint">{{ __('batches.create.forecast_hint') }}</p>
                        </div>

                        <div class="ml-forecast-note">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M4 19V5m0 14h16M8 15l3-4 3 2 4-6" />
                            </svg>
                            <span>{{ __('batches.create.forecast_note') }}</span>
                        </div>

                        <div class="ml-action-row">
                            <a href="{{ route('batches.index') }}" class="ml-btn-quiet">{{ __('common.cancel') }}</a>
                            <button type="submit" class="ml-btn-primary">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('batches.create.submit') }}
                            </button>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </form>
</div>
@endsection
