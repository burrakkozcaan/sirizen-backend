<?php

namespace App\Filament\Resources\RecentlyVieweds\Pages;

use App\Filament\Resources\RecentlyVieweds\RecentlyViewedResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRecentlyVieweds extends ListRecords
{
    protected static string $resource = RecentlyViewedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
