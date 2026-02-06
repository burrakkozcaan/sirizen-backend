<?php

namespace App\Filament\Resources\VendorBalances\Pages;

use App\Filament\Resources\VendorBalances\VendorBalanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendorBalances extends ListRecords
{
    protected static string $resource = VendorBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
