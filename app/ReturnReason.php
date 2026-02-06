<?php

namespace App;

enum ReturnReason: string
{
    case WRONG_SIZE = 'wrong_size';
    case WRONG_PRODUCT = 'wrong_product';
    case DAMAGED = 'damaged';
    case DEFECTIVE = 'defective';
    case NOT_AS_DESCRIBED = 'not_as_described';
    case QUALITY_ISSUE = 'quality_issue';
    case LATE_DELIVERY = 'late_delivery';
    case CHANGED_MIND = 'changed_mind';
    case FOUND_CHEAPER = 'found_cheaper';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::WRONG_SIZE => 'Beden Uygun Değil',
            self::WRONG_PRODUCT => 'Yanlış Ürün Geldi',
            self::DAMAGED => 'Hasarlı/Kırık Geldi',
            self::DEFECTIVE => 'Kusurlu/Arızalı',
            self::NOT_AS_DESCRIBED => 'Açıklamaya Uygun Değil',
            self::QUALITY_ISSUE => 'Kalite Sorunu',
            self::LATE_DELIVERY => 'Geç Teslim Edildi',
            self::CHANGED_MIND => 'Fikrim Değişti',
            self::FOUND_CHEAPER => 'Daha Ucuzunu Buldum',
            self::OTHER => 'Diğer',
        };
    }
}
