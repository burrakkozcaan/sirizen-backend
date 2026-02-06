<?php

namespace App\Filament\Resources\SearchLogs;

use App\Filament\Resources\SearchLogs\Pages\CreateSearchLog;
use App\Filament\Resources\SearchLogs\Pages\EditSearchLog;
use App\Filament\Resources\SearchLogs\Pages\ListSearchLogs;
use App\Filament\Resources\SearchLogs\Schemas\SearchLogForm;
use App\Filament\Resources\SearchLogs\Tables\SearchLogsTable;
use App\Models\SearchLog;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SearchLogResource extends Resource
{
    protected static ?string $model = SearchLog::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ARAMA_VE_ANALYTICS;

    protected static ?string $navigationLabel = 'Arama Logları';

    protected static ?string $modelLabel = 'Arama Kaydı';

    protected static ?string $pluralModelLabel = 'Arama Kayıtları';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentMagnifyingGlass;

    public static function form(Schema $schema): Schema
    {
        return SearchLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SearchLogsTable::configure($table);
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
            'index' => ListSearchLogs::route('/'),
            'create' => CreateSearchLog::route('/create'),
            'edit' => EditSearchLog::route('/{record}/edit'),
        ];
    }
}
