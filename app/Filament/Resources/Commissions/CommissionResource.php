<?php

namespace App\Filament\Resources\Commissions;

use App\Filament\Resources\Commissions\Pages\CreateCommission;
use App\Filament\Resources\Commissions\Pages\EditCommission;
use App\Filament\Resources\Commissions\Pages\ListCommissions;
use App\Filament\Resources\Commissions\Schemas\CommissionForm;
use App\Filament\Resources\Commissions\Tables\CommissionsTable;
use App\Models\Commission;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CommissionResource extends Resource
{
    protected static ?string $model = Commission::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::ODEME_VE_KOMISYON;

    protected static ?string $navigationLabel = 'Komisyonlar';

    protected static ?string $modelLabel = 'Komisyon';

    protected static ?string $pluralModelLabel = 'Komisyonlar';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ReceiptPercent;

    public static function form(Schema $schema): Schema
    {
        return CommissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommissionsTable::configure($table);
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
            'index' => ListCommissions::route('/'),
            'create' => CreateCommission::route('/create'),
            'edit' => EditCommission::route('/{record}/edit'),
        ];
    }
}
