<?php

namespace App\Filament\Resources\VendorAnalytics\Pages;

use App\Filament\Resources\VendorAnalytics\VendorAnalyticResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendorAnalytics extends ListRecords
{
    protected static string $resource = VendorAnalyticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
