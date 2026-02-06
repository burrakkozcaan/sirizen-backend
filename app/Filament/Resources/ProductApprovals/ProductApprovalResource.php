<?php

namespace App\Filament\Resources\ProductApprovals;

use App\Filament\Resources\ProductApprovals\Pages\CreateProductApproval;
use App\Filament\Resources\ProductApprovals\Pages\EditProductApproval;
use App\Filament\Resources\ProductApprovals\Pages\ListProductApprovals;
use App\Filament\Resources\ProductApprovals\Schemas\ProductApprovalForm;
use App\Filament\Resources\ProductApprovals\Tables\ProductApprovalsTable;
use App\Models\ProductApproval;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProductApprovalResource extends Resource
{
    protected static ?string $model = ProductApproval::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::URUN_YONETIMI;

    protected static ?string $navigationLabel = 'Ürün Onayları';

    protected static ?string $modelLabel = 'Ürün Onayı';

    protected static ?string $pluralModelLabel = 'Ürün Onayları';

    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CheckCircle;


    public static function form(Schema $schema): Schema
    {
        return ProductApprovalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductApprovalsTable::configure($table);
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
            'index' => ListProductApprovals::route('/'),
            'create' => CreateProductApproval::route('/create'),
            'edit' => EditProductApproval::route('/{record}/edit'),
        ];
    }
}
