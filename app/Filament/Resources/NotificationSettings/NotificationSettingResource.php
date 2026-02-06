<?php

namespace App\Filament\Resources\NotificationSettings;

use App\Filament\Resources\NotificationSettings\Pages\CreateNotificationSetting;
use App\Filament\Resources\NotificationSettings\Pages\EditNotificationSetting;
use App\Filament\Resources\NotificationSettings\Pages\ListNotificationSettings;
use App\Filament\Resources\NotificationSettings\Schemas\NotificationSettingForm;
use App\Filament\Resources\NotificationSettings\Tables\NotificationSettingsTable;
use App\Models\NotificationPreference;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NotificationSettingResource extends Resource
{
    protected static ?string $model = NotificationPreference::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::BILDIRIMLER;

    protected static ?string $navigationLabel = 'Bildirim Tercihleri';

    protected static ?string $modelLabel = 'Bildirim Tercihi';

    protected static ?string $pluralModelLabel = 'Bildirim Tercihleri';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog;

    public static function form(Schema $schema): Schema
    {
        return NotificationSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotificationSettingsTable::configure($table);
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
            'index' => ListNotificationSettings::route('/'),
            'create' => CreateNotificationSetting::route('/create'),
            'edit' => EditNotificationSetting::route('/{record}/edit'),
        ];
    }
}
