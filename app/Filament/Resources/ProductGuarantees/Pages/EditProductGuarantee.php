<?php

namespace App\Filament\Resources\ProductGuarantees\Pages;

use App\Filament\Resources\ProductGuarantees\ProductGuaranteeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductGuarantee extends EditRecord
{
    protected static string $resource = ProductGuaranteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
