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
            self::Invited => 'Diundang',
            self::Active => 'Aktif',
            self::Suspended => 'Ditangguhkan',
        };
    }
}
