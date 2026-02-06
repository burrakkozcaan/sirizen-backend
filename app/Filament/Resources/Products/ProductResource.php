<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\RelationManagers\AttributesRelationManager;
use App\Filament\Resources\Products\RelationManagers\CampaignsRelationManager;
use App\Filament\Resources\Products\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductBadgesRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductBannersRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductSafetyDocumentsRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductSafetyImagesRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductSellersRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductFeaturesRelationManager;
use App\Filament\Resources\Products\RelationManagers\SimilarProductsRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductVideosRelationManager;
use App\Filament\Resources\Products\RelationManagers\VariantsRelationManager;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::URUN_YONETIMI;

    protected static ?string $navigationLabel = 'Ürünler';

    protected static ?string $modelLabel = 'Ürün';

    protected static ?string $pluralModelLabel = 'Ürünler';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cube;

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getRelations(): array
    {
        return [
            ImagesRelationManager::class,
            VariantsRelationManager::class,
            AttributesRelationManager::class,
            ProductSellersRelationManager::class,
            SimilarProductsRelationManager::class,
            ProductBannersRelationManager::class,
            ProductBadgesRelationManager::class,
            ProductFeaturesRelationManager::class,
            ProductSafetyImagesRelationManager::class,
            ProductSafetyDocumentsRelationManager::class,
            ProductVideosRelationManager::class,
            CampaignsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
