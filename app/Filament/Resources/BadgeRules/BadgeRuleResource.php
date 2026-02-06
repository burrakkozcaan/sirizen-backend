<?php

namespace App\Filament\Resources\BadgeRules;

use App\Filament\Resources\BadgeRules\Pages\CreateBadgeRule;
use App\Filament\Resources\BadgeRules\Pages\EditBadgeRule;
use App\Filament\Resources\BadgeRules\Pages\ListBadgeRules;
use App\Filament\Resources\BadgeRules\Schemas\BadgeRuleForm;
use App\Filament\Resources\BadgeRules\Tables\BadgeRulesTable;
use App\Models\BadgeRule;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BadgeRuleResource extends Resource
{
    protected static ?string $model = BadgeRule::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::URUN_YONETIMI;

    protected static ?string $navigationLabel = 'Rozet Kuralları';

    protected static ?string $modelLabel = 'Rozet Kuralı';

    protected static ?string $pluralModelLabel = 'Rozet Kuralları';

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AdjustmentsHorizontal;

    public static function form(Schema $schema): Schema
    {
        return BadgeRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BadgeRulesTable::configure($table);
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
            'index' => ListBadgeRules::route('/'),
            'create' => CreateBadgeRule::route('/create'),
            'edit' => EditBadgeRule::route('/{record}/edit'),
        ];
    }
}
