<?php

namespace App\Filament\Resources\VendorPenalties\Pages;

use App\Filament\Resources\VendorPenalties\VendorPenaltyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendorPenalties extends ListRecords
{
    protected static string $resource = VendorPenaltyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
