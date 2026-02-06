<?php

namespace App\Filament\Resources\VendorScores\Pages;

use App\Filament\Resources\VendorScores\VendorScoreResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorScore extends EditRecord
{
    protected static string $resource = VendorScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
