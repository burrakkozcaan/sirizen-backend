<?php

namespace App\Filament\Resources\CargoIntegrations;

use App\Filament\Resources\CargoIntegrations\Pages\CreateCargoIntegration;
use App\Filament\Resources\CargoIntegrations\Pages\EditCargoIntegration;
use App\Filament\Resources\CargoIntegrations\Pages\ListCargoIntegrations;
use App\Filament\Resources\CargoIntegrations\Schemas\CargoIntegrationForm;
use App\Filament\Resources\CargoIntegrations\Tables\CargoIntegrationsTable;
use App\Models\CargoIntegration;
use App\NavigationGroup;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CargoIntegrationResource extends Resource
{
    protected static ?string $model = CargoIntegration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PuzzlePiece;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::KARGO_VE_LOJISTIK;

    protected static ?string $navigationLabel = 'Kargo Entegrasyonları';

    protected static ?string $modelLabel = 'Kargo Entegrasyonu';

    protected static ?string $pluralModelLabel = 'Kargo Entegrasyonları';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CargoIntegrationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CargoIntegrationsTable::configure($table);
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
            'index' => ListCargoIntegrations::route('/'),
            'create' => CreateCargoIntegration::route('/create'),
            'edit' => EditCargoIntegration::route('/{record}/edit'),
        ];
    }
}
