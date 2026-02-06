<?php

namespace App\Filament\Resources\UserConsents;

use App\Filament\Resources\UserConsents\Pages\CreateUserConsent;
use App\Filament\Resources\UserConsents\Pages\EditUserConsent;
use App\Filament\Resources\UserConsents\Pages\ListUserConsents;
use App\Filament\Resources\UserConsents\Schemas\UserConsentForm;
use App\Filament\Resources\UserConsents\Tables\UserConsentsTable;
use App\Models\UserConsent;
use App\NavigationGroup;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserConsentResource extends Resource
{
    protected static ?string $model = UserConsent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::KVKK_VE_UYUMLULUK;

    protected static ?string $navigationLabel = 'Kullanıcı Onayları';

    protected static ?string $modelLabel = 'Kullanıcı Onayı';

    protected static ?string $pluralModelLabel = 'Kullanıcı Onayları';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return UserConsentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserConsentsTable::configure($table);
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
            'index' => ListUserConsents::route('/'),
            'create' => CreateUserConsent::route('/create'),
            'edit' => EditUserConsent::route('/{record}/edit'),
        ];
    }
}
