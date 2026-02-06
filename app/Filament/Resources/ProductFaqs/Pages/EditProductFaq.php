<?php

namespace App\Filament\Resources\ProductFaqs\Pages;

use App\Filament\Resources\ProductFaqs\ProductFaqResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductFaq extends EditRecord
{
    protected static string $resource = ProductFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
