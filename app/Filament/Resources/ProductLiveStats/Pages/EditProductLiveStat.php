<?php

namespace App\Filament\Resources\ProductLiveStats\Pages;

use App\Filament\Resources\ProductLiveStats\ProductLiveStatResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductLiveStat extends EditRecord
{
    protected static string $resource = ProductLiveStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
