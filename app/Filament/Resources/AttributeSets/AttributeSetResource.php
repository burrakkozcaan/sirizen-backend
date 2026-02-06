<?php

namespace App\Filament\Resources\AttributeSets;

use App\Filament\Resources\AttributeSets\Pages\CreateAttributeSet;
use App\Filament\Resources\AttributeSets\Pages\EditAttributeSet;
use App\Filament\Resources\AttributeSets\Pages\ListAttributeSets;
use App\Filament\Resources\AttributeSets\Schemas\AttributeSetForm;
use App\Filament\Resources\AttributeSets\Tables\AttributeSetsTable;
use App\Models\AttributeSet;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AttributeSetResource extends Resource
{
    protected static ?string $model = AttributeSet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::QueueList;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::KATALOG_YONETIMI;

    protected static ?string $navigationLabel = 'Özellik Setleri';

    protected static ?string $modelLabel = 'Özellik Seti';

    protected static ?string $pluralModelLabel = 'Özellik Setleri';

    public static function form(Schema $schema): Schema
    {
        return AttributeSetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttributeSetsTable::configure($table);
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
            'index' => ListAttributeSets::route('/'),
            'create' => CreateAttributeSet::route('/create'),
            'edit' => EditAttributeSet::route('/{record}/edit'),
        ];
    }
}
