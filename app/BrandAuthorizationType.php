<?php

namespace App;

enum BrandAuthorizationType: string
{
    case OWNER = 'owner';
    case AUTHORIZED_DEALER = 'authorized_dealer';
    case INVOICE_CHAIN = 'invoice_chain';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Marka Sahibi',
            self::AUTHORIZED_DEALER => 'Yetkili Satıcı',
            self::INVOICE_CHAIN => 'Fatura Silsilesi',
        };
    }
}
