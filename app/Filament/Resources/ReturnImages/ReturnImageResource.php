<?php

namespace App\Filament\Resources\ReturnImages;

use App\Filament\Resources\ReturnImages\Pages\CreateReturnImage;
use App\Filament\Resources\ReturnImages\Pages\EditReturnImage;
use App\Filament\Resources\ReturnImages\Pages\ListReturnImages;
use App\Filament\Resources\ReturnImages\Schemas\ReturnImageForm;
use App\Filament\Resources\ReturnImages\Tables\ReturnImagesTable;
use App\Models\ReturnImage;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ReturnImageResource extends Resource
{
    protected static ?string $model = ReturnImage::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SIPARIS_YONETIMI;

    protected static ?string $navigationLabel = 'İade Görselleri';

    protected static ?string $modelLabel = 'İade Görseli';

    protected static ?string $pluralModelLabel = 'İade Görselleri';

    protected static ?int $navigationSort = 6;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Photo;

    public static function form(Schema $schema): Schema
    {
        return ReturnImageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReturnImagesTable::configure($table);
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
            'index' => ListReturnImages::route('/'),
            'create' => CreateReturnImage::route('/create'),
            'edit' => EditReturnImage::route('/{record}/edit'),
        ];
    }
}
