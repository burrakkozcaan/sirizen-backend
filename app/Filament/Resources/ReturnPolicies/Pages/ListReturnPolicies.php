<?php

namespace App\Filament\Resources\ReturnPolicies\Pages;

use App\Filament\Resources\ReturnPolicies\ReturnPolicyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReturnPolicies extends ListRecords
{
    protected static string $resource = ReturnPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
