@extends('layouts.mediloop', ['title' => __('facility.settings.page_title')])

@section('content')
<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="{{ __('facility.breadcrumb_aria') }}">
        <a href="{{ route('dashboard') }}">{{ __('navigation.dashboard') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ __('facility.settings.breadcrumb') }}</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">{{ __('facility.settings.heading', ['facility' => $facility->name]) }}</h1>
                @include('admin.partials.status', ['status' => $facility->status, 'label' => $facility->status_label])
            </div>
            <p class="ml-page-sub">{{ __('facility.settings.subtitle') }}</p>
        </div>
        <div class="ml-page-actions">
            <a href="{{ route('facility.team.index') }}" class="ml-btn-quiet">{{ __('facility.settings.manage_team') }}</a>
        </div>
    </header>

    @if(session('status'))
        <x-alert variant="success">{{ session('status') }}</x-alert>
    @endif

    <x-form.summary for="facilitySettingsForm" />

    <div class="ml-settings-layout">
        <aside>
            <section class="ml-card ml-settings-aside" aria-labelledby="identity-title">
                <div class="ml-card-body">
                    <span class="ml-settings-mark" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($facility->name, 0, 1)) }}</span>
                    <h2 class="ml-card-title" id="identity-title">{{ __('facility.settings.identity_title') }}</h2>
                    <p class="ml-card-sub">{{ __('facility.settings.identity_sub') }}</p>

                    <dl class="ml-settings-list">
                        <div>
                            <dt>{{ __('facility.settings.facility_name') }}</dt>
                            <dd>{{ $facility->name }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('common.code') }}</dt>
                            <dd class="is-mono">{{ $facility->code }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('facility.settings.type') }}</dt>
                            <dd>{{ $facility->type_label }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('facility.labels.license_number') }}</dt>
                            <dd>{{ $facility->license_number ?: __('common.not_recorded') }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('facility.settings.coordinates') }}</dt>
                            <dd class="is-mono">{{ number_format((float) $facility->latitude, 7, '.', '') }}, {{ number_format((float) $facility->longitude, 7, '.', '') }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('facility.settings.verified_at') }}</dt>
                            <dd>{{ $facility->verified_at?->format('d M Y') ?? __('facility.settings.not_verified') }}</dd>
                        </div>
                    </dl>

                    <div class="ml-settings-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 8v4m0 4h.01" />
                        </svg>
                        {{ __('facility.settings.identity_note') }}
                    </div>
                </div>
            </section>
        </aside>

        <form method="POST" action="{{ route('facility.settings.update') }}" id="facilitySettingsForm" novalidate data-validate>
            @csrf
            @method('PUT')

            <section class="ml-card" aria-labelledby="contact-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="contact-title">{{ __('facility.settings.contact_title') }}</h2>
                        <p class="ml-card-sub">{{ __('facility.settings.contact_sub') }}</p>
                    </div>
                    <span class="ml-card-index" aria-hidden="true">{{ __('facility.settings.edit_badge') }}</span>
                </div>
                <div class="ml-card-body">
                    <div class="ml-form-grid">
                        <div class="ml-field is-full">
                            <label for="address" class="ml-label">{{ __('common.address') }} <span class="ml-required">*</span></label>
                            <input id="address" name="address" type="text" class="ml-input @error('address') is-invalid @enderror" value="{{ old('address', $facility->address) }}" required autocomplete="street-address" data-error-required="{{ __('facility.settings.address_required') }}">
                            <x-form.error for="address" />
                        </div>

                        <div class="ml-field">
                            <label for="province" class="ml-label">{{ __('common.province') }} <span class="ml-required">*</span></label>
                            <input id="province" name="province" type="text" class="ml-input @error('province') is-invalid @enderror" value="{{ old('province', $facility->province) }}" required autocomplete="address-level1" data-error-required="{{ __('facility.settings.province_required') }}">
                            <x-form.error for="province" />
                        </div>

                        <div class="ml-field">
                            <label for="city" class="ml-label">{{ __('facility.labels.city') }} <span class="ml-required">*</span></label>
                            <input id="city" name="city" type="text" class="ml-input @error('city') is-invalid @enderror" value="{{ old('city', $facility->city) }}" required autocomplete="address-level2" data-error-required="{{ __('facility.settings.city_required') }}">
                            <x-form.error for="city" />
                        </div>

                        <div class="ml-field">
                            <label for="district" class="ml-label">{{ __('facility.labels.district') }}</label>
                            <input id="district" name="district" type="text" class="ml-input @error('district') is-invalid @enderror" value="{{ old('district', $facility->district) }}" autocomplete="address-level3">
                            <x-form.error for="district" />
                        </div>

                        <div class="ml-field">
                            <label for="contact_person" class="ml-label">{{ __('facility.labels.contact_person') }} <span class="ml-required">*</span></label>
                            <input id="contact_person" name="contact_person" type="text" class="ml-input @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $facility->contact_person) }}" required autocomplete="name" data-error-required="{{ __('facility.settings.contact_person_required') }}">
                            <x-form.error for="contact_person" />
                        </div>

                        <div class="ml-field">
                            <label for="phone" class="ml-label">{{ __('facility.labels.phone') }} <span class="ml-required">*</span></label>
                            <input id="phone" name="phone" type="tel" class="ml-input @error('phone') is-invalid @enderror" value="{{ old('phone', $facility->phone) }}" required autocomplete="tel" inputmode="tel" data-error-required="{{ __('facility.settings.phone_required') }}">
                            <x-form.error for="phone" />
                        </div>

                        <div class="ml-field is-full">
                            <label for="email" class="ml-label">{{ __('facility.settings.facility_email') }}</label>
                            <input id="email" name="email" type="email" class="ml-input @error('email') is-invalid @enderror" value="{{ old('email', $facility->email) }}" autocomplete="email" inputmode="email" data-error-email="{{ __('facility.settings.email_invalid') }}">
                            <x-form.error for="email" />
                        </div>

                        <div class="ml-field is-full">
                            <label class="ml-checkbox-row">
                                <input type="checkbox" name="cold_chain_ready" value="1" @checked(old('cold_chain_ready', $facility->cold_chain_ready))>
                                <span>{{ __('facility.settings.cold_chain') }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="ml-action-row">
                        <a href="{{ route('dashboard') }}" class="ml-btn-quiet">{{ __('common.cancel') }}</a>
                        <button type="submit" class="ml-btn-primary">{{ __('common.save_changes') }}</button>
                    </div>
                </div>
            </section>
        </form>
    </div>
</div>
@endsection
