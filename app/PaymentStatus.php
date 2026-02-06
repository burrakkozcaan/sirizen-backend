<?php

declare(strict_types=1);

namespace App;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case PartiallyRefunded = 'partially_refunded';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Beklemede',
            self::Processing => 'İşleniyor',
            self::Completed => 'Tamamlandı',
            self::Failed => 'Başarısız',
            self::Refunded => 'İade Edildi',
            self::PartiallyRefunded => 'Kısmi İade',
            self::Cancelled => 'İptal Edildi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Processing => 'info',
            self::Completed => 'success',
            self::Failed => 'danger',
            self::Refunded, self::PartiallyRefunded => 'gray',
            self::Cancelled => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Processing => 'heroicon-o-arrow-path',
            self::Completed => 'heroicon-o-check-circle',
            self::Failed => 'heroicon-o-x-circle',
            self::Refunded, self::PartiallyRefunded => 'heroicon-o-receipt-refund',
            self::Cancelled => 'heroicon-o-x-mark',
        };
    }
}
