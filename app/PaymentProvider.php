<?php

declare(strict_types=1);

namespace App;

enum PaymentProvider: string
{
    case Iyzico = 'iyzico';
    case Paytr = 'paytr';
    case Test = 'test';

    public function label(): string
    {
        return match ($this) {
            self::Iyzico => 'iyzico',
            self::Paytr => 'PayTR',
            self::Test => 'Test',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Iyzico => 'info',
            self::Paytr => 'warning',
            self::Test => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Iyzico => 'heroicon-o-credit-card',
            self::Paytr => 'heroicon-o-credit-card',
            self::Test => 'heroicon-o-beaker',
        };
    }
}
