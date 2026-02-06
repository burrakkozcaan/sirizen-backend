<?php

namespace App\Filament\Resources\FilterConfigs;

use App\Filament\Resources\FilterConfigs\Pages\CreateFilterConfig;
use App\Filament\Resources\FilterConfigs\Pages\EditFilterConfig;
use App\Filament\Resources\FilterConfigs\Pages\ListFilterConfigs;
use App\Filament\Resources\FilterConfigs\Schemas\FilterConfigForm;
use App\Filament\Resources\FilterConfigs\Tables\FilterConfigsTable;
use App\Models\FilterConfig;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FilterConfigResource extends Resource
{
    protected static ?string $model = FilterConfig::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::URUN_YONETIMI;

    protected static ?string $navigationLabel = 'Filtre Yapılandırmaları';

    protected static ?string $modelLabel = 'Filtre Yapılandırması';

    protected static ?string $pluralModelLabel = 'Filtre Yapılandırmaları';

    protected static ?int $navigationSort = 7;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AdjustmentsHorizontal;

    public static function form(Schema $schema): Schema
    {
        return FilterConfigForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FilterConfigsTable::configure($table);
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
            'index' => ListFilterConfigs::route('/'),
            'create' => CreateFilterConfig::route('/create'),
            'edit' => EditFilterConfig::route('/{record}/edit'),
        ];
    }
}
