<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\CreateActivityLog;
use App\Filament\Resources\ActivityLogs\Pages\EditActivityLog;
use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogs\Schemas\ActivityLogForm;
use App\Filament\Resources\ActivityLogs\Tables\ActivityLogsTable;
use App\Models\ActivityLog;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ARAMA_VE_ANALYTICS;

    protected static ?string $navigationLabel = 'Aktivite Logları';

    protected static ?string $modelLabel = 'Aktivite Kaydı';

    protected static ?string $pluralModelLabel = 'Aktivite Kayıtları';

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    public static function form(Schema $schema): Schema
    {
        return ActivityLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivityLogsTable::configure($table);
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
            'index' => ListActivityLogs::route('/'),
            'create' => CreateActivityLog::route('/create'),
            'edit' => EditActivityLog::route('/{record}/edit'),
        ];
    }
}
