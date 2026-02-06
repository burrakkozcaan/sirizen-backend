<?php

namespace App\Filament\Resources\SellerPages\Pages;

use App\Filament\Resources\SellerPages\SellerPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSellerPages extends ListRecords
{
    protected static string $resource = SellerPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
