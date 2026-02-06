<?php

namespace App\Filament\Resources\PdpLayouts;

use App\Filament\Resources\PdpLayouts\Pages\CreatePdpLayout;
use App\Filament\Resources\PdpLayouts\Pages\EditPdpLayout;
use App\Filament\Resources\PdpLayouts\Pages\ListPdpLayouts;
use App\Filament\Resources\PdpLayouts\Schemas\PdpLayoutForm;
use App\Filament\Resources\PdpLayouts\Tables\PdpLayoutsTable;
use App\Models\PdpLayout;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PdpLayoutResource extends Resource
{
    protected static ?string $model = PdpLayout::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::URUN_YONETIMI;

    protected static ?string $navigationLabel = 'PDP Düzenleri';

    protected static ?string $modelLabel = 'PDP Düzeni';

    protected static ?string $pluralModelLabel = 'PDP Düzenleri';

    protected static ?int $navigationSort = 8;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::RectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PdpLayoutForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PdpLayoutsTable::configure($table);
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
            'index' => ListPdpLayouts::route('/'),
            'create' => CreatePdpLayout::route('/create'),
            'edit' => EditPdpLayout::route('/{record}/edit'),
        ];
    }
}
