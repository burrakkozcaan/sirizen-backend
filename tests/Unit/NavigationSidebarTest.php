<?php

use App\Filament\Resources\PriceHistories\PriceHistoryResource;
use App\Filament\Resources\ProductBundles\ProductBundleResource;
use App\Filament\Resources\ProductFaqs\ProductFaqResource;
use App\Filament\Resources\ProductGuarantees\ProductGuaranteeResource;
use App\Filament\Resources\ProductLiveStats\ProductLiveStatResource;
use App\Filament\Resources\ReturnPolicies\ReturnPolicyResource;
use App\Filament\Resources\SellerPages\SellerPageResource;
use App\Filament\Resources\ShippingRules\ShippingRuleResource;
use App\Filament\Resources\SimilarProducts\SimilarProductResource;
use App\NavigationGroup;
use Filament\Support\Icons\Heroicon;

it('sets navigation groups and icons for catalog resources', function () {
    $resources = [
        PriceHistoryResource::class => [
            NavigationGroup::ANALYTICS,
            Heroicon::OutlinedCurrencyDollar,
        ],
        ProductBundleResource::class => [
            NavigationGroup::PRODUCT_MANAGEMENT,
            Heroicon::OutlinedQueueList,
        ],
        ProductFaqResource::class => [
            NavigationGroup::REVIEWS,
            Heroicon::OutlinedChatBubbleLeft,
        ],
        ProductGuaranteeResource::class => [
            NavigationGroup::PRODUCT_MANAGEMENT,
            Heroicon::OutlinedShieldCheck,
        ],
        ProductLiveStatResource::class => [
            NavigationGroup::ANALYTICS,
            Heroicon::OutlinedChartBarSquare,
        ],
        ReturnPolicyResource::class => [
            NavigationGroup::ORDER_MANAGEMENT,
            Heroicon::OutlinedDocument,
        ],
        SellerPageResource::class => [
            NavigationGroup::CONTENT,
            Heroicon::OutlinedDocumentText,
        ],
        ShippingRuleResource::class => [
            NavigationGroup::ORDER_MANAGEMENT,
            Heroicon::OutlinedTruck,
        ],
        SimilarProductResource::class => [
            NavigationGroup::PRODUCT_MANAGEMENT,
            Heroicon::OutlinedMagnifyingGlass,
        ],
    ];

    foreach ($resources as $resource => [$group, $icon]) {
        expect($resource::getNavigationGroup())->toBe($group)
            ->and($resource::getNavigationIcon())->toBe($icon);
    }
});
