<?php

namespace App\Filament\Resources\ProductLiveStats\Pages;

use App\Filament\Resources\ProductLiveStats\ProductLiveStatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductLiveStats extends ListRecords
{
    protected static string $resource = ProductLiveStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
