<?php

namespace App\Filament\Resources\Refunds;

use App\Filament\Resources\Refunds\Pages\CreateRefund;
use App\Filament\Resources\Refunds\Pages\EditRefund;
use App\Filament\Resources\Refunds\Pages\ListRefunds;
use App\Filament\Resources\Refunds\Schemas\RefundForm;
use App\Filament\Resources\Refunds\Tables\RefundsTable;
use App\Models\Refund;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RefundResource extends Resource
{
    protected static ?string $model = Refund::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SIPARIS_YONETIMI;

    protected static ?string $navigationLabel = 'İadeler';

    protected static ?string $modelLabel = 'İade';

    protected static ?string $pluralModelLabel = 'İadeler';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ReceiptRefund;

    public static function form(Schema $schema): Schema
    {
        return RefundForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RefundsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
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
            'index' => ListRefunds::route('/'),
            'create' => CreateRefund::route('/create'),
            'edit' => EditRefund::route('/{record}/edit'),
        ];
    }
}
