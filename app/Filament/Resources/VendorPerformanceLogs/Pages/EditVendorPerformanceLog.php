<?php

namespace App\Filament\Resources\VendorPerformanceLogs\Pages;

use App\Filament\Resources\VendorPerformanceLogs\VendorPerformanceLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorPerformanceLog extends EditRecord
{
    protected static string $resource = VendorPerformanceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
