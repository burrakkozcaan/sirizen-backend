<?php

namespace App\Filament\Resources\FilterConfigs\Pages;

use App\Filament\Resources\FilterConfigs\FilterConfigResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFilterConfigs extends ListRecords
{
    protected static string $resource = FilterConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
