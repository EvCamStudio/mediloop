<?php

namespace App\Enums;

enum UserStatus: string
{
    case Invited = 'INVITED';
    case Active = 'ACTIVE';
    case Suspended = 'SUSPENDED';

    public function label(): string
    {
        return match ($this) {
            self::Invited => __('enums.user_status.invited'),
            self::Active => __('enums.user_status.active'),
            self::Suspended => __('enums.user_status.suspended'),
        };
    }
}
