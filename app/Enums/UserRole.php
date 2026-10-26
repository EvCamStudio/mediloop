<?php

namespace App\Enums;

enum UserRole: string
{
    case Sysadmin = 'SYSADMIN';
    case FacilityStaff = 'FACILITY_STAFF';

    public function label(): string
    {
        return match ($this) {
            self::Sysadmin => __('enums.user_role.sysadmin'),
            self::FacilityStaff => __('enums.user_role.facility_staff'),
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $role): string => $role->value, self::cases());
    }

    /**
     * @return array<int, string>
     */
    public static function facilityValues(): array
    {
        return [self::FacilityStaff->value];
    }
}
