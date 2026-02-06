<?php

namespace App\Filament\Resources\SocialProofRules\Pages;

use App\Filament\Resources\SocialProofRules\SocialProofRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSocialProofRules extends ListRecords
{
    protected static string $resource = SocialProofRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
