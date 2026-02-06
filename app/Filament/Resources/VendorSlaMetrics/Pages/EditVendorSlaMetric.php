<?php

namespace App\Filament\Resources\VendorSlaMetrics\Pages;

use App\Filament\Resources\VendorSlaMetrics\VendorSlaMetricResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorSlaMetric extends EditRecord
{
    protected static string $resource = VendorSlaMetricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
