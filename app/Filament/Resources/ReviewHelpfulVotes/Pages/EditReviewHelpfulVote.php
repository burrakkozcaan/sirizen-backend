<?php

namespace App\Filament\Resources\ReviewHelpfulVotes\Pages;

use App\Filament\Resources\ReviewHelpfulVotes\ReviewHelpfulVoteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReviewHelpfulVote extends EditRecord
{
    protected static string $resource = ReviewHelpfulVoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
