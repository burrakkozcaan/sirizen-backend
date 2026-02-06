<?php

namespace App\Filament\Resources\Disputes;

use App\Filament\Resources\Disputes\Pages\CreateDispute;
use App\Filament\Resources\Disputes\Pages\EditDispute;
use App\Filament\Resources\Disputes\Pages\ListDisputes;
use App\Filament\Resources\Disputes\Schemas\DisputeForm;
use App\Filament\Resources\Disputes\Tables\DisputesTable;
use App\Models\Dispute;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DisputeResource extends Resource
{
    protected static ?string $model = Dispute::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SIPARIS_YONETIMI;

    protected static ?string $navigationLabel = 'Anlaşmazlıklar';

    protected static ?string $modelLabel = 'Anlaşmazlık';

    protected static ?string $pluralModelLabel = 'Anlaşmazlıklar';

    protected static ?int $navigationSort = 7;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Scale;

    public static function form(Schema $schema): Schema
    {
        return DisputeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DisputesTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
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
            'index' => ListDisputes::route('/'),
            'create' => CreateDispute::route('/create'),
            'edit' => EditDispute::route('/{record}/edit'),
        ];
    }
}
