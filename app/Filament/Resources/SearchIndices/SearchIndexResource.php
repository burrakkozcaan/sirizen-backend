<?php

namespace App\Filament\Resources\SearchIndices;

use App\Filament\Resources\SearchIndices\Pages\CreateSearchIndex;
use App\Filament\Resources\SearchIndices\Pages\EditSearchIndex;
use App\Filament\Resources\SearchIndices\Pages\ListSearchIndices;
use App\Filament\Resources\SearchIndices\Schemas\SearchIndexForm;
use App\Filament\Resources\SearchIndices\Tables\SearchIndicesTable;
use App\Models\SearchIndex;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SearchIndexResource extends Resource
{
    protected static ?string $model = SearchIndex::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ARAMA_VE_ANALYTICS;

    protected static ?string $navigationLabel = 'Arama İndeksleri';

    protected static ?string $modelLabel = 'Arama İndeksi';

    protected static ?string $pluralModelLabel = 'Arama İndeksleri';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::QueueList;

    public static function form(Schema $schema): Schema
    {
        return SearchIndexForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SearchIndicesTable::configure($table);
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
            'index' => ListSearchIndices::route('/'),
            'create' => CreateSearchIndex::route('/create'),
            'edit' => EditSearchIndex::route('/{record}/edit'),
        ];
    }
}
