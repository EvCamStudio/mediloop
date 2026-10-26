@extends('layouts.public', ['title' => __('facility.request.page_title')])

@section('content')
@if($submitted)
    <div class="pub-success-shell">
        <div class="pub-success-card">
            <div class="pub-success-lead">
                <span class="pub-success-icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5" />
                    </svg>
                </span>
                <p class="pub-eyebrow">{{ __('facility.request.submitted.eyebrow') }}</p>
                <h2>{{ __('facility.request.submitted.title') }}</h2>
                <p>
                    {{ __('facility.request.submitted.body') }}
                </p>
            </div>

            <div class="pub-success-next">
                <h3>{{ __('facility.request.submitted.next_title') }}</h3>
                <ol class="pub-success-steps">
                    <li class="pub-success-step">
                        <span aria-hidden="true">01</span>
                        <span>
                            <strong>{{ __('facility.request.submitted.step1_title') }}</strong>
                            <small>{{ __('facility.request.submitted.step1_body') }}</small>
                        </span>
                    </li>
                    <li class="pub-success-step">
                        <span aria-hidden="true">02</span>
                        <span>
                            <strong>{{ __('facility.request.submitted.step2_title') }}</strong>
                            <small>{{ __('facility.request.submitted.step2_body') }}</small>
                        </span>
                    </li>
                    <li class="pub-success-step">
                        <span aria-hidden="true">03</span>
                        <span>
                            <strong>{{ __('facility.request.submitted.step3_title') }}</strong>
                            <small>{{ __('facility.request.submitted.step3_body') }}</small>
                        </span>
                    </li>
                </ol>

                <div class="pub-success-actions">
                    <a href="{{ route('home') }}" class="pub-btn">{{ __('facility.request.submitted.back_home') }}</a>
                    <a href="{{ route('facility-requests.create') }}" class="pub-btn pub-btn-primary">{{ __('facility.request.submitted.another') }}</a>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="pub-onboarding">
        <aside class="pub-dossier" aria-labelledby="dossier-title">
            <p class="pub-eyebrow">{{ __('facility.request.onboarding_eyebrow') }}</p>
            <h2 id="dossier-title">{{ __('facility.request.dossier_title') }}</h2>
            <p>{{ __('facility.request.dossier_body') }}</p>

            <ol class="pub-steps" aria-label="{{ __('facility.request.steps_aria') }}">
                <li class="pub-step is-current">
                    <span class="pub-step-number" aria-hidden="true">01</span>
                    <span>
                        <span class="pub-step-title">{{ __('facility.request.steps.identity_title') }}</span>
                        <span class="pub-step-sub">{{ __('facility.request.steps.identity_sub') }}</span>
                    </span>
                </li>
                <li class="pub-step">
                    <span class="pub-step-number" aria-hidden="true">02</span>
                    <span>
                        <span class="pub-step-title">{{ __('facility.request.steps.location_title') }}</span>
                        <span class="pub-step-sub">{{ __('facility.request.steps.location_sub') }}</span>
                    </span>
                </li>
                <li class="pub-step">
                    <span class="pub-step-number" aria-hidden="true">03</span>
                    <span>
                        <span class="pub-step-title">{{ __('facility.request.steps.contact_title') }}</span>
                        <span class="pub-step-sub">{{ __('facility.request.steps.contact_sub') }}</span>
                    </span>
                </li>
                <li class="pub-step">
                    <span class="pub-step-number" aria-hidden="true">04</span>
                    <span>
                        <span class="pub-step-title">{{ __('facility.request.steps.review_title') }}</span>
                        <span class="pub-step-sub">{{ __('facility.request.steps.review_sub') }}</span>
                    </span>
                </li>
            </ol>

            <div class="pub-dossier-divider"></div>

            <ul class="pub-trust-list">
                <li class="pub-trust-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z" />
                        <path d="M9 12l2 2 4-4" />
                    </svg>
                    <span>{{ __('facility.request.trust.no_patient_data') }}</span>
                </li>
                <li class="pub-trust-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 7v5l3 2" />
                    </svg>
                    <span>{{ __('facility.request.trust.manual_verification') }}</span>
                </li>
                <li class="pub-trust-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13A4 4 0 0116 11" />
                    </svg>
                    <span>{{ __('facility.request.trust.invite_only') }}</span>
                </li>
            </ul>
        </aside>

        <div class="pub-form-panel">
            <header class="pub-form-intro">
                <p class="pub-eyebrow">{{ __('facility.request.form_eyebrow') }}</p>
                <h1 class="pub-title">{{ __('facility.request.heading') }}</h1>
                <p class="pub-sub">
                    {{ __('facility.request.subtitle') }}
                </p>
                <div class="pub-form-meta" aria-label="{{ __('facility.request.meta_aria') }}">
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                        </svg>
                        {{ trans_choice('facility.request.meta_sections', 3, ['count' => 3]) }}
                    </span>
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z" />
                        </svg>
                        {{ __('facility.request.meta_no_patient_data') }}
                    </span>
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 2" />
                        </svg>
                        {{ __('facility.request.meta_manual_verification') }}
                    </span>
                </div>
            </header>

            <x-form.summary variant="pub" for="facilityRequestForm" style="margin: 22px 32px 0;" />

            <form method="POST" action="{{ route('facility-requests.store') }}" id="facilityRequestForm" novalidate data-validate>
                @csrf

                <section class="pub-section" id="identitas" aria-labelledby="identity-title">
                    <div class="pub-section-head">
                        <span class="pub-section-index" aria-hidden="true">01</span>
                        <div>
                            <h2 class="pub-section-title" id="identity-title">{{ __('facility.request.identity_title') }}</h2>
                            <p class="pub-section-sub">{{ __('facility.request.identity_sub') }}</p>
                        </div>
                    </div>

                    <div class="pub-grid">
                        <div class="pub-field is-full">
                            <label for="name" class="pub-label">{{ __('facility.request.name') }} <span class="pub-required">*</span></label>
                            <input
                                id="name"
                                data-error-required="{{ __('facility.request.name_required') }}"
                                name="name"
                                type="text"
                                class="pub-input @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                required
                                autocomplete="organization"
                                aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                                @if($errors->has('name')) aria-describedby="name-error" @endif
                            >
                            <x-form.error variant="pub" for="name" />
                        </div>

                        <div class="pub-field">
                            <label for="code" class="pub-label">{{ __('facility.request.code') }} <span class="pub-required">*</span></label>
                            <input
                                id="code"
                                data-error-required="{{ __('facility.request.code_required') }}" data-error-pattern="{{ __('facility.request.code_pattern') }}"
                                name="code"
                                maxlength="40"
                                pattern="[A-Za-z0-9_-]+"
                                type="text"
                                class="pub-input @error('code') is-invalid @enderror"
                                value="{{ old('code') }}"
                                required
                                placeholder="{{ __('facility.request.code_placeholder') }}"
                                autocomplete="off"
                                aria-invalid="{{ $errors->has('code') ? 'true' : 'false' }}"
                                aria-describedby="code-help{{ $errors->has('code') ? ' code-error' : '' }}"
                            >
                            <p class="pub-help" id="code-help">{{ __('facility.request.code_help') }}</p>
                            <x-form.error variant="pub" for="code" />
                        </div>

                        <div class="pub-field">
                            <label for="type" class="pub-label">{{ __('facility.request.type') }} <span class="pub-required">*</span></label>
                            <select
                                id="type"
                                data-error-required="{{ __('facility.request.type_required') }}"
                                name="type"
                                class="pub-select @error('type') is-invalid @enderror"
                                required
                                aria-invalid="{{ $errors->has('type') ? 'true' : 'false' }}"
                                @if($errors->has('type')) aria-describedby="type-error" @endif
                            >
                                <option value="">{{ __('facility.request.type_placeholder') }}</option>
                                @foreach(\App\Enums\FacilityType::cases() as $facilityType)
                                    @if($facilityType !== \App\Enums\FacilityType::Dinkes)
                                        <option value="{{ $facilityType->value }}" @selected(old('type') === $facilityType->value)>{{ $facilityType->label() }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <x-form.error variant="pub" for="type" />
                        </div>

                        <div class="pub-field">
                            <label for="ownership" class="pub-label">{{ __('facility.request.ownership') }}</label>
                            <select
                                id="ownership"
                                name="ownership"
                                class="pub-select @error('ownership') is-invalid @enderror"
                                aria-invalid="{{ $errors->has('ownership') ? 'true' : 'false' }}"
                                @if($errors->has('ownership')) aria-describedby="ownership-error" @endif
                            >
                                <option value="">{{ __('facility.request.ownership_placeholder') }}</option>
                                <option value="PEMERINTAH" @selected(old('ownership') === 'PEMERINTAH')>{{ __('facility.request.ownership_government') }}</option>
                                <option value="SWASTA" @selected(old('ownership') === 'SWASTA')>{{ __('facility.request.ownership_private') }}</option>
                                <option value="LAINNYA" @selected(old('ownership') === 'LAINNYA')>{{ __('facility.request.ownership_other') }}</option>
                            </select>
                            <x-form.error variant="pub" for="ownership" />
                        </div>

                        <div class="pub-field">
                            <label for="license_number" class="pub-label">{{ __('facility.request.license_number') }}</label>
                            <input
                                id="license_number"
                                name="license_number"
                                type="text"
                                class="pub-input @error('license_number') is-invalid @enderror"
                                value="{{ old('license_number') }}"
                                placeholder="{{ __('facility.request.license_number_placeholder') }}"
                                aria-invalid="{{ $errors->has('license_number') ? 'true' : 'false' }}"
                                @if($errors->has('license_number')) aria-describedby="license_number-error" @endif
                            >
                            <x-form.error variant="pub" for="license_number" />
                        </div>
                    </div>
                </section>

                <section class="pub-section" id="lokasi" aria-labelledby="location-title">
                    <div class="pub-section-head">
                        <span class="pub-section-index" aria-hidden="true">02</span>
                        <div>
                            <h2 class="pub-section-title" id="location-title">{{ __('facility.request.location_title') }}</h2>
                            <p class="pub-section-sub">{{ __('facility.request.location_sub') }}</p>
                        </div>
                    </div>

                    <div class="pub-grid">
                        <div class="pub-field is-full">
                            <label for="address" class="pub-label">{{ __('facility.request.address') }} <span class="pub-required">*</span></label>
                            <input
                                id="address"
                                data-error-required="{{ __('facility.request.address_required') }}"
                                name="address"
                                type="text"
                                class="pub-input @error('address') is-invalid @enderror"
                                value="{{ old('address') }}"
                                required
                                autocomplete="street-address"
                                aria-invalid="{{ $errors->has('address') ? 'true' : 'false' }}"
                                @if($errors->has('address')) aria-describedby="address-error" @endif
                            >
                            <x-form.error variant="pub" for="address" />
                        </div>

                        <div class="pub-field">
                            <label for="province" class="pub-label">{{ __('common.province') }} <span class="pub-required">*</span></label>
                            <input
                                id="province"
                                data-error-required="{{ __('facility.request.province_required') }}"
                                name="province"
                                type="text"
                                class="pub-input @error('province') is-invalid @enderror"
                                value="{{ old('province') }}"
                                required
                                autocomplete="address-level1"
                                aria-invalid="{{ $errors->has('province') ? 'true' : 'false' }}"
                                @if($errors->has('province')) aria-describedby="province-error" @endif
                            >
                            <x-form.error variant="pub" for="province" />
                        </div>

                        <div class="pub-field">
                            <label for="city" class="pub-label">{{ __('facility.labels.city') }} <span class="pub-required">*</span></label>
                            <input
                                id="city"
                                data-error-required="{{ __('facility.request.city_required') }}"
                                name="city"
                                type="text"
                                class="pub-input @error('city') is-invalid @enderror"
                                value="{{ old('city') }}"
                                required
                                autocomplete="address-level2"
                                aria-invalid="{{ $errors->has('city') ? 'true' : 'false' }}"
                                @if($errors->has('city')) aria-describedby="city-error" @endif
                            >
                            <x-form.error variant="pub" for="city" />
                        </div>

                        <div class="pub-field">
                            <label for="district" class="pub-label">{{ __('facility.labels.district') }}</label>
                            <input
                                id="district"
                                name="district"
                                type="text"
                                class="pub-input @error('district') is-invalid @enderror"
                                value="{{ old('district') }}"
                                autocomplete="address-level3"
                                aria-invalid="{{ $errors->has('district') ? 'true' : 'false' }}"
                                @if($errors->has('district')) aria-describedby="district-error" @endif
                            >
                            <x-form.error variant="pub" for="district" />
                        </div>

                        <div class="pub-field is-full">
                            <span class="pub-label">{{ __('facility.request.map_label') }} <span class="pub-required">*</span></span>
                            <div
                                id="facility-map"
                                class="pub-map"
                                data-lat="{{ old('latitude', -6.1754) }}"
                                data-lng="{{ old('longitude', 106.8272) }}"
                                role="application"
                                tabindex="0"
                                aria-label="{{ __('facility.request.map_aria') }}"
                                aria-describedby="map-coordinates"
                            ></div>
                            <div class="pub-map-meta">
                                <span>{{ __('facility.request.map_hint') }}</span>
                                <code id="map-coordinates">{{ old('latitude', '-6.1754000') }}, {{ old('longitude', '106.8272000') }}</code>
                            </div>
                        </div>

                        <div class="pub-field">
                            <label for="latitude" class="pub-label">{{ __('facility.request.latitude') }} <span class="pub-required">*</span></label>
                            <input
                                id="latitude"
                                data-error-required="{{ __('facility.request.coordinates_required') }}"
                                name="latitude"
                                type="number"
                                step="0.0000001"
                                min="-90"
                                max="90"
                                class="pub-input @error('latitude') is-invalid @enderror"
                                value="{{ old('latitude', -6.1754) }}"
                                required
                                aria-invalid="{{ $errors->has('latitude') ? 'true' : 'false' }}"
                                @if($errors->has('latitude')) aria-describedby="latitude-error" @endif
                            >
                            <x-form.error variant="pub" for="latitude" />
                        </div>

                        <div class="pub-field">
                            <label for="longitude" class="pub-label">{{ __('facility.request.longitude') }} <span class="pub-required">*</span></label>
                            <input
                                id="longitude"
                                data-error-required="{{ __('facility.request.coordinates_required') }}"
                                name="longitude"
                                type="number"
                                step="0.0000001"
                                min="-180"
                                max="180"
                                class="pub-input @error('longitude') is-invalid @enderror"
                                value="{{ old('longitude', 106.8272) }}"
                                required
                                aria-invalid="{{ $errors->has('longitude') ? 'true' : 'false' }}"
                                @if($errors->has('longitude')) aria-describedby="longitude-error" @endif
                            >
                            <x-form.error variant="pub" for="longitude" />
                        </div>
                    </div>
                </section>

                <section class="pub-section" id="kontak" aria-labelledby="contact-title">
                    <div class="pub-section-head">
                        <span class="pub-section-index" aria-hidden="true">03</span>
                        <div>
                            <h2 class="pub-section-title" id="contact-title">{{ __('facility.request.contact_title') }}</h2>
                            <p class="pub-section-sub">{{ __('facility.request.contact_sub') }}</p>
                        </div>
                    </div>

                    <div class="pub-grid">
                        <div class="pub-field">
                            <label for="contact_person" class="pub-label">{{ __('facility.request.contact_person') }} <span class="pub-required">*</span></label>
                            <input
                                id="contact_person"
                                data-error-required="{{ __('facility.request.contact_person_required') }}"
                                name="contact_person"
                                type="text"
                                class="pub-input @error('contact_person') is-invalid @enderror"
                                value="{{ old('contact_person') }}"
                                required
                                autocomplete="name"
                                aria-invalid="{{ $errors->has('contact_person') ? 'true' : 'false' }}"
                                @if($errors->has('contact_person')) aria-describedby="contact_person-error" @endif
                            >
                            <x-form.error variant="pub" for="contact_person" />
                        </div>

                        <div class="pub-field">
                            <label for="phone" class="pub-label">{{ __('facility.labels.phone') }} <span class="pub-required">*</span></label>
                            <input
                                id="phone"
                                data-error-required="{{ __('facility.request.phone_required') }}"
                                name="phone"
                                type="tel"
                                class="pub-input @error('phone') is-invalid @enderror"
                                value="{{ old('phone') }}"
                                required
                                autocomplete="tel"
                                inputmode="tel"
                                aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}"
                                @if($errors->has('phone')) aria-describedby="phone-error" @endif
                            >
                            <x-form.error variant="pub" for="phone" />
                        </div>

                        <div class="pub-field is-full">
                            <label for="email" class="pub-label">{{ __('facility.request.email') }} <span class="pub-required">*</span></label>
                            <input
                                id="email"
                                data-error-required="{{ __('facility.request.email_required') }}" data-error-email="{{ __('facility.request.email_invalid') }}"
                                name="email"
                                type="email"
                                class="pub-input @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                inputmode="email"
                                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                @if($errors->has('email')) aria-describedby="email-error" @endif
                            >
                            <x-form.error variant="pub" for="email" />
                        </div>

                        <div class="pub-field is-full">
                            <label class="pub-checkbox">
                                <input type="checkbox" name="cold_chain_ready" value="1" @checked(old('cold_chain_ready'))>
                                <span>{{ __('facility.request.cold_chain') }}</span>
                            </label>
                        </div>

                        <div class="pub-field is-full">
                            <label for="notes" class="pub-label">{{ __('facility.request.notes') }}</label>
                            <textarea
                                id="notes"
                                name="notes"
                                class="pub-textarea @error('notes') is-invalid @enderror"
                                placeholder="{{ __('facility.request.notes_placeholder') }}"
                                aria-invalid="{{ $errors->has('notes') ? 'true' : 'false' }}"
                                @if($errors->has('notes')) aria-describedby="notes-error" @endif
                            >{{ old('notes') }}</textarea>
                            <x-form.error variant="pub" for="notes" />
                        </div>
                    </div>
                </section>

                <div class="pub-submitbar">
                    <p class="pub-submit-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 8v4m0 4h.01" />
                        </svg>
                        <span>{{ __('facility.request.submit_note') }}</span>
                    </p>
                    <div class="pub-submit-actions">
                        <a href="{{ route('home') }}" class="pub-btn">{{ __('common.cancel') }}</a>
                        <button type="submit" class="pub-btn pub-btn-primary">{{ __('facility.request.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection

@section('scripts')
    @vite('resources/js/facility-map-picker.js')
@endsection
