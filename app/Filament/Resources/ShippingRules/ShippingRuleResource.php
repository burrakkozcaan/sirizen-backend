<?php

namespace App\Filament\Resources\ShippingRules;

use App\Filament\Resources\ShippingRules\Pages\CreateShippingRule;
use App\Filament\Resources\ShippingRules\Pages\EditShippingRule;
use App\Filament\Resources\ShippingRules\Pages\ListShippingRules;
use App\Filament\Resources\ShippingRules\Schemas\ShippingRuleForm;
use App\Filament\Resources\ShippingRules\Tables\ShippingRulesTable;
use App\Models\ShippingRule;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ShippingRuleResource extends Resource
{
    protected static ?string $model = ShippingRule::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SIPARIS_YONETIMI;

    protected static ?string $navigationLabel = 'Kargo Kuralları';

    protected static ?string $modelLabel = 'Kargo Kuralı';

    protected static ?string $pluralModelLabel = 'Kargo Kuralları';

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Truck;

    public static function form(Schema $schema): Schema
    {
        return ShippingRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShippingRulesTable::configure($table);
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
            'index' => ListShippingRules::route('/'),
            'create' => CreateShippingRule::route('/create'),
            'edit' => EditShippingRule::route('/{record}/edit'),
        ];
    }
}
