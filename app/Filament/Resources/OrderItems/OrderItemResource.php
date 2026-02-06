<?php

namespace App\Filament\Resources\OrderItems;

use App\Filament\Resources\OrderItems\Pages\CreateOrderItem;
use App\Filament\Resources\OrderItems\Pages\EditOrderItem;
use App\Filament\Resources\OrderItems\Pages\ListOrderItems;
use App\Filament\Resources\OrderItems\Schemas\OrderItemForm;
use App\Filament\Resources\OrderItems\Tables\OrderItemsTable;
use App\Models\OrderItem;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SIPARIS_YONETIMI;

    protected static ?string $navigationLabel = 'Sipariş Kalemleri';

    protected static ?string $modelLabel = 'Sipariş Kalemi';

    protected static ?string $pluralModelLabel = 'Sipariş Kalemleri';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ListBullet;

    public static function form(Schema $schema): Schema
    {
        return OrderItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderItemsTable::configure($table);
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
            'index' => ListOrderItems::route('/'),
            'create' => CreateOrderItem::route('/create'),
            'edit' => EditOrderItem::route('/{record}/edit'),
        ];
    }
}
