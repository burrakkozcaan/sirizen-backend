<?php

namespace App\Filament\Resources\VendorTiers\Pages;

use App\Filament\Resources\VendorTiers\VendorTierResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorTier extends EditRecord
{
    protected static string $resource = VendorTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
