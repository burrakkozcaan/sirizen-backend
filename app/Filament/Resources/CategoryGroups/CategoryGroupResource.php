<?php

namespace App\Filament\Resources\CategoryGroups;

use App\Filament\Resources\CategoryGroups\Pages\CreateCategoryGroup;
use App\Filament\Resources\CategoryGroups\Pages\EditCategoryGroup;
use App\Filament\Resources\CategoryGroups\Pages\ListCategoryGroups;
use App\Filament\Resources\CategoryGroups\Schemas\CategoryGroupForm;
use App\Filament\Resources\CategoryGroups\Tables\CategoryGroupsTable;
use App\Models\CategoryGroup;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CategoryGroupResource extends Resource
{
    protected static ?string $model = CategoryGroup::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::URUN_YONETIMI;

    protected static ?string $navigationLabel = 'Kategori Grupları';

    protected static ?string $modelLabel = 'Kategori Grubu';

    protected static ?string $pluralModelLabel = 'Kategori Grupları';

    protected static ?int $navigationSort = 6;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::FolderOpen;

    public static function form(Schema $schema): Schema
    {
        return CategoryGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoryGroupsTable::configure($table);
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
            'index' => ListCategoryGroups::route('/'),
            'create' => CreateCategoryGroup::route('/create'),
            'edit' => EditCategoryGroup::route('/{record}/edit'),
        ];
    }
}
