<?php

namespace App\Filament\Resources\CargoIntegrations\Pages;

use App\Filament\Resources\CargoIntegrations\CargoIntegrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCargoIntegrations extends ListRecords
{
    protected static string $resource = CargoIntegrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
