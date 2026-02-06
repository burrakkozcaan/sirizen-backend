<?php

namespace App;

enum BrandAuthorizationStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Beklemede',
            self::APPROVED => 'Onaylandı',
            self::REJECTED => 'Reddedildi',
            self::EXPIRED => 'Süresi Doldu',
        };
    }
}
