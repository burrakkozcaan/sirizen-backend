<?php

namespace App\Filament\Resources\CargoIntegrations\Pages;

use App\Filament\Resources\CargoIntegrations\CargoIntegrationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCargoIntegration extends EditRecord
{
    protected static string $resource = CargoIntegrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
