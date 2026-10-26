@php
    $isEdit = $facility->exists;
    $types = \App\Enums\FacilityType::cases();
@endphp

<x-form.summary for="facilityForm" />

<form method="POST" action="{{ $action }}" id="facilityForm" novalidate data-validate>
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="ml-two-col">
        <div>
            <section class="ml-card" aria-labelledby="facility-identity-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="facility-identity-title">{{ __('admin.facility_form.identity_title') }}</h2>
                    <p class="ml-card-sub">{{ __('admin.facility_form.identity_subtitle') }}</p>
                </div>
                <div class="ml-card-body">
                    <div class="ml-form-grid">
                        <div class="ml-field is-full">
                            <label for="name" class="ml-label">{{ __('admin.facility_form.name_label') }} <span class="ml-required">*</span></label>
                            <input id="name" name="name" type="text" class="ml-input @error('name') is-invalid @enderror" value="{{ old('name', $facility->name) }}" required data-error-required="{{ __('admin.validation.name_required') }}">
                            <x-form.error for="name" />
                        </div>

                        <div class="ml-field">
                            <label for="code" class="ml-label">{{ __('admin.facility_form.code_label') }} <span class="ml-required">*</span></label>
                            <input id="code" name="code" type="text" class="ml-input @error('code') is-invalid @enderror" value="{{ old('code', $facility->code) }}" required maxlength="40" pattern="[A-Za-z0-9_-]+" data-error-required="{{ __('admin.validation.code_required') }}" data-error-pattern="{{ __('admin.validation.code_pattern') }}">
                            <x-form.error for="code" />
                        </div>

                        <div class="ml-field">
                            <label for="type" class="ml-label">{{ __('common.type') }} <span class="ml-required">*</span></label>
                            <select id="type" name="type" class="ml-select @error('type') is-invalid @enderror" required data-error-required="{{ __('admin.validation.type_required') }}">
                                <option value="">{{ __('admin.facility_form.select_type') }}</option>
                                @foreach($types as $facilityType)
                                    @if($facilityType !== \App\Enums\FacilityType::Dinkes)
                                        <option value="{{ $facilityType->value }}" @selected(old('type', $facility->type) === $facilityType->value)>{{ $facilityType->label() }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <x-form.error for="type" />
                        </div>

                        <div class="ml-field">
                            <label for="ownership" class="ml-label">{{ __('admin.ownership') }}</label>
                            <select id="ownership" name="ownership" class="ml-select @error('ownership') is-invalid @enderror">
                                <option value="">{{ __('admin.facility_form.select_ownership') }}</option>
                                <option value="PEMERINTAH" @selected(old('ownership', $facility->ownership) === 'PEMERINTAH')>{{ __('admin.facility_form.ownership_government') }}</option>
                                <option value="SWASTA" @selected(old('ownership', $facility->ownership) === 'SWASTA')>{{ __('admin.facility_form.ownership_private') }}</option>
                                <option value="LAINNYA" @selected(old('ownership', $facility->ownership) === 'LAINNYA')>{{ __('admin.facility_form.ownership_other') }}</option>
                            </select>
                            <x-form.error for="ownership" />
                        </div>

                        <div class="ml-field is-full">
                            <label for="license_number" class="ml-label">{{ __('admin.license_number') }}</label>
                            <input id="license_number" name="license_number" type="text" class="ml-input @error('license_number') is-invalid @enderror" value="{{ old('license_number', $facility->license_number) }}">
                            <x-form.error for="license_number" />
                        </div>
                    </div>
                </div>
            </section>

            <section class="ml-card" aria-labelledby="facility-contact-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="facility-contact-title">{{ __('admin.facility_form.location_contact_title') }}</h2>
                    <p class="ml-card-sub">{{ __('admin.facility_form.location_contact_subtitle') }}</p>
                </div>
                <div class="ml-card-body">
                    <div class="ml-form-grid">
                        <div class="ml-field is-full">
                            <label for="address" class="ml-label">{{ __('common.address') }} <span class="ml-required">*</span></label>
                            <input id="address" name="address" type="text" class="ml-input @error('address') is-invalid @enderror" value="{{ old('address', $facility->address) }}" required data-error-required="{{ __('admin.validation.address_required') }}">
                            <x-form.error for="address" />
                        </div>

                        <div class="ml-field">
                            <label for="province" class="ml-label">{{ __('common.province') }} <span class="ml-required">*</span></label>
                            <input id="province" name="province" type="text" class="ml-input @error('province') is-invalid @enderror" value="{{ old('province', $facility->province) }}" required data-error-required="{{ __('admin.validation.province_required') }}">
                            <x-form.error for="province" />
                        </div>

                        <div class="ml-field">
                            <label for="city" class="ml-label">{{ __('admin.facility_form.city_regency') }} <span class="ml-required">*</span></label>
                            <input id="city" name="city" type="text" class="ml-input @error('city') is-invalid @enderror" value="{{ old('city', $facility->city) }}" required data-error-required="{{ __('admin.validation.city_required') }}">
                            <x-form.error for="city" />
                        </div>

                        <div class="ml-field">
                            <label for="district" class="ml-label">{{ __('admin.facility_form.district') }}</label>
                            <input id="district" name="district" type="text" class="ml-input @error('district') is-invalid @enderror" value="{{ old('district', $facility->district) }}">
                            <x-form.error for="district" />
                        </div>

                        <div class="ml-field">
                            <label for="contact_person" class="ml-label">{{ __('admin.contact_person') }} <span class="ml-required">*</span></label>
                            <input id="contact_person" name="contact_person" type="text" class="ml-input @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $facility->contact_person) }}" required data-error-required="{{ __('admin.validation.contact_person_required') }}">
                            <x-form.error for="contact_person" />
                        </div>

                        <div class="ml-field">
                            <label for="phone" class="ml-label">{{ __('common.phone') }} <span class="ml-required">*</span></label>
                            <input id="phone" name="phone" type="text" class="ml-input @error('phone') is-invalid @enderror" value="{{ old('phone', $facility->phone) }}" required data-error-required="{{ __('admin.validation.phone_required') }}">
                            <x-form.error for="phone" />
                        </div>

                        <div class="ml-field">
                            <label for="email" class="ml-label">{{ __('admin.facility_email') }}</label>
                            <input id="email" name="email" type="email" class="ml-input @error('email') is-invalid @enderror" value="{{ old('email', $facility->email) }}" data-error-email="{{ __('admin.validation.email_invalid') }}">
                            <x-form.error for="email" />
                        </div>

                        <div class="ml-field is-full">
                            <label class="ml-checkbox-row">
                                <input type="checkbox" name="cold_chain_ready" value="1" @checked(old('cold_chain_ready', $facility->cold_chain_ready))>
                                <span>{{ __('admin.facility_form.cold_chain_checkbox') }}</span>
                            </label>
                        </div>

                        <div class="ml-field is-full">
                            <label for="notes" class="ml-label">{{ __('admin.verification_notes') }}</label>
                            <textarea id="notes" name="notes" class="ml-textarea @error('notes') is-invalid @enderror" placeholder="{{ __('admin.facility_form.notes_placeholder') }}">{{ old('notes', $facility->notes) }}</textarea>
                            <x-form.error for="notes" />
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <aside>
            <section class="ml-card ml-sticky" aria-labelledby="facility-map-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="facility-map-title">{{ __('admin.facility_form.location_title') }}</h2>
                    <p class="ml-card-sub">{{ __('admin.facility_form.location_subtitle') }}</p>
                </div>
                <div class="ml-card-body">
                    <div id="facility-map" class="ml-map-picker" data-lat="{{ old('latitude', $facility->latitude ?? -6.1754) }}" data-lng="{{ old('longitude', $facility->longitude ?? 106.8272) }}" role="application" tabindex="0" aria-label="{{ __('admin.facility_form.map_aria') }}" aria-describedby="map-coordinates"></div>
                    <div class="ml-map-coordinates">
                        <span>{{ __('admin.facility_form.selected_coordinates') }}</span>
                        <code id="map-coordinates">{{ old('latitude', $facility->latitude ?? '-6.1754000') }}, {{ old('longitude', $facility->longitude ?? '106.8272000') }}</code>
                    </div>

                    <div class="ml-form-grid" style="margin-top: 16px;">
                        <div class="ml-field" style="margin-bottom: 0;">
                            <label for="latitude" class="ml-label">Latitude <span class="ml-required">*</span></label>
                            <input id="latitude" name="latitude" type="number" step="0.0000001" min="-90" max="90" class="ml-input @error('latitude') is-invalid @enderror" value="{{ old('latitude', $facility->latitude ?? -6.1754) }}" required data-error-required="{{ __('admin.validation.location_required') }}">
                            <x-form.error for="latitude" />
                        </div>
                        <div class="ml-field" style="margin-bottom: 0;">
                            <label for="longitude" class="ml-label">Longitude <span class="ml-required">*</span></label>
                            <input id="longitude" name="longitude" type="number" step="0.0000001" min="-180" max="180" class="ml-input @error('longitude') is-invalid @enderror" value="{{ old('longitude', $facility->longitude ?? 106.8272) }}" required data-error-required="{{ __('admin.validation.location_required') }}">
                            <x-form.error for="longitude" />
                        </div>
                    </div>
                </div>
            </section>
        </aside>
    </div>

    <div class="ml-action-row" style="margin-top: 20px;">
        <a href="{{ $cancelUrl }}" class="ml-btn-quiet">{{ __('common.cancel') }}</a>
        <button type="submit" class="ml-btn-primary">{{ $submitLabel }}</button>
    </div>
</form>

@vite('resources/js/facility-map-picker.js')
