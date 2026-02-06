<?php

namespace App\Filament\Resources\VendorDailyStats\Pages;

use App\Filament\Resources\VendorDailyStats\VendorDailyStatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendorDailyStats extends ListRecords
{
    protected static string $resource = VendorDailyStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
