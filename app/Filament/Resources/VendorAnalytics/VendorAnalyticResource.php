<?php

namespace App\Filament\Resources\VendorAnalytics;

use App\Filament\Resources\VendorAnalytics\Pages\CreateVendorAnalytic;
use App\Filament\Resources\VendorAnalytics\Pages\EditVendorAnalytic;
use App\Filament\Resources\VendorAnalytics\Pages\ListVendorAnalytics;
use App\Filament\Resources\VendorAnalytics\Schemas\VendorAnalyticForm;
use App\Filament\Resources\VendorAnalytics\Tables\VendorAnalyticsTable;
use App\Models\VendorAnalytic;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VendorAnalyticResource extends Resource
{
    protected static ?string $model = VendorAnalytic::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ARAMA_VE_ANALYTICS;

    protected static ?string $navigationLabel = 'Satıcı Analizleri';

    protected static ?string $modelLabel = 'Satıcı Analitiği';

    protected static ?string $pluralModelLabel = 'Satıcı Analitikleri';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;


    public static function form(Schema $schema): Schema
    {
        return VendorAnalyticForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorAnalyticsTable::configure($table);
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
            'index' => ListVendorAnalytics::route('/'),
            'create' => CreateVendorAnalytic::route('/create'),
            'edit' => EditVendorAnalytic::route('/{record}/edit'),
        ];
    }
}
