<?php

namespace App\Filament\Resources\StockAlerts;

use App\Filament\Resources\StockAlerts\Pages\CreateStockAlert;
use App\Filament\Resources\StockAlerts\Pages\EditStockAlert;
use App\Filament\Resources\StockAlerts\Pages\ListStockAlerts;
use App\Filament\Resources\StockAlerts\Schemas\StockAlertForm;
use App\Filament\Resources\StockAlerts\Tables\StockAlertsTable;
use App\Models\StockAlert;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StockAlertResource extends Resource
{
    protected static ?string $model = StockAlert::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::BILDIRIMLER;

    protected static ?string $navigationLabel = 'Stok Uyarıları';

    protected static ?string $modelLabel = 'Stok Uyarısı';

    protected static ?string $pluralModelLabel = 'Stok Uyarıları';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cube;

    public static function form(Schema $schema): Schema
    {
        return StockAlertForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockAlertsTable::configure($table);
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
            'index' => ListStockAlerts::route('/'),
            'create' => CreateStockAlert::route('/create'),
            'edit' => EditStockAlert::route('/{record}/edit'),
        ];
    }
}
