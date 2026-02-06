<?php

namespace App\Filament\Resources\DataDeletionRequests;

use App\Filament\Resources\DataDeletionRequests\Pages\CreateDataDeletionRequest;
use App\Filament\Resources\DataDeletionRequests\Pages\EditDataDeletionRequest;
use App\Filament\Resources\DataDeletionRequests\Pages\ListDataDeletionRequests;
use App\Filament\Resources\DataDeletionRequests\Schemas\DataDeletionRequestForm;
use App\Filament\Resources\DataDeletionRequests\Tables\DataDeletionRequestsTable;
use App\Models\DataDeletionRequest;
use App\NavigationGroup;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DataDeletionRequestResource extends Resource
{
    protected static ?string $model = DataDeletionRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Trash;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::KVKK_VE_UYUMLULUK;

    protected static ?string $navigationLabel = 'Veri Silme İstekleri';

    protected static ?string $modelLabel = 'Veri Silme Talebi';

    protected static ?string $pluralModelLabel = 'Veri Silme Talepleri';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return DataDeletionRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DataDeletionRequestsTable::configure($table);
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
            'index' => ListDataDeletionRequests::route('/'),
            'create' => CreateDataDeletionRequest::route('/create'),
            'edit' => EditDataDeletionRequest::route('/{record}/edit'),
        ];
    }
}
