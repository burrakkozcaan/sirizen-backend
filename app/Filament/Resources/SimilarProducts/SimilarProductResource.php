<?php

namespace App\Filament\Resources\SimilarProducts;

use App\Filament\Resources\SimilarProducts\Pages\CreateSimilarProduct;
use App\Filament\Resources\SimilarProducts\Pages\EditSimilarProduct;
use App\Filament\Resources\SimilarProducts\Pages\ListSimilarProducts;
use App\Filament\Resources\SimilarProducts\Schemas\SimilarProductForm;
use App\Filament\Resources\SimilarProducts\Tables\SimilarProductsTable;
use App\Models\SimilarProduct;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SimilarProductResource extends Resource
{
    protected static ?string $model = SimilarProduct::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::URUN_YONETIMI;

    protected static ?string $navigationLabel = 'Benzer Ürünler';

    protected static ?string $modelLabel = 'Benzer Ürün';

    protected static ?string $pluralModelLabel = 'Benzer Ürünler';

    protected static ?int $navigationSort = 13;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::MagnifyingGlass;

    public static function form(Schema $schema): Schema
    {
        return SimilarProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SimilarProductsTable::configure($table);
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
            'index' => ListSimilarProducts::route('/'),
            'create' => CreateSimilarProduct::route('/create'),
            'edit' => EditSimilarProduct::route('/{record}/edit'),
        ];
    }
}
