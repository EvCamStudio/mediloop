<?php

namespace App\Enums;

enum UserRole: string
{
    case Sysadmin = 'SYSADMIN';
    case DinkesAuditor = 'DINKES_AUDITOR';
    case FacilityApprover = 'FACILITY_APPROVER';
    case FacilityStaff = 'FACILITY_STAFF';

    public function label(): string
    {
        return match ($this) {
            self::Sysadmin => 'Administrator Platform',
            self::DinkesAuditor => 'Auditor Dinkes',
            self::FacilityApprover => 'Kepala Fasilitas',
            self::FacilityStaff => 'Staf Farmasi',
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
        return [self::FacilityApprover->value, self::FacilityStaff->value];
    }
}
