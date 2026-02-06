<?php

namespace App\Filament\Resources\VendorPayouts;

use App\Filament\Resources\VendorPayouts\Pages\CreateVendorPayout;
use App\Filament\Resources\VendorPayouts\Pages\EditVendorPayout;
use App\Filament\Resources\VendorPayouts\Pages\ListVendorPayouts;
use App\Filament\Resources\VendorPayouts\Schemas\VendorPayoutForm;
use App\Filament\Resources\VendorPayouts\Tables\VendorPayoutsTable;
use App\Models\VendorPayout;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VendorPayoutResource extends Resource
{
    protected static ?string $model = VendorPayout::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SATICI_YONETIMI;

    protected static ?string $navigationLabel = 'Ödemeler';

    protected static ?string $modelLabel = 'Satıcı Ödemesi';

    protected static ?string $pluralModelLabel = 'Satıcı Ödemeleri';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Banknotes;

    public static function form(Schema $schema): Schema
    {
        return VendorPayoutForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorPayoutsTable::configure($table);
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
            'index' => ListVendorPayouts::route('/'),
            'create' => CreateVendorPayout::route('/create'),
            'edit' => EditVendorPayout::route('/{record}/edit'),
        ];
    }
}
