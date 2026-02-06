<?php

namespace App\Filament\Resources\PlatformRevenueReports\Pages;

use App\Filament\Resources\PlatformRevenueReports\PlatformRevenueReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlatformRevenueReports extends ListRecords
{
    protected static string $resource = PlatformRevenueReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
