<?php

namespace App\Filament\Resources\CategoryGroups\Pages;

use App\Filament\Resources\CategoryGroups\CategoryGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategoryGroup extends CreateRecord
{
    protected static string $resource = CategoryGroupResource::class;
}
