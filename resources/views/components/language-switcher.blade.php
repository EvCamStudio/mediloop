@props(['variant' => 'public'])

@php
    $currentLocale = app()->getLocale();
    $supportedLocales = config('localization.supported');
    $selectId = 'locale-switcher-'.str_replace(' ', '-', $variant);
    $selectClasses = match ($variant) {
        'on-dark' => 'border-white/20 bg-white/10 text-white',
        'compact' => 'border-border bg-white py-1 text-[11px] text-text-muted',
        default => 'border-border bg-surface-raised text-text-muted',
    };
@endphp

<form method="POST" action="{{ route('locale.update') }}" class="inline-flex items-center gap-2">
    @csrf

    <label for="{{ $selectId }}" class="sr-only">{{ __('common.language') }}</label>

    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
        class="{{ $variant === 'on-dark' ? 'text-white/70' : 'text-text-subtle' }}" aria-hidden="true">
        <circle cx="12" cy="12" r="9" />
        <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" />
    </svg>

    <select
        id="{{ $selectId }}"
        name="locale"
        onchange="this.form.submit()"
        class="rounded-lg border px-2.5 py-1.5 text-xs font-semibold focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-primary {{ $selectClasses }}"
    >
        @foreach ($supportedLocales as $locale => $label)
            <option value="{{ $locale }}" @selected($currentLocale === $locale)>{{ $label }}</option>
        @endforeach
    </select>

    <noscript>
        <button type="submit" class="rounded-lg border border-border bg-surface-raised px-2.5 py-1.5 text-xs font-semibold text-text">
            {{ __('common.save') }}
        </button>
    </noscript>
</form>
