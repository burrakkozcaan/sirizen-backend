<?php

namespace App\Filament\Resources\Addresses;

use App\Filament\Resources\Addresses\Pages\CreateAddress;
use App\Filament\Resources\Addresses\Pages\EditAddress;
use App\Filament\Resources\Addresses\Pages\ListAddresses;
use App\Filament\Resources\Addresses\Schemas\AddressForm;
use App\Filament\Resources\Addresses\Tables\AddressesTable;
use App\Models\Address;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AddressResource extends Resource
{
    protected static ?string $model = Address::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::MUSTERI_YONETIMI;

    protected static ?string $navigationLabel = 'Adresler';

    protected static ?string $modelLabel = 'Adres';

    protected static ?string $pluralModelLabel = 'Adresler';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::MapPin;

    public static function form(Schema $schema): Schema
    {
        return AddressForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AddressesTable::configure($table);
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
            'index' => ListAddresses::route('/'),
            'create' => CreateAddress::route('/create'),
            'edit' => EditAddress::route('/{record}/edit'),
        ];
    }
}
