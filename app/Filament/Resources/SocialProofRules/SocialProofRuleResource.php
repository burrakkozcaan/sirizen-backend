<?php

namespace App\Filament\Resources\SocialProofRules;

use App\Filament\Resources\SocialProofRules\Pages\CreateSocialProofRule;
use App\Filament\Resources\SocialProofRules\Pages\EditSocialProofRule;
use App\Filament\Resources\SocialProofRules\Pages\ListSocialProofRules;
use App\Filament\Resources\SocialProofRules\Schemas\SocialProofRuleForm;
use App\Filament\Resources\SocialProofRules\Tables\SocialProofRulesTable;
use App\Models\SocialProofRule;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SocialProofRuleResource extends Resource
{
    protected static ?string $model = SocialProofRule::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::URUN_YONETIMI;

    protected static ?string $navigationLabel = 'Sosyal Kanıt Kuralları';

    protected static ?string $modelLabel = 'Sosyal Kanıt Kuralı';

    protected static ?string $pluralModelLabel = 'Sosyal Kanıt Kuralları';

    protected static ?int $navigationSort = 9;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    public static function form(Schema $schema): Schema
    {
        return SocialProofRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SocialProofRulesTable::configure($table);
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
            'index' => ListSocialProofRules::route('/'),
            'create' => CreateSocialProofRule::route('/create'),
            'edit' => EditSocialProofRule::route('/{record}/edit'),
        ];
    }
}
