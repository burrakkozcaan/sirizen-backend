<?php

namespace App\Filament\Resources\ProductApprovals\Pages;

use App\Filament\Resources\ProductApprovals\ProductApprovalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductApprovals extends ListRecords
{
    protected static string $resource = ProductApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
