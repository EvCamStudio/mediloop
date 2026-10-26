@extends('layouts.public', ['title' => __('invitations.accept.page_title')])

@section('content')
<div class="pub-invite-shell">
    <aside class="pub-invite-aside">
        <p class="pub-eyebrow">{{ __('invitations.accept.eyebrow') }}</p>
        <h1>{{ __('invitations.accept.heading', ['facility' => $invitation->facility->name]) }}</h1>
        <p>
            {{ __('invitations.accept.invited_as') }} <strong>{{ $roleLabel }}</strong>. {{ __('invitations.accept.invited_description') }}
        </p>

        <div class="pub-invite-facility">
            <span class="pub-invite-mark" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($invitation->facility->name, 0, 1)) }}</span>
            <span>
                <strong>{{ $invitation->facility->name }}</strong>
                <small>{{ $invitation->facility->city ?: __('invitations.accept.facility_fallback') }} · {{ $invitation->facility->type_label }}</small>
            </span>
        </div>

        <ul class="pub-invite-meta">
            <li>
                <span>{{ __('common.role') }}</span>
                <strong>{{ $roleLabel }}</strong>
            </li>
            <li>
                <span>{{ __('invitations.accept.invited_by') }}</span>
                <strong>{{ $invitation->inviter->name }}</strong>
            </li>
            <li>
                <span>{{ __('common.email') }}</span>
                <strong>{{ $invitation->email }}</strong>
            </li>
            <li>
                <span>{{ __('invitations.accept.expires_at') }}</span>
                <strong>{{ $invitation->expires_at->format('d M Y, H:i') }}</strong>
            </li>
        </ul>

        <div class="pub-invite-trust">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z" />
                <path d="M9 12l2 2 4-4" />
            </svg>
            <span>{{ __('invitations.accept.single_use') }}</span>
        </div>
    </aside>

    <section class="pub-invite-form">
        <h2>{{ __('invitations.accept.activate_title') }}</h2>
        <p>{{ __('invitations.accept.activate_description') }}</p>

        <x-form.summary variant="pub" for="inviteAcceptForm" />

        <form method="POST" action="{{ route('invitations.accept', ['token' => $token]) }}" id="inviteAcceptForm" novalidate data-validate>
            @csrf

            <div class="pub-field">
                <label for="name" class="pub-label">{{ __('invitations.accept.full_name') }} <span class="pub-required">*</span></label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    class="pub-input @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    data-error-required="{{ __('invitations.accept.name_required') }}"
                >
                <x-form.error variant="pub" for="name" />
            </div>

            <div class="pub-field">
                <label for="password" class="pub-label">{{ __('common.password') }} <span class="pub-required">*</span></label>
                <div class="password-control">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="pub-input password-input @error('password') is-invalid @enderror"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        data-error-required="{{ __('invitations.accept.password_required') }}"
                        data-error-minlength="{{ __('invitations.accept.password_minlength') }}"
                    >
                    <button type="button" class="password-toggle" data-password-toggle
                        data-password-target="password" data-label-show="{{ __('js.password.show') }}"
                        data-label-hide="{{ __('js.password.hide') }}" aria-controls="password"
                        aria-label="{{ __('js.password.show') }}" aria-pressed="false">
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
                <p class="pub-help">{{ __('invitations.accept.password_help') }}</p>
                <x-form.error variant="pub" for="password" />
            </div>

            <div class="pub-field">
                <label for="password_confirmation" class="pub-label">{{ __('invitations.accept.password_confirmation') }} <span class="pub-required">*</span></label>
                <div class="password-control">
                    <input id="password_confirmation" name="password_confirmation" type="password"
                        class="pub-input password-input" required minlength="8" autocomplete="new-password"
                        data-error-required="{{ __('invitations.accept.password_confirmation_required') }}"
                        data-error-minlength="{{ __('invitations.accept.password_minlength') }}">
                    <button type="button" class="password-toggle" data-password-toggle
                        data-password-target="password_confirmation"
                        data-label-show="{{ __('js.password.show_confirmation') }}"
                        data-label-hide="{{ __('js.password.hide_confirmation') }}"
                        aria-controls="password_confirmation" aria-label="{{ __('js.password.show_confirmation') }}"
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
                <x-form.error variant="pub" for="password_confirmation" />
            </div>

            <div class="pub-actions">
                <button type="submit" class="pub-btn pub-btn-primary pub-btn-block">{{ __('invitations.accept.activate_button') }}</button>
            </div>
        </form>

        <p class="pub-invite-footnote">
            {{ __('invitations.accept.footnote', ['email' => $invitation->email]) }}
        </p>
    </section>
</div>
@endsection
