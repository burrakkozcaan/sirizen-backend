<?php

namespace App\Filament\Resources\VendorPerformanceLogs;

use App\Filament\Resources\VendorPerformanceLogs\Pages\CreateVendorPerformanceLog;
use App\Filament\Resources\VendorPerformanceLogs\Pages\EditVendorPerformanceLog;
use App\Filament\Resources\VendorPerformanceLogs\Pages\ListVendorPerformanceLogs;
use App\Filament\Resources\VendorPerformanceLogs\Schemas\VendorPerformanceLogForm;
use App\Filament\Resources\VendorPerformanceLogs\Tables\VendorPerformanceLogsTable;
use App\Models\VendorPerformanceLog;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VendorPerformanceLogResource extends Resource
{
    protected static ?string $model = VendorPerformanceLog::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SATICI_YONETIMI;

    protected static ?string $navigationLabel = 'Performans Logları';

    protected static ?string $modelLabel = 'Performans Kaydı';

    protected static ?string $pluralModelLabel = 'Performans Kayıtları';

    protected static ?int $navigationSort = 9;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBarSquare;

    public static function form(Schema $schema): Schema
    {
        return VendorPerformanceLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorPerformanceLogsTable::configure($table);
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
            'index' => ListVendorPerformanceLogs::route('/'),
            'create' => CreateVendorPerformanceLog::route('/create'),
            'edit' => EditVendorPerformanceLog::route('/{record}/edit'),
        ];
    }
}
