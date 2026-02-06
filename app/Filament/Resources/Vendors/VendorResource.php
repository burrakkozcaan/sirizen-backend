<?php

namespace App\Filament\Resources\Vendors;

use App\Filament\Resources\Vendors\Pages\CreateVendor;
use App\Filament\Resources\Vendors\Pages\EditVendor;
use App\Filament\Resources\Vendors\Pages\ListVendors;
use App\Filament\Resources\Vendors\RelationManagers\VendorBadgesRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\SellerPagesRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\SellerReviewsRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\VendorCampaignsRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\VendorCouponsRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\VendorFollowersRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\VendorOrderItemsRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\VendorPenaltiesRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\VendorProductQuestionsRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\VendorProductReviewsRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\VendorProductsRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\VendorReturnPoliciesRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\VendorScoresRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\VendorShippingRulesRelationManager;
use App\Filament\Resources\Vendors\Schemas\VendorForm;
use App\Filament\Resources\Vendors\Tables\VendorsTable;
use App\Models\Vendor;
use App\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::SATICI_YONETIMI;

    protected static ?string $navigationLabel = 'Satıcılar';

    protected static ?string $modelLabel = 'Satıcı';

    protected static ?string $pluralModelLabel = 'Satıcılar';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingStorefront;

    public static function form(Schema $schema): Schema
    {
        return VendorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getRelations(): array
    {
        return [
            SellerPagesRelationManager::class,
            VendorProductsRelationManager::class,
            VendorCampaignsRelationManager::class,
            VendorCouponsRelationManager::class,
            VendorOrderItemsRelationManager::class,
            VendorProductQuestionsRelationManager::class,
            VendorProductReviewsRelationManager::class,
            SellerReviewsRelationManager::class,
            VendorScoresRelationManager::class,
            VendorBadgesRelationManager::class,
            VendorPenaltiesRelationManager::class,
            VendorFollowersRelationManager::class,
            VendorShippingRulesRelationManager::class,
            VendorReturnPoliciesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVendors::route('/'),
            'create' => CreateVendor::route('/create'),
            'edit' => EditVendor::route('/{record}/edit'),
        ];
    }
}
