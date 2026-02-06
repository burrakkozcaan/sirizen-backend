<?php

namespace App\Filament\Resources\PlatformRevenueReports;

use App\Filament\Resources\PlatformRevenueReports\Pages\CreatePlatformRevenueReport;
use App\Filament\Resources\PlatformRevenueReports\Pages\EditPlatformRevenueReport;
use App\Filament\Resources\PlatformRevenueReports\Pages\ListPlatformRevenueReports;
use App\Filament\Resources\PlatformRevenueReports\Schemas\PlatformRevenueReportForm;
use App\Filament\Resources\PlatformRevenueReports\Tables\PlatformRevenueReportsTable;
use App\Models\PlatformRevenueReport;
use App\NavigationGroup;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PlatformRevenueReportResource extends Resource
{
    protected static ?string $model = PlatformRevenueReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ARAMA_VE_ANALYTICS;

    protected static ?string $navigationLabel = 'Platform Gelir Raporları';

    protected static ?string $modelLabel = 'Platform Gelir Raporu';

    protected static ?string $pluralModelLabel = 'Platform Gelir Raporları';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return PlatformRevenueReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlatformRevenueReportsTable::configure($table);
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
            'index' => ListPlatformRevenueReports::route('/'),
            'create' => CreatePlatformRevenueReport::route('/create'),
            'edit' => EditPlatformRevenueReport::route('/{record}/edit'),
        ];
    }
}
