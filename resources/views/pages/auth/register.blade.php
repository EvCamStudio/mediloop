<x-layouts::auth :title="__('auth_ui.register.title')">
    @php
        $inputClass = 'w-full rounded-lg border border-[#DBE5F5] bg-white px-3.5 py-2.5 text-sm text-[#0B1F4B] shadow-xs outline-none transition placeholder:text-[#8A99B3] focus:border-[#1D4ED8] focus:ring-2 focus:ring-[#1D4ED8]/20';
        $facilityOptions = $facilities ?? collect();
    @endphp

    <div class="flex flex-col gap-6">
        <header>
            <h1 class="text-2xl font-extrabold tracking-tight text-[#0B1F4B]">{{ __('auth_ui.register.title') }}</h1>
            <p class="mt-1.5 text-sm text-[#52627F]">
                {{ __('auth_ui.register.subtitle') }}
            </p>
        </header>

        @if ($facilityOptions->isEmpty())
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-3.5 py-3 text-sm text-amber-900" role="alert">
                {{ __('auth_ui.register.no_facilities') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" id="registerForm" class="flex flex-col gap-5" novalidate data-validate>
            @csrf

            <x-form.summary variant="auth" :title="__('auth_ui.summary.title')" />

            <div>
                <label for="name" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                    {{ __('auth_ui.register.full_name') }}
                </label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    autocomplete="name" placeholder="{{ __('auth_ui.register.full_name_placeholder') }}" class="{{ $inputClass }}"
                    data-error-required="{{ __('auth_ui.register.full_name_required') }}"
                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                <x-form.error variant="auth" for="name" />
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                    {{ __('auth_ui.register.email') }}
                </label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                    placeholder="{{ __('auth_ui.register.email_placeholder') }}" class="{{ $inputClass }}"
                    data-error-required="{{ __('auth_ui.register.email_required') }}" data-error-email="{{ __('auth_ui.register.email_invalid') }}"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                <x-form.error variant="auth" for="email" />
            </div>

            <div>
                <label for="facility_id" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                    {{ __('auth_ui.register.facility') }}
                </label>
                <select id="facility_id" name="facility_id" required class="{{ $inputClass }}"
                    data-error-required="{{ __('auth_ui.register.select_facility_error') }}"
                    @error('facility_id') aria-invalid="true" aria-describedby="facility_id-error" @enderror>
                    <option value="" disabled @selected(old('facility_id') === null)>{{ __('auth_ui.register.select_facility') }}</option>
                    @foreach ($facilityOptions as $facility)
                        <option value="{{ $facility->id }}" @selected((string) old('facility_id') === (string) $facility->id)>
                            {{ $facility->name }}{{ filled($facility->city) ? ' — '.$facility->city : '' }}
                        </option>
                    @endforeach
                </select>
                <x-form.error variant="auth" for="facility_id" />
            </div>

            <div>
                <label for="role" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                    {{ __('auth_ui.register.role') }}
                </label>
                <select id="role" name="role" required class="{{ $inputClass }}"
                    data-error-required="{{ __('auth_ui.register.select_role_error') }}"
                    @error('role') aria-invalid="true" aria-describedby="role-error" @enderror>
                    <option value="" disabled @selected(old('role') === null)>{{ __('auth_ui.register.select_role') }}</option>
                    @foreach ($roles ?? [] as $value => $label)
                        <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-form.error variant="auth" for="role" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="job_title" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                        {{ __('auth_ui.register.job_title') }} <span class="font-medium normal-case text-[#8A99B3]">({{ __('common.optional') }})</span>
                    </label>
                    <input id="job_title" name="job_title" type="text" value="{{ old('job_title') }}"
                        autocomplete="organization-title" placeholder="{{ __('auth_ui.register.job_title_placeholder') }}" class="{{ $inputClass }}"
                        @error('job_title') aria-invalid="true" aria-describedby="job_title-error" @enderror>
                    <x-form.error variant="auth" for="job_title" />
                </div>

                <div>
                    <label for="license_sip" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                        {{ __('auth_ui.register.sip') }} <span class="font-medium normal-case text-[#8A99B3]">({{ __('common.optional') }})</span>
                    </label>
                    <input id="license_sip" name="license_sip" type="text" value="{{ old('license_sip') }}"
                        placeholder="{{ __('auth_ui.register.sip_placeholder') }}" class="{{ $inputClass }}"
                        @error('license_sip') aria-invalid="true" aria-describedby="license_sip-error" @enderror>
                    <x-form.error variant="auth" for="license_sip" />
                </div>
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                    {{ __('auth_ui.register.password') }}
                </label>
                <div class="password-control">
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                        placeholder="{{ __('auth_ui.register.password_placeholder') }}" class="{{ $inputClass }} password-input"
                        minlength="8"
                        data-error-required="{{ __('auth_ui.register.password_required') }}" data-error-minlength="{{ __('auth_ui.register.password_min') }}"
                        @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                    <button type="button" class="password-toggle" data-password-toggle
                        data-password-target="password" aria-controls="password" aria-label="{{ __('auth_ui.login.show_password') }}"
                        aria-pressed="false">
                        <svg class="password-toggle-show" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" aria-hidden="true">
                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z" />
                            <circle cx="12" cy="12" r="2.8" />
                        </svg>
                        <svg class="password-toggle-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" aria-hidden="true">
                            <path d="M3 3l18 18" />
                            <path d="M10.6 6.2A10.8 10.8 0 0112 6c6 0 9.5 6 9.5 6a16.5 16.5 0 01-3 3.6" />
                            <path d="M6.2 6.8C3.8 8.5 2.5 12 2.5 12s3.5 6 9.5 6c1.4 0 2.7-.3 3.8-.8" />
                        </svg>
                    </button>
                </div>
                <x-form.error variant="auth" for="password" />
            </div>

            <div>
                <label for="password_confirmation"
                    class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                    {{ __('auth_ui.register.confirm_password') }}
                </label>
                <div class="password-control">
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        autocomplete="new-password" placeholder="{{ __('auth_ui.register.confirm_placeholder') }}"
                        class="{{ $inputClass }} password-input"
                        minlength="8"
                        data-error-required="{{ __('auth_ui.register.confirm_required') }}" data-error-minlength="{{ __('auth_ui.register.password_min') }}">
                    <button type="button" class="password-toggle" data-password-toggle
                        data-password-target="password_confirmation"
                        data-label-show="{{ __('auth_ui.register.confirm_show') }}"
                        data-label-hide="{{ __('auth_ui.register.confirm_hide') }}"
                        aria-controls="password_confirmation" aria-label="{{ __('auth_ui.register.confirm_show') }}"
                        aria-pressed="false">
                        <svg class="password-toggle-show" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" aria-hidden="true">
                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z" />
                            <circle cx="12" cy="12" r="2.8" />
                        </svg>
                        <svg class="password-toggle-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" aria-hidden="true">
                            <path d="M3 3l18 18" />
                            <path d="M10.6 6.2A10.8 10.8 0 0112 6c6 0 9.5 6 9.5 6a16.5 16.5 0 01-3 3.6" />
                            <path d="M6.2 6.8C3.8 8.5 2.5 12 2.5 12s3.5 6 9.5 6c1.4 0 2.7-.3 3.8-.8" />
                        </svg>
                    </button>
                </div>
                <x-form.error variant="auth" for="password_confirmation" />
            </div>

            <button type="submit"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#1D4ED8] px-4 py-2.5 text-sm font-bold text-white shadow-xs transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1D4ED8] focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                data-test="register-user-button" @disabled($facilityOptions->isEmpty())>
                {{ __('auth_ui.register.submit') }}
            </button>
        </form>

        <p class="text-center text-sm text-[#52627F]">
            {{ __('auth_ui.register.have_account') }}
            <a href="{{ route('login') }}" class="font-semibold text-[#1D4ED8] hover:text-[#1E40AF]">{{ __('auth_ui.register.sign_in') }}</a>
        </p>
    </div>
</x-layouts::auth>
