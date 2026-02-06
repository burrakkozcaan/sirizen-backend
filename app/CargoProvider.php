<?php

declare(strict_types=1);

namespace App;

enum CargoProvider: string
{
    case Aras = 'aras';
    case Yurtici = 'yurtici';
    case Mng = 'mng';
    case Ptt = 'ptt';
    case Surat = 'surat';
    case Ups = 'ups';
    case Dhl = 'dhl';

    public function label(): string
    {
        return match ($this) {
            self::Aras => 'Aras Kargo',
            self::Yurtici => 'Yurtiçi Kargo',
            self::Mng => 'MNG Kargo',
            self::Ptt => 'PTT Kargo',
            self::Surat => 'Sürat Kargo',
            self::Ups => 'UPS',
            self::Dhl => 'DHL',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Aras => 'danger',
            self::Yurtici => 'warning',
            self::Mng => 'success',
            self::Ptt => 'info',
            self::Surat => 'primary',
            self::Ups => 'warning',
            self::Dhl => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Aras, self::Yurtici, self::Mng, self::Ptt, self::Surat => 'heroicon-o-truck',
            self::Ups, self::Dhl => 'heroicon-o-globe-alt',
        };
    }

    public function trackingUrl(string $trackingNumber): ?string
    {
        return match ($this) {
            self::Aras => "https://www.araskargo.com.tr/taki.aspx?kession={$trackingNumber}",
            self::Yurtici => "https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula?code={$trackingNumber}",
            self::Mng => "https://www.mngkargo.com.tr/gonderi-takip/?gonderikodu={$trackingNumber}",
            self::Ptt => "https://gonderitakip.ptt.gov.tr/Track/Verify?q={$trackingNumber}",
            self::Surat => "https://suratkargo.com.tr/KargoTakip?kargotakipno={$trackingNumber}",
            self::Ups => "https://www.ups.com/track?tracknum={$trackingNumber}",
            self::Dhl => "https://www.dhl.com/tr-tr/home/tracking/tracking-global-forwarding.html?submit=1&tracking-id={$trackingNumber}",
        };
    }

    /**
     * Aktif olarak entegre edilmiş provider'ları döndür
     *
     * @return array<self>
     */
    public static function integrated(): array
    {
        return [
            self::Aras,
            self::Yurtici,
            self::Mng,
        ];
    }
}
