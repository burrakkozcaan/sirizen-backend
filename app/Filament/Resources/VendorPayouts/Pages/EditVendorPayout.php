<?php

namespace App\Filament\Resources\VendorPayouts\Pages;

use App\Filament\Resources\VendorPayouts\VendorPayoutResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorPayout extends EditRecord
{
    protected static string $resource = VendorPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
