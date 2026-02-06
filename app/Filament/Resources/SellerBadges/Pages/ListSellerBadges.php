<?php

namespace App\Filament\Resources\SellerBadges\Pages;

use App\Filament\Resources\SellerBadges\SellerBadgeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSellerBadges extends ListRecords
{
    protected static string $resource = SellerBadgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
