<?php

namespace App\Filament\Resources\VendorTiers;

use App\Filament\Resources\VendorTiers\Pages\CreateVendorTier;
use App\Filament\Resources\VendorTiers\Pages\EditVendorTier;
use App\Filament\Resources\VendorTiers\Pages\ListVendorTiers;
use App\Filament\Resources\VendorTiers\Schemas\VendorTierForm;
use App\Filament\Resources\VendorTiers\Tables\VendorTiersTable;
use App\Models\VendorTier;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VendorTierResource extends Resource
{
    protected static ?string $model = VendorTier::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SATICI_YONETIMI;

    protected static ?string $navigationLabel = 'Seviyeler';

    protected static ?string $modelLabel = 'Satıcı Seviyesi';

    protected static ?string $pluralModelLabel = 'Satıcı Seviyeleri';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Trophy;

    public static function form(Schema $schema): Schema
    {
        return VendorTierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorTiersTable::configure($table);
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
            'index' => ListVendorTiers::route('/'),
            'create' => CreateVendorTier::route('/create'),
            'edit' => EditVendorTier::route('/{record}/edit'),
        ];
    }
}
