<?php

namespace App\Filament\Resources\Notifications;

use App\Filament\Resources\Notifications\Pages\CreateNotification;
use App\Filament\Resources\Notifications\Pages\EditNotification;
use App\Filament\Resources\Notifications\Pages\ListNotifications;
use App\Filament\Resources\Notifications\Schemas\NotificationForm;
use App\Filament\Resources\Notifications\Tables\NotificationsTable;
use App\Models\Notification;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::BILDIRIMLER;

    protected static ?string $navigationLabel = 'Bildirimler';

    protected static ?string $modelLabel = 'Bildirim';

    protected static ?string $pluralModelLabel = 'Bildirimler';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bell;

    public static function form(Schema $schema): Schema
    {
        return NotificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotificationsTable::configure($table);
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
            'index' => ListNotifications::route('/'),
            'create' => CreateNotification::route('/create'),
            'edit' => EditNotification::route('/{record}/edit'),
        ];
    }
}
