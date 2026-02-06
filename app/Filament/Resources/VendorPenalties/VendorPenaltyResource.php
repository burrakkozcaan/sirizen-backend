<?php

namespace App\Filament\Resources\VendorPenalties;

use App\Filament\Resources\VendorPenalties\Pages\CreateVendorPenalty;
use App\Filament\Resources\VendorPenalties\Pages\EditVendorPenalty;
use App\Filament\Resources\VendorPenalties\Pages\ListVendorPenalties;
use App\Filament\Resources\VendorPenalties\Schemas\VendorPenaltyForm;
use App\Filament\Resources\VendorPenalties\Tables\VendorPenaltiesTable;
use App\Models\VendorPenalty;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VendorPenaltyResource extends Resource
{
    protected static ?string $model = VendorPenalty::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SATICI_YONETIMI;

    protected static ?string $navigationLabel = 'Satıcı Cezaları';

    protected static ?string $modelLabel = 'Satıcı Cezası';

    protected static ?string $pluralModelLabel = 'Satıcı Cezaları';

    protected static ?int $navigationSort = 8;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ExclamationTriangle;

    public static function form(Schema $schema): Schema
    {
        return VendorPenaltyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorPenaltiesTable::configure($table);
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
            'index' => ListVendorPenalties::route('/'),
            'create' => CreateVendorPenalty::route('/create'),
            'edit' => EditVendorPenalty::route('/{record}/edit'),
        ];
    }
}
