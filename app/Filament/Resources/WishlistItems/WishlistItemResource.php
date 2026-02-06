<?php

namespace App\Filament\Resources\WishlistItems;

use App\Filament\Resources\WishlistItems\Pages\CreateWishlistItem;
use App\Filament\Resources\WishlistItems\Pages\EditWishlistItem;
use App\Filament\Resources\WishlistItems\Pages\ListWishlistItems;
use App\Filament\Resources\WishlistItems\Schemas\WishlistItemForm;
use App\Filament\Resources\WishlistItems\Tables\WishlistItemsTable;
use App\Models\WishlistItem;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WishlistItemResource extends Resource
{
    protected static ?string $model = WishlistItem::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::FAVORI_VE_LISTELER;

    protected static ?string $navigationLabel = 'İstek Kalemleri';

    protected static ?string $modelLabel = 'İstek Listesi Ürünü';

    protected static ?string $pluralModelLabel = 'İstek Listesi Ürünleri';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ListBullet;

    public static function form(Schema $schema): Schema
    {
        return WishlistItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WishlistItemsTable::configure($table);
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
            'index' => ListWishlistItems::route('/'),
            'create' => CreateWishlistItem::route('/create'),
            'edit' => EditWishlistItem::route('/{record}/edit'),
        ];
    }
}
