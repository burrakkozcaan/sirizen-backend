<?php

namespace App\Filament\Resources\ProductApprovals\Pages;

use App\Filament\Resources\ProductApprovals\ProductApprovalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductApproval extends EditRecord
{
    protected static string $resource = ProductApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
