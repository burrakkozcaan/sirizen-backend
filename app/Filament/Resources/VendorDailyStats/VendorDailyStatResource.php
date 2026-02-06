<?php

namespace App\Filament\Resources\VendorDailyStats;

use App\Filament\Resources\VendorDailyStats\Pages\CreateVendorDailyStat;
use App\Filament\Resources\VendorDailyStats\Pages\EditVendorDailyStat;
use App\Filament\Resources\VendorDailyStats\Pages\ListVendorDailyStats;
use App\Filament\Resources\VendorDailyStats\Schemas\VendorDailyStatForm;
use App\Filament\Resources\VendorDailyStats\Tables\VendorDailyStatsTable;
use App\Models\VendorDailyStat;
use App\NavigationGroup;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VendorDailyStatResource extends Resource
{
    protected static ?string $model = VendorDailyStat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ARAMA_VE_ANALYTICS;

    protected static ?string $navigationLabel = 'Satıcı Günlük İstatistikleri';

    protected static ?string $modelLabel = 'Günlük İstatistik';

    protected static ?string $pluralModelLabel = 'Günlük İstatistikler';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return VendorDailyStatForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorDailyStatsTable::configure($table);
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
            'index' => ListVendorDailyStats::route('/'),
            'create' => CreateVendorDailyStat::route('/create'),
            'edit' => EditVendorDailyStat::route('/{record}/edit'),
        ];
    }
}
