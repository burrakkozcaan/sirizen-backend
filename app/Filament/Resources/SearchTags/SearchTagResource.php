<?php

namespace App\Filament\Resources\SearchTags;

use App\Filament\Resources\SearchTags\Pages\CreateSearchTag;
use App\Filament\Resources\SearchTags\Pages\EditSearchTag;
use App\Filament\Resources\SearchTags\Pages\ListSearchTags;
use App\Filament\Resources\SearchTags\Schemas\SearchTagForm;
use App\Filament\Resources\SearchTags\Tables\SearchTagsTable;
use App\Models\SearchTag;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SearchTagResource extends Resource
{
    protected static ?string $model = SearchTag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ICERIK_YONETIMI;

    protected static ?string $navigationLabel = 'Arama Etiketleri';

    protected static ?string $modelLabel = 'Arama Etiketi';

    protected static ?string $pluralModelLabel = 'Arama Etiketleri';

    public static function form(Schema $schema): Schema
    {
        return SearchTagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SearchTagsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSearchTags::route('/'),
            'create' => CreateSearchTag::route('/create'),
            'edit' => EditSearchTag::route('/{record}/edit'),
        ];
    }
}
