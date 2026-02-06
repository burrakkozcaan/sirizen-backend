<?php

namespace App\Filament\Resources\VendorTiers\Pages;

use App\Filament\Resources\VendorTiers\VendorTierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendorTiers extends ListRecords
{
    protected static string $resource = VendorTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
