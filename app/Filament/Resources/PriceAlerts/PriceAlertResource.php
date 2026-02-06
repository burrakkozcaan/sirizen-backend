<?php

namespace App\Filament\Resources\PriceAlerts;

use App\Filament\Resources\PriceAlerts\Pages\CreatePriceAlert;
use App\Filament\Resources\PriceAlerts\Pages\EditPriceAlert;
use App\Filament\Resources\PriceAlerts\Pages\ListPriceAlerts;
use App\Filament\Resources\PriceAlerts\Schemas\PriceAlertForm;
use App\Filament\Resources\PriceAlerts\Tables\PriceAlertsTable;
use App\Models\PriceAlert;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PriceAlertResource extends Resource
{
    protected static ?string $model = PriceAlert::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::BILDIRIMLER;

    protected static ?string $navigationLabel = 'Fiyat Uyarıları';

    protected static ?string $modelLabel = 'Fiyat Uyarısı';

    protected static ?string $pluralModelLabel = 'Fiyat Uyarıları';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;

    public static function form(Schema $schema): Schema
    {
        return PriceAlertForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PriceAlertsTable::configure($table);
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
            'index' => ListPriceAlerts::route('/'),
            'create' => CreatePriceAlert::route('/create'),
            'edit' => EditPriceAlert::route('/{record}/edit'),
        ];
    }
}
