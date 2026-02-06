<?php

namespace App\Filament\Resources\ReturnPolicies;

use App\Filament\Resources\ReturnPolicies\Pages\CreateReturnPolicy;
use App\Filament\Resources\ReturnPolicies\Pages\EditReturnPolicy;
use App\Filament\Resources\ReturnPolicies\Pages\ListReturnPolicies;
use App\Filament\Resources\ReturnPolicies\Schemas\ReturnPolicyForm;
use App\Filament\Resources\ReturnPolicies\Tables\ReturnPoliciesTable;
use App\Models\ReturnPolicy;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ReturnPolicyResource extends Resource
{
    protected static ?string $model = ReturnPolicy::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SIPARIS_YONETIMI;

    protected static ?string $navigationLabel = 'İade Politikaları';

    protected static ?string $modelLabel = 'İade Politikası';

    protected static ?string $pluralModelLabel = 'İade Politikaları';

    protected static ?int $navigationSort = 6;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Document;

    public static function form(Schema $schema): Schema
    {
        return ReturnPolicyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReturnPoliciesTable::configure($table);
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
            'index' => ListReturnPolicies::route('/'),
            'create' => CreateReturnPolicy::route('/create'),
            'edit' => EditReturnPolicy::route('/{record}/edit'),
        ];
    }
}
