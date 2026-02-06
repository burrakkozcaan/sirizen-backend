<?php

namespace App\Filament\Resources\ProductFaqs\Pages;

use App\Filament\Resources\ProductFaqs\ProductFaqResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductFaqs extends ListRecords
{
    protected static string $resource = ProductFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
