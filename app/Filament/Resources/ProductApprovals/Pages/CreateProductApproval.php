<?php

namespace App\Filament\Resources\ProductApprovals\Pages;

use App\Filament\Resources\ProductApprovals\ProductApprovalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductApproval extends CreateRecord
{
    protected static string $resource = ProductApprovalResource::class;
}
