<?php

namespace App\Filament\Resources\VendorBalances\Pages;

use App\Filament\Resources\VendorBalances\VendorBalanceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorBalance extends EditRecord
{
    protected static string $resource = VendorBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
