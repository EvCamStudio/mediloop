@props(['title' => 'Periksa kembali isian berikut:', 'for' => null, 'variant' => 'ml'])

@php
    $hasErrors = $errors->any();
    $isAuth = $variant === 'auth';
    $variantClass = match ($variant) {
        'pub' => 'pub-alert pub-alert-error',
        'auth' => 'rounded-lg border border-feedback-danger-surface-border bg-feedback-danger-surface px-3.5 py-2.5 text-sm text-feedback-danger-ink',
        default => 'ml-alert ml-alert-error',
    };
@endphp

<div
    {{ $attributes->class($variantClass) }}
    role="alert"
    data-form-summary
    @if (filled($for)) data-form-summary-for="{{ $for }}" @endif
    @if (! $hasErrors) hidden @endif
>
    <strong @class(['font-semibold' => $isAuth])>{{ $title }}</strong>
    <ul data-form-summary-list @class(['mt-1.5 list-disc space-y-1 pl-5' => $isAuth])>
        @foreach ($errors->keys() as $field)
            <li><a href="#{{ $field }}">{{ $errors->first($field) }}</a></li>
        @endforeach
    </ul>
</div>
