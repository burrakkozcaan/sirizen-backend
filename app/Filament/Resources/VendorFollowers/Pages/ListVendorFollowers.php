<?php

namespace App\Filament\Resources\VendorFollowers\Pages;

use App\Filament\Resources\VendorFollowers\VendorFollowerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendorFollowers extends ListRecords
{
    protected static string $resource = VendorFollowerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
