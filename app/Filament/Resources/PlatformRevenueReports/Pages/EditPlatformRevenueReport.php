<?php

namespace App\Filament\Resources\PlatformRevenueReports\Pages;

use App\Filament\Resources\PlatformRevenueReports\PlatformRevenueReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlatformRevenueReport extends EditRecord
{
    protected static string $resource = PlatformRevenueReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
