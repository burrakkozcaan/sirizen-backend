<?php

namespace App\Filament\Resources\VendorDocuments;

use App\Filament\Resources\VendorDocuments\Pages\CreateVendorDocument;
use App\Filament\Resources\VendorDocuments\Pages\EditVendorDocument;
use App\Filament\Resources\VendorDocuments\Pages\ListVendorDocuments;
use App\Filament\Resources\VendorDocuments\Schemas\VendorDocumentForm;
use App\Filament\Resources\VendorDocuments\Tables\VendorDocumentsTable;
use App\Models\VendorDocument;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VendorDocumentResource extends Resource
{
    protected static ?string $model = VendorDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentDuplicate;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SATICI_YONETIMI;

    protected static ?string $navigationLabel = 'Satıcı Belgeleri';

    protected static ?string $modelLabel = 'Satıcı Belgesi';

    protected static ?string $pluralModelLabel = 'Satıcı Belgeleri';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return VendorDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorDocumentsTable::configure($table);
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
            'index' => ListVendorDocuments::route('/'),
            'create' => CreateVendorDocument::route('/create'),
            'edit' => EditVendorDocument::route('/{record}/edit'),
        ];
    }
}
