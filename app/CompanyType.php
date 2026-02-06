<?php

namespace App;

enum CompanyType: string
{
    case LIMITED = 'limited';
    case ANONIM = 'anonim';
    case SAHIS = 'sahis';
    case KOLEKTIF = 'kolektif';
    case KOMANDIT = 'komandit';
    case KOOPERATIF = 'kooperatif';

    public function label(): string
    {
        return match ($this) {
            self::LIMITED => 'Limited Şirket',
            self::ANONIM => 'Anonim Şirket',
            self::SAHIS => 'Şahıs Şirketi',
            self::KOLEKTIF => 'Kolektif Şirket',
            self::KOMANDIT => 'Komandit Şirket',
            self::KOOPERATIF => 'Kooperatif',
        };
    }
}
