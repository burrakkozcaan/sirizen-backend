<?php

namespace App\Filament\Resources\BadgeRules\Pages;

use App\Filament\Resources\BadgeRules\BadgeRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBadgeRule extends EditRecord
{
    protected static string $resource = BadgeRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
