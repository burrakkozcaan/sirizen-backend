<?php

namespace App\Filament\Resources\VendorSlaMetrics;

use App\Filament\Resources\VendorSlaMetrics\Pages\CreateVendorSlaMetric;
use App\Filament\Resources\VendorSlaMetrics\Pages\EditVendorSlaMetric;
use App\Filament\Resources\VendorSlaMetrics\Pages\ListVendorSlaMetrics;
use App\Filament\Resources\VendorSlaMetrics\Schemas\VendorSlaMetricForm;
use App\Filament\Resources\VendorSlaMetrics\Tables\VendorSlaMetricsTable;
use App\Models\VendorSlaMetric;
use App\NavigationGroup;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VendorSlaMetricResource extends Resource
{
    protected static ?string $model = VendorSlaMetric::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Clock;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ARAMA_VE_ANALYTICS;

    protected static ?string $navigationLabel = 'Satıcı SLA Metrikleri';

    protected static ?string $modelLabel = 'SLA Metriği';

    protected static ?string $pluralModelLabel = 'SLA Metrikleri';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return VendorSlaMetricForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorSlaMetricsTable::configure($table);
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
            'index' => ListVendorSlaMetrics::route('/'),
            'create' => CreateVendorSlaMetric::route('/create'),
            'edit' => EditVendorSlaMetric::route('/{record}/edit'),
        ];
    }
}
