<?php

namespace App\Filament\Resources\SellerPages\Pages;

use App\Filament\Resources\SellerPages\SellerPageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSellerPage extends EditRecord
{
    protected static string $resource = SellerPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
