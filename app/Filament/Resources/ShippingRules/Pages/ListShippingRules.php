<?php

namespace App\Filament\Resources\ShippingRules\Pages;

use App\Filament\Resources\ShippingRules\ShippingRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShippingRules extends ListRecords
{
    protected static string $resource = ShippingRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
