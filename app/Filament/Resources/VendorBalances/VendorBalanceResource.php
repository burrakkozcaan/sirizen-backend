<?php

namespace App\Filament\Resources\VendorBalances;

use App\Filament\Resources\VendorBalances\Pages\CreateVendorBalance;
use App\Filament\Resources\VendorBalances\Pages\EditVendorBalance;
use App\Filament\Resources\VendorBalances\Pages\ListVendorBalances;
use App\Filament\Resources\VendorBalances\Schemas\VendorBalanceForm;
use App\Filament\Resources\VendorBalances\Tables\VendorBalancesTable;
use App\Models\VendorBalance;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VendorBalanceResource extends Resource
{
    protected static ?string $model = VendorBalance::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SATICI_YONETIMI;

    protected static ?string $navigationLabel = 'Bakiyeler';

    protected static ?string $modelLabel = 'Satıcı Bakiyesi';

    protected static ?string $pluralModelLabel = 'Satıcı Bakiyeleri';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Wallet;

    public static function form(Schema $schema): Schema
    {
        return VendorBalanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorBalancesTable::configure($table);
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
            'index' => ListVendorBalances::route('/'),
            'create' => CreateVendorBalance::route('/create'),
            'edit' => EditVendorBalance::route('/{record}/edit'),
        ];
    }
}
