@php
    $statusClass = match ($status) {
        'ACTIVE', 'APPROVED' => 'active',
        'PENDING' => 'pending',
        'SUSPENDED', 'REJECTED' => 'suspended',
        default => 'inactive',
    };
@endphp
<span class="ml-status ml-status-{{ $statusClass }}">{{ $label }}</span>
