<?php

namespace App\Filament\Resources\SellerPages;

use App\Filament\Resources\SellerPages\Pages\CreateSellerPage;
use App\Filament\Resources\SellerPages\Pages\EditSellerPage;
use App\Filament\Resources\SellerPages\Pages\ListSellerPages;
use App\Filament\Resources\SellerPages\Schemas\SellerPageForm;
use App\Filament\Resources\SellerPages\Tables\SellerPagesTable;
use App\Models\SellerPage;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SellerPageResource extends Resource
{
    protected static ?string $model = SellerPage::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ICERIK_YONETIMI;

    protected static ?string $navigationLabel = 'Satıcı Sayfaları';

    protected static ?string $modelLabel = 'Satıcı Sayfası';

    protected static ?string $pluralModelLabel = 'Satıcı Sayfaları';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    public static function form(Schema $schema): Schema
    {
        return SellerPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SellerPagesTable::configure($table);
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
            'index' => ListSellerPages::route('/'),
            'create' => CreateSellerPage::route('/create'),
            'edit' => EditSellerPage::route('/{record}/edit'),
        ];
    }
}
