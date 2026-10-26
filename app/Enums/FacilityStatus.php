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
            self::Pending => __('enums.facility_status.pending'),
            self::Active => __('enums.facility_status.active'),
            self::Suspended => __('enums.facility_status.suspended'),
            self::Inactive => __('enums.facility_status.inactive'),
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
