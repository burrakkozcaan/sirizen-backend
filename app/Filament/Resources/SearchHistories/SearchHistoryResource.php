<?php

namespace App\Filament\Resources\SearchHistories;

use App\Filament\Resources\SearchHistories\Pages\CreateSearchHistory;
use App\Filament\Resources\SearchHistories\Pages\EditSearchHistory;
use App\Filament\Resources\SearchHistories\Pages\ListSearchHistories;
use App\Filament\Resources\SearchHistories\Schemas\SearchHistoryForm;
use App\Filament\Resources\SearchHistories\Tables\SearchHistoriesTable;
use App\Models\SearchHistory;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SearchHistoryResource extends Resource
{
    protected static ?string $model = SearchHistory::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ARAMA_VE_ANALYTICS;

    protected static ?string $navigationLabel = 'Arama Geçmişi';

    protected static ?string $modelLabel = 'Arama Geçmişi';

    protected static ?string $pluralModelLabel = 'Arama Geçmişleri';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::MagnifyingGlass;

    public static function form(Schema $schema): Schema
    {
        return SearchHistoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SearchHistoriesTable::configure($table);
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
            'index' => ListSearchHistories::route('/'),
            'create' => CreateSearchHistory::route('/create'),
            'edit' => EditSearchHistory::route('/{record}/edit'),
        ];
    }
}
