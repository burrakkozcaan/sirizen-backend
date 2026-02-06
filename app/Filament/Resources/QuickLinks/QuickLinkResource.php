<?php

namespace App\Filament\Resources\QuickLinks;

use App\Filament\Resources\QuickLinks\Pages\CreateQuickLink;
use App\Filament\Resources\QuickLinks\Pages\EditQuickLink;
use App\Filament\Resources\QuickLinks\Pages\ListQuickLinks;
use App\Filament\Resources\QuickLinks\Schemas\QuickLinkForm;
use App\Filament\Resources\QuickLinks\Tables\QuickLinksTable;
use App\Models\QuickLink;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class QuickLinkResource extends Resource
{
    protected static ?string $model = QuickLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Link;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::URUN_YONETIMI;

    protected static ?string $navigationLabel = 'Hızlı Linkler';

    protected static ?string $modelLabel = 'Hızlı Link';

    protected static ?string $pluralModelLabel = 'Hızlı Linkler';

    public static function form(Schema $schema): Schema
    {
        return QuickLinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuickLinksTable::configure($table);
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
            'index' => ListQuickLinks::route('/'),
            'create' => CreateQuickLink::route('/create'),
            'edit' => EditQuickLink::route('/{record}/edit'),
        ];
    }
}
