<?php

namespace App\Filament\Resources\VendorScores;

use App\Filament\Resources\VendorScores\Pages\CreateVendorScore;
use App\Filament\Resources\VendorScores\Pages\EditVendorScore;
use App\Filament\Resources\VendorScores\Pages\ListVendorScores;
use App\Filament\Resources\VendorScores\Schemas\VendorScoreForm;
use App\Filament\Resources\VendorScores\Tables\VendorScoresTable;
use App\Models\VendorScore;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VendorScoreResource extends Resource
{
    protected static ?string $model = VendorScore::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SATICI_YONETIMI;

    protected static ?string $navigationLabel = 'Puanlar';

    protected static ?string $modelLabel = 'Satıcı Puanı';

    protected static ?string $pluralModelLabel = 'Satıcı Puanları';

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Star;

    public static function form(Schema $schema): Schema
    {
        return VendorScoreForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorScoresTable::configure($table);
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
            'index' => ListVendorScores::route('/'),
            'create' => CreateVendorScore::route('/create'),
            'edit' => EditVendorScore::route('/{record}/edit'),
        ];
    }
}
