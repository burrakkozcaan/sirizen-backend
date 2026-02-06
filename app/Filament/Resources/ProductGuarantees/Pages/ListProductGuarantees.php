<?php

namespace App\Filament\Resources\ProductGuarantees\Pages;

use App\Filament\Resources\ProductGuarantees\ProductGuaranteeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductGuarantees extends ListRecords
{
    protected static string $resource = ProductGuaranteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
