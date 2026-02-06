<?php

namespace App\Filament\Resources\PriceHistories;

use App\Filament\Resources\PriceHistories\Pages\CreatePriceHistory;
use App\Filament\Resources\PriceHistories\Pages\EditPriceHistory;
use App\Filament\Resources\PriceHistories\Pages\ListPriceHistories;
use App\Filament\Resources\PriceHistories\Schemas\PriceHistoryForm;
use App\Filament\Resources\PriceHistories\Tables\PriceHistoriesTable;
use App\Models\PriceHistory;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PriceHistoryResource extends Resource
{
    protected static ?string $model = PriceHistory::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ARAMA_VE_ANALYTICS;

    protected static ?string $navigationLabel = 'Fiyat Geçmişi';

    protected static ?string $modelLabel = 'Fiyat Geçmişi';

    protected static ?string $pluralModelLabel = 'Fiyat Geçmişleri';

    protected static ?int $navigationSort = 6;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;

    public static function form(Schema $schema): Schema
    {
        return PriceHistoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PriceHistoriesTable::configure($table);
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
            'index' => ListPriceHistories::route('/'),
            'create' => CreatePriceHistory::route('/create'),
            'edit' => EditPriceHistory::route('/{record}/edit'),
        ];
    }
}
