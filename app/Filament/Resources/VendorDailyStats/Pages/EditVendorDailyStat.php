<?php

namespace App\Filament\Resources\VendorDailyStats\Pages;

use App\Filament\Resources\VendorDailyStats\VendorDailyStatResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorDailyStat extends EditRecord
{
    protected static string $resource = VendorDailyStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
