<?php

namespace App;

enum CommissionStatus: string
{
    case PENDING = 'pending';            // Sipariş oluşturuldu, ödeme bekleniyor
    case PAID = 'paid';                  // Ödeme alındı, teslim bekleniyor
    case SETTLED = 'settled';           // Teslim edildi, bakiyeye eklendi
    case CANCELLED = 'cancelled';       // Sipariş/ödeme iptal
    case REFUNDED = 'refunded';         // Tam iade
    case PARTIALLY_REFUNDED = 'partially_refunded'; // Kısmi iade

    public function label(): string
    {
        return match ($this) {
            self::PENDING            => 'Beklemede',
            self::PAID               => 'Ödeme Alındı',
            self::SETTLED            => 'Kesinleşti',
            self::CANCELLED          => 'İptal',
            self::REFUNDED           => 'İade Edildi',
            self::PARTIALLY_REFUNDED => 'Kısmi İade',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING            => 'warning',
            self::PAID               => 'info',
            self::SETTLED            => 'success',
            self::CANCELLED          => 'gray',
            self::REFUNDED           => 'danger',
            self::PARTIALLY_REFUNDED => 'warning',
        };
    }
}
