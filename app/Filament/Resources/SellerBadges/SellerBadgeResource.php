<?php

namespace App\Filament\Resources\SellerBadges;

use App\Filament\Resources\SellerBadges\Pages\CreateSellerBadge;
use App\Filament\Resources\SellerBadges\Pages\EditSellerBadge;
use App\Filament\Resources\SellerBadges\Pages\ListSellerBadges;
use App\Filament\Resources\SellerBadges\RelationManagers\VendorsRelationManager;
use App\Filament\Resources\SellerBadges\Schemas\SellerBadgeForm;
use App\Filament\Resources\SellerBadges\Tables\SellerBadgesTable;
use App\Models\SellerBadge;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SellerBadgeResource extends Resource
{
    protected static ?string $model = SellerBadge::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SATICI_YONETIMI;

    protected static ?string $navigationLabel = 'Satıcı Rozetleri';

    protected static ?string $modelLabel = 'Satıcı Rozeti';

    protected static ?string $pluralModelLabel = 'Satıcı Rozetleri';

    public static function form(Schema $schema): Schema
    {
        return SellerBadgeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SellerBadgesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            VendorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSellerBadges::route('/'),
            'create' => CreateSellerBadge::route('/create'),
            'edit' => EditSellerBadge::route('/{record}/edit'),
        ];
    }
}
