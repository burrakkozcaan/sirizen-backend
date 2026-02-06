<?php

namespace App\Filament\Resources\BadgeDefinitions\Pages;

use App\Filament\Resources\BadgeDefinitions\BadgeDefinitionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBadgeDefinitions extends ListRecords
{
    protected static string $resource = BadgeDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
