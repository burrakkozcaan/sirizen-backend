<?php

namespace App\Filament\Resources\ProductFaqs\Pages;

use App\Filament\Resources\ProductFaqs\ProductFaqResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductFaq extends CreateRecord
{
    protected static string $resource = ProductFaqResource::class;
}
