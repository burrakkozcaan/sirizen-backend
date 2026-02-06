<?php

namespace App\Filament\Resources\VendorAnalytics\Pages;

use App\Filament\Resources\VendorAnalytics\VendorAnalyticResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorAnalytic extends EditRecord
{
    protected static string $resource = VendorAnalyticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
