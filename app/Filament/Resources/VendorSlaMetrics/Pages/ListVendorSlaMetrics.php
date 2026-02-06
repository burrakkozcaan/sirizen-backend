<?php

namespace App\Filament\Resources\VendorSlaMetrics\Pages;

use App\Filament\Resources\VendorSlaMetrics\VendorSlaMetricResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendorSlaMetrics extends ListRecords
{
    protected static string $resource = VendorSlaMetricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
