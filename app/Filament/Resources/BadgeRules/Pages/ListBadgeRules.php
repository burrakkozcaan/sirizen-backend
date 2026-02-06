<?php

namespace App\Filament\Resources\BadgeRules\Pages;

use App\Filament\Resources\BadgeRules\BadgeRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBadgeRules extends ListRecords
{
    protected static string $resource = BadgeRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
