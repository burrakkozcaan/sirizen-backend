<?php

namespace App\Filament\Resources\ProductImportLogs;

use App\Filament\Resources\ProductImportLogs\Pages\CreateProductImportLog;
use App\Filament\Resources\ProductImportLogs\Pages\EditProductImportLog;
use App\Filament\Resources\ProductImportLogs\Pages\ListProductImportLogs;
use App\Filament\Resources\ProductImportLogs\Schemas\ProductImportLogForm;
use App\Filament\Resources\ProductImportLogs\Tables\ProductImportLogsTable;
use App\Models\ProductImportLog;
use App\NavigationGroup;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductImportLogResource extends Resource
{
    protected static ?string $model = ProductImportLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowDownTray;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::URUN_YONETIMI;

    protected static ?string $navigationLabel = 'Ürün İçe Aktarma Logları';

    protected static ?string $modelLabel = 'İçe Aktarma Kaydı';

    protected static ?string $pluralModelLabel = 'İçe Aktarma Kayıtları';

    protected static ?int $navigationSort = 14;

    public static function form(Schema $schema): Schema
    {
        return ProductImportLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductImportLogsTable::configure($table);
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
            'index' => ListProductImportLogs::route('/'),
            'create' => CreateProductImportLog::route('/create'),
            'edit' => EditProductImportLog::route('/{record}/edit'),
        ];
    }
}
