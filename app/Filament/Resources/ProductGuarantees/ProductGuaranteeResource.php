<?php

namespace App\Filament\Resources\ProductGuarantees;

use App\Filament\Resources\ProductGuarantees\Pages\CreateProductGuarantee;
use App\Filament\Resources\ProductGuarantees\Pages\EditProductGuarantee;
use App\Filament\Resources\ProductGuarantees\Pages\ListProductGuarantees;
use App\Filament\Resources\ProductGuarantees\Schemas\ProductGuaranteeForm;
use App\Filament\Resources\ProductGuarantees\Tables\ProductGuaranteesTable;
use App\Models\ProductGuarantee;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProductGuaranteeResource extends Resource
{
    protected static ?string $model = ProductGuarantee::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::URUN_YONETIMI;

    protected static ?string $navigationLabel = 'Ürün Garantileri';

    protected static ?string $modelLabel = 'Ürün Garantisi';

    protected static ?string $pluralModelLabel = 'Ürün Garantileri';

    protected static ?int $navigationSort = 12;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShieldCheck;

    public static function form(Schema $schema): Schema
    {
        return ProductGuaranteeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductGuaranteesTable::configure($table);
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
            'index' => ListProductGuarantees::route('/'),
            'create' => CreateProductGuarantee::route('/create'),
            'edit' => EditProductGuarantee::route('/{record}/edit'),
        ];
    }
}
