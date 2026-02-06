<?php

namespace App\Filament\Resources\CartItems;

use App\Filament\Resources\CartItems\Pages\CreateCartItem;
use App\Filament\Resources\CartItems\Pages\EditCartItem;
use App\Filament\Resources\CartItems\Pages\ListCartItems;
use App\Filament\Resources\CartItems\Schemas\CartItemForm;
use App\Filament\Resources\CartItems\Tables\CartItemsTable;
use App\Models\CartItem;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CartItemResource extends Resource
{
    protected static ?string $model = CartItem::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ALISVERIS_SEPETI;

    protected static ?string $navigationLabel = 'Sepet Kalemleri';

    protected static ?string $modelLabel = 'Sepet Ürünü';

    protected static ?string $pluralModelLabel = 'Sepet Ürünleri';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ListBullet;

    public static function form(Schema $schema): Schema
    {
        return CartItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CartItemsTable::configure($table);
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
            'index' => ListCartItems::route('/'),
            'create' => CreateCartItem::route('/create'),
            'edit' => EditCartItem::route('/{record}/edit'),
        ];
    }
}
