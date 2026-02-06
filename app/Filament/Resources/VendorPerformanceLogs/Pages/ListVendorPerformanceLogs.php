<?php

namespace App\Filament\Resources\VendorPerformanceLogs\Pages;

use App\Filament\Resources\VendorPerformanceLogs\VendorPerformanceLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendorPerformanceLogs extends ListRecords
{
    protected static string $resource = VendorPerformanceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
