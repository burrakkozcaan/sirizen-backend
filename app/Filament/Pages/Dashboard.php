<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Kontrol Paneli';

    protected static ?string $navigationLabel = 'Kontrol Paneli';

    public function getColumns(): int|array
    {
        return 2;
    }
}
