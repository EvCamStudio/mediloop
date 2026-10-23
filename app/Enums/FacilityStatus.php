<?php

namespace App\Enums;

enum FacilityStatus: string
{
    case Pending = 'PENDING';
    case Active = 'ACTIVE';
    case Suspended = 'SUSPENDED';
    case Inactive = 'INACTIVE';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu verifikasi',
            self::Active => 'Aktif',
            self::Suspended => 'Ditangguhkan',
            self::Inactive => 'Nonaktif',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }

    public function isOperational(): bool
    {
        return $this === self::Active;
    }
}
