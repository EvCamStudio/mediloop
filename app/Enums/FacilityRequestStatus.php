<?php

namespace App\Enums;

enum FacilityRequestStatus: string
{
    case Pending = 'PENDING';
    case Approved = 'APPROVED';
    case Rejected = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu review',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
        };
    }
}
