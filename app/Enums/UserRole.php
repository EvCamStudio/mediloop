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
            self::Sysadmin => __('enums.user_role.sysadmin'),
            self::DinkesAuditor => __('enums.user_role.dinkes_auditor'),
            self::FacilityApprover => __('enums.user_role.facility_approver'),
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
        return [self::FacilityApprover->value, self::FacilityStaff->value];
    }
}
