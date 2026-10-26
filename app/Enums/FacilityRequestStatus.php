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
            self::Pending => __('enums.request_status.pending'),
            self::Approved => __('enums.request_status.approved'),
            self::Rejected => __('enums.request_status.rejected'),
        };
    }
}
