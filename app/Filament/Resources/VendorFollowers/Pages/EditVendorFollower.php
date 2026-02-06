<?php

namespace App\Filament\Resources\VendorFollowers\Pages;

use App\Filament\Resources\VendorFollowers\VendorFollowerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorFollower extends EditRecord
{
    protected static string $resource = VendorFollowerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
