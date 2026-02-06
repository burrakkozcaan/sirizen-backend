<?php

namespace App\Filament\Resources\Campaigns;

use App\Filament\Resources\Campaigns\Pages\CreateCampaign;
use App\Filament\Resources\Campaigns\Pages\EditCampaign;
use App\Filament\Resources\Campaigns\Pages\ListCampaigns;
use App\Filament\Resources\Campaigns\Schemas\CampaignForm;
use App\Filament\Resources\Campaigns\Tables\CampaignsTable;
use App\Models\Campaign;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::KAMPANYA_VE_KUPONLAR;

    protected static ?string $navigationLabel = 'Kampanyalar';

    protected static ?string $modelLabel = 'Kampanya';

    protected static ?string $pluralModelLabel = 'Kampanyalar';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Megaphone;

    public static function form(Schema $schema): Schema
    {
        return CampaignForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampaignsTable::configure($table);
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
            'index' => ListCampaigns::route('/'),
            'create' => CreateCampaign::route('/create'),
            'edit' => EditCampaign::route('/{record}/edit'),
        ];
    }
}
