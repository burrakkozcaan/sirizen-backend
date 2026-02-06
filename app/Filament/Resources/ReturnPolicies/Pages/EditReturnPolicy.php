<?php

namespace App\Filament\Resources\ReturnPolicies\Pages;

use App\Filament\Resources\ReturnPolicies\ReturnPolicyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReturnPolicy extends EditRecord
{
    protected static string $resource = ReturnPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
