<?php

namespace App\Filament\Resources\ReviewHelpfulVotes\Pages;

use App\Filament\Resources\ReviewHelpfulVotes\ReviewHelpfulVoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReviewHelpfulVotes extends ListRecords
{
    protected static string $resource = ReviewHelpfulVoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
