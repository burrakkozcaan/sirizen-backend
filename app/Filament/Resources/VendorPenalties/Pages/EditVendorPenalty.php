<?php

namespace App\Filament\Resources\VendorPenalties\Pages;

use App\Filament\Resources\VendorPenalties\VendorPenaltyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorPenalty extends EditRecord
{
    protected static string $resource = VendorPenaltyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
