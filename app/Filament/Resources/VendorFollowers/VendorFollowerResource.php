<?php

namespace App\Filament\Resources\VendorFollowers;

use App\Filament\Resources\VendorFollowers\Pages\CreateVendorFollower;
use App\Filament\Resources\VendorFollowers\Pages\EditVendorFollower;
use App\Filament\Resources\VendorFollowers\Pages\ListVendorFollowers;
use App\Filament\Resources\VendorFollowers\Schemas\VendorFollowerForm;
use App\Filament\Resources\VendorFollowers\Tables\VendorFollowersTable;
use App\Models\VendorFollower;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VendorFollowerResource extends Resource
{
    protected static ?string $model = VendorFollower::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SATICI_YONETIMI;

    protected static ?string $navigationLabel = 'Takipçiler';

    protected static ?string $modelLabel = 'Satıcı Takipçisi';

    protected static ?string $pluralModelLabel = 'Satıcı Takipçileri';

    protected static ?int $navigationSort = 6;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    public static function form(Schema $schema): Schema
    {
        return VendorFollowerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorFollowersTable::configure($table);
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
            'index' => ListVendorFollowers::route('/'),
            'create' => CreateVendorFollower::route('/create'),
            'edit' => EditVendorFollower::route('/{record}/edit'),
        ];
    }
}
