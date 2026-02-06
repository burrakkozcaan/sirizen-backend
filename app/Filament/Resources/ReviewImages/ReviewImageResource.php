<?php

namespace App\Filament\Resources\ReviewImages;

use App\Filament\Resources\ReviewImages\Pages\CreateReviewImage;
use App\Filament\Resources\ReviewImages\Pages\EditReviewImage;
use App\Filament\Resources\ReviewImages\Pages\ListReviewImages;
use App\Filament\Resources\ReviewImages\Schemas\ReviewImageForm;
use App\Filament\Resources\ReviewImages\Tables\ReviewImagesTable;
use App\Models\ReviewImage;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ReviewImageResource extends Resource
{
    protected static ?string $model = ReviewImage::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::INCELEME_VE_SORULAR;

    protected static ?string $navigationLabel = 'Yorum Görselleri';

    protected static ?string $modelLabel = 'Yorum Görseli';

    protected static ?string $pluralModelLabel = 'Yorum Görselleri';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Photo;

    public static function form(Schema $schema): Schema
    {
        return ReviewImageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReviewImagesTable::configure($table);
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
            'index' => ListReviewImages::route('/'),
            'create' => CreateReviewImage::route('/create'),
            'edit' => EditReviewImage::route('/{record}/edit'),
        ];
    }
}
