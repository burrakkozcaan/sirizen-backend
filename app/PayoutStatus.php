<?php

namespace App;

enum PayoutStatus: string
{
    case Pending = 'pending';
    case Waiting = 'waiting';
    case Processing = 'processing';
    case Completed = 'completed';
    case Paid = 'paid';
    case Failed = 'failed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Beklemede',
            self::Waiting => 'Onay Bekliyor',
            self::Processing => 'İşleniyor',
            self::Completed, self::Paid => 'Ödendi',
            self::Failed => 'Başarısız',
            self::Cancelled => 'İptal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending, self::Waiting => 'warning',
            self::Processing => 'info',
            self::Completed, self::Paid => 'success',
            self::Failed => 'danger',
            self::Cancelled => 'gray',
        };
    }
}
