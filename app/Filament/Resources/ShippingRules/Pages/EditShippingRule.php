<?php

namespace App\Filament\Resources\ShippingRules\Pages;

use App\Filament\Resources\ShippingRules\ShippingRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShippingRule extends EditRecord
{
    protected static string $resource = ShippingRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
