@props(['variant' => 'info', 'role' => null])

@php
    $variantClass = match ($variant) {
        'success' => 'ml-alert-success',
        'error' => 'ml-alert-error',
        'warning' => 'ml-alert-warning',
        default => 'ml-alert-info',
    };
@endphp

<div {{ $attributes->class(['ml-alert', $variantClass]) }} role="{{ $role ?? ($variant === 'error' ? 'alert' : 'status') }}">
    {{ $slot }}
</div>
