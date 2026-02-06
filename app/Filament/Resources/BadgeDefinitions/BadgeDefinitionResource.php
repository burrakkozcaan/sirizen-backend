<?php

namespace App\Filament\Resources\BadgeDefinitions;

use App\Filament\Resources\BadgeDefinitions\Pages\CreateBadgeDefinition;
use App\Filament\Resources\BadgeDefinitions\Pages\EditBadgeDefinition;
use App\Filament\Resources\BadgeDefinitions\Pages\ListBadgeDefinitions;
use App\Filament\Resources\BadgeDefinitions\Schemas\BadgeDefinitionForm;
use App\Filament\Resources\BadgeDefinitions\Tables\BadgeDefinitionsTable;
use App\Models\BadgeDefinition;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BadgeDefinitionResource extends Resource
{
    protected static ?string $model = BadgeDefinition::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::URUN_YONETIMI;

    protected static ?string $navigationLabel = 'Rozet Tanımları';

    protected static ?string $modelLabel = 'Rozet Tanımı';

    protected static ?string $pluralModelLabel = 'Rozet Tanımları';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CheckCircle;

    public static function form(Schema $schema): Schema
    {
        return BadgeDefinitionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BadgeDefinitionsTable::configure($table);
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
            'index' => ListBadgeDefinitions::route('/'),
            'create' => CreateBadgeDefinition::route('/create'),
            'edit' => EditBadgeDefinition::route('/{record}/edit'),
        ];
    }
}
