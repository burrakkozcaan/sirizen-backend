<?php

namespace App\Filament\Resources\BadgeDefinitions\Pages;

use App\Filament\Resources\BadgeDefinitions\BadgeDefinitionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBadgeDefinition extends EditRecord
{
    protected static string $resource = BadgeDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
