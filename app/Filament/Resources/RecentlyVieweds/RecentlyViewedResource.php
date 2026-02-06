<?php

namespace App\Filament\Resources\RecentlyVieweds;

use App\Filament\Resources\RecentlyVieweds\Pages\CreateRecentlyViewed;
use App\Filament\Resources\RecentlyVieweds\Pages\EditRecentlyViewed;
use App\Filament\Resources\RecentlyVieweds\Pages\ListRecentlyVieweds;
use App\Filament\Resources\RecentlyVieweds\Schemas\RecentlyViewedForm;
use App\Filament\Resources\RecentlyVieweds\Tables\RecentlyViewedsTable;
use App\Models\RecentlyViewed;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RecentlyViewedResource extends Resource
{
    protected static ?string $model = RecentlyViewed::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ARAMA_VE_ANALYTICS;

    protected static ?string $navigationLabel = 'Son Görüntülenen';

    protected static ?string $modelLabel = 'Son Görüntülenen';

    protected static ?string $pluralModelLabel = 'Son Görüntülenenler';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Clock;

    public static function form(Schema $schema): Schema
    {
        return RecentlyViewedForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecentlyViewedsTable::configure($table);
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
            'index' => ListRecentlyVieweds::route('/'),
            'create' => CreateRecentlyViewed::route('/create'),
            'edit' => EditRecentlyViewed::route('/{record}/edit'),
        ];
    }
}
