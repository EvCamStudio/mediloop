@props(['for', 'variant' => 'ml'])

@php
    $message = $errors->first($for);
    $variantClass = match ($variant) {
        'pub' => 'pub-error',
        'auth' => 'mt-1.5 text-xs font-medium text-feedback-danger',
        default => 'ml-error-text',
    };
@endphp

<p
    id="{{ $for }}-error"
    data-field-error="{{ $for }}"
    {{ $attributes->class($variantClass) }}
    @if (blank($message)) hidden @endif
>{{ $message }}</p>
