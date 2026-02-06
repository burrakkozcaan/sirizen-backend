<?php

namespace App\Filament\Resources\ReviewHelpfulVotes;

use App\Filament\Resources\ReviewHelpfulVotes\Pages\CreateReviewHelpfulVote;
use App\Filament\Resources\ReviewHelpfulVotes\Pages\EditReviewHelpfulVote;
use App\Filament\Resources\ReviewHelpfulVotes\Pages\ListReviewHelpfulVotes;
use App\Filament\Resources\ReviewHelpfulVotes\Schemas\ReviewHelpfulVoteForm;
use App\Filament\Resources\ReviewHelpfulVotes\Tables\ReviewHelpfulVotesTable;
use App\Models\ReviewHelpfulVote;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ReviewHelpfulVoteResource extends Resource
{
    protected static ?string $model = ReviewHelpfulVote::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::INCELEME_VE_SORULAR;

    protected static ?string $navigationLabel = 'Faydalı Oylar';

    protected static ?string $modelLabel = 'Faydalı Oy';

    protected static ?string $pluralModelLabel = 'Faydalı Oylar';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::HandThumbUp;

    public static function form(Schema $schema): Schema
    {
        return ReviewHelpfulVoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReviewHelpfulVotesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviewHelpfulVotes::route('/'),
            'create' => CreateReviewHelpfulVote::route('/create'),
            'edit' => EditReviewHelpfulVote::route('/{record}/edit'),
        ];
    }
}
