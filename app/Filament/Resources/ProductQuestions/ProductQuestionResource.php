<?php

namespace App\Filament\Resources\ProductQuestions;

use App\Filament\Resources\ProductQuestions\Pages\CreateProductQuestion;
use App\Filament\Resources\ProductQuestions\Pages\EditProductQuestion;
use App\Filament\Resources\ProductQuestions\Pages\ListProductQuestions;
use App\Filament\Resources\ProductQuestions\Schemas\ProductQuestionForm;
use App\Filament\Resources\ProductQuestions\Tables\ProductQuestionsTable;
use App\Models\ProductQuestion;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProductQuestionResource extends Resource
{
    protected static ?string $model = ProductQuestion::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::INCELEME_VE_SORULAR;

    protected static ?string $navigationLabel = 'Ürün Soruları';

    protected static ?string $modelLabel = 'Ürün Sorusu';

    protected static ?string $pluralModelLabel = 'Ürün Soruları';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChatBubbleLeft;

    public static function form(Schema $schema): Schema
    {
        return ProductQuestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductQuestionsTable::configure($table);
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
            'index' => ListProductQuestions::route('/'),
            'create' => CreateProductQuestion::route('/create'),
            'edit' => EditProductQuestion::route('/{record}/edit'),
        ];
    }
}
