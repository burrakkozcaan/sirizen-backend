<?php

namespace App\Filament\Resources\Wishlists;

use App\Filament\Resources\Wishlists\Pages\CreateWishlist;
use App\Filament\Resources\Wishlists\Pages\EditWishlist;
use App\Filament\Resources\Wishlists\Pages\ListWishlists;
use App\Filament\Resources\Wishlists\Schemas\WishlistForm;
use App\Filament\Resources\Wishlists\Tables\WishlistsTable;
use App\Models\Wishlist;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WishlistResource extends Resource
{
    protected static ?string $model = Wishlist::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::FAVORI_VE_LISTELER;

    protected static ?string $navigationLabel = 'İstek Listeleri';

    protected static ?string $modelLabel = 'İstek Listesi';

    protected static ?string $pluralModelLabel = 'İstek Listeleri';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bookmark;

    public static function form(Schema $schema): Schema
    {
        return WishlistForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WishlistsTable::configure($table);
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
            'index' => ListWishlists::route('/'),
            'create' => CreateWishlist::route('/create'),
            'edit' => EditWishlist::route('/{record}/edit'),
        ];
    }
}
