<?php

namespace App\Filament\Resources\ProductLiveStats;

use App\Filament\Resources\ProductLiveStats\Pages\CreateProductLiveStat;
use App\Filament\Resources\ProductLiveStats\Pages\EditProductLiveStat;
use App\Filament\Resources\ProductLiveStats\Pages\ListProductLiveStats;
use App\Filament\Resources\ProductLiveStats\Schemas\ProductLiveStatForm;
use App\Filament\Resources\ProductLiveStats\Tables\ProductLiveStatsTable;
use App\Models\ProductLiveStat;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProductLiveStatResource extends Resource
{
    protected static ?string $model = ProductLiveStat::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ARAMA_VE_ANALYTICS;

    protected static ?string $navigationLabel = 'Ürün Canlı İstatistikleri';

    protected static ?string $modelLabel = 'Canlı İstatistik';

    protected static ?string $pluralModelLabel = 'Canlı İstatistikler';

    protected static ?int $navigationSort = 7;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBarSquare;

    public static function form(Schema $schema): Schema
    {
        return ProductLiveStatForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductLiveStatsTable::configure($table);
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
            'index' => ListProductLiveStats::route('/'),
            'create' => CreateProductLiveStat::route('/create'),
            'edit' => EditProductLiveStat::route('/{record}/edit'),
        ];
    }
}
