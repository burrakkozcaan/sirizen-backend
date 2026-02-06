<?php

namespace App\Filament\Resources\PaymentGatewaySettings;

use App\Filament\Resources\PaymentGatewaySettings\Pages\EditPaymentGatewaySetting;
use App\Filament\Resources\PaymentGatewaySettings\Pages\ListPaymentGatewaySettings;
use App\Filament\Resources\PaymentGatewaySettings\Schemas\PaymentGatewaySettingForm;
use App\Filament\Resources\PaymentGatewaySettings\Tables\PaymentGatewaySettingsTable;
use App\Models\PaymentGatewaySetting;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PaymentGatewaySettingResource extends Resource
{
    protected static ?string $model = PaymentGatewaySetting::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ODEME_VE_KOMISYON;

    protected static ?string $navigationLabel = 'Gateway Ayarları';

    protected static ?string $modelLabel = 'Gateway Ayarı';

    protected static ?string $pluralModelLabel = 'Gateway Ayarları';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;

    public static function form(Schema $schema): Schema
    {
        return PaymentGatewaySettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentGatewaySettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentGatewaySettings::route('/'),
            'edit' => EditPaymentGatewaySetting::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
