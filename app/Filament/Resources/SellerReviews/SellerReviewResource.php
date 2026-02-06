<?php

namespace App\Filament\Resources\SellerReviews;

use App\Filament\Resources\SellerReviews\Pages\CreateSellerReview;
use App\Filament\Resources\SellerReviews\Pages\EditSellerReview;
use App\Filament\Resources\SellerReviews\Pages\ListSellerReviews;
use App\Filament\Resources\SellerReviews\Schemas\SellerReviewForm;
use App\Filament\Resources\SellerReviews\Tables\SellerReviewsTable;
use App\Models\SellerReview;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SellerReviewResource extends Resource
{
    protected static ?string $model = SellerReview::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::INCELEME_VE_SORULAR;

    protected static ?string $navigationLabel = 'Satıcı Yorumları';

    protected static ?string $modelLabel = 'Satıcı Değerlendirmesi';

    protected static ?string $pluralModelLabel = 'Satıcı Değerlendirmeleri';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChatBubbleLeft;

    public static function form(Schema $schema): Schema
    {
        return SellerReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SellerReviewsTable::configure($table);
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
            'index' => ListSellerReviews::route('/'),
            'create' => CreateSellerReview::route('/create'),
            'edit' => EditSellerReview::route('/{record}/edit'),
        ];
    }
}
