<?php

namespace App\Filament\Resources\ProductFaqs;

use App\Filament\Resources\ProductFaqs\Pages\CreateProductFaq;
use App\Filament\Resources\ProductFaqs\Pages\EditProductFaq;
use App\Filament\Resources\ProductFaqs\Pages\ListProductFaqs;
use App\Filament\Resources\ProductFaqs\Schemas\ProductFaqForm;
use App\Filament\Resources\ProductFaqs\Tables\ProductFaqsTable;
use App\Models\ProductFaq;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProductFaqResource extends Resource
{
    protected static ?string $model = ProductFaq::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::INCELEME_VE_SORULAR;

    protected static ?string $navigationLabel = 'Ürün SSS';

    protected static ?string $modelLabel = 'Ürün SSS';

    protected static ?string $pluralModelLabel = 'Ürün SSS';

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChatBubbleLeft;

    public static function form(Schema $schema): Schema
    {
        return ProductFaqForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductFaqsTable::configure($table);
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
            'index' => ListProductFaqs::route('/'),
            'create' => CreateProductFaq::route('/create'),
            'edit' => EditProductFaq::route('/{record}/edit'),
        ];
    }
}
