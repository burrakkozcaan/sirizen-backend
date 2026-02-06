<?php

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\RelationManagers\AttributesRelationManager;
use App\Filament\Resources\Products\RelationManagers\CampaignsRelationManager;
use App\Filament\Resources\Products\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductBadgesRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductBannersRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductSafetyDocumentsRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductSafetyImagesRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductFeaturesRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductSellersRelationManager;
use App\Filament\Resources\Products\RelationManagers\ProductVideosRelationManager;
use App\Filament\Resources\Products\RelationManagers\SimilarProductsRelationManager;
use App\Filament\Resources\Products\RelationManagers\VariantsRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\SellerPagesRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\SellerReviewsRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\VendorCampaignsRelationManager;
use App\Filament\Resources\Vendors\RelationManagers\VendorBadgesRelationManager;
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
use App\Filament\Resources\Vendors\VendorResource;
use App\Models\Address;
use App\Models\Brand;
use App\Models\BlogPost;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Coupon;
use App\Models\NotificationPreference;
use App\Models\OrderItem;
use App\Models\PriceAlert;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductBadge;
use App\Models\ProductBanner;
use App\Models\ProductCampaign;
use App\Models\ProductFeature;
use App\Models\ProductFaq;
use App\Models\ProductGuarantee;
use App\Models\ProductImage;
use App\Models\ProductSafetyDocument;
use App\Models\ProductSafetyImage;
use App\Models\ProductQuestion;
use App\Models\ProductReview;
use App\Models\ProductReturn;
use App\Models\ReviewHelpfulVote;
use App\Models\ReviewImage;
use App\Models\ReturnPolicy;
use App\Models\RelatedProduct;
use App\Models\ProductSeller;
use App\Models\ProductVideo;
use App\Models\ReturnImage;
use App\Models\SearchHistory;
use App\Models\SellerPage;
use App\Models\ShippingRule;
use App\Models\SellerBadge;
use App\Models\SellerReview;
use App\Models\SimilarProduct;
use App\Models\ProductVariant;
use App\Models\StockAlert;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorFollower;
use App\Models\VendorPenalty;
use App\Models\VendorScore;
use App\Models\VendorTier;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tests\TestCase;

uses(TestCase::class);

it('defines product seller relationships', function () {
    $product = new Product();
    $vendor = new Vendor();
    $productSeller = new ProductSeller();

    expect($product->productSellers())->toBeInstanceOf(HasMany::class)
        ->and($product->productSellers()->getRelated())->toBeInstanceOf(ProductSeller::class)
        ->and($vendor->productSellers())->toBeInstanceOf(HasMany::class)
        ->and($vendor->productSellers()->getRelated())->toBeInstanceOf(ProductSeller::class)
        ->and($productSeller->product())->toBeInstanceOf(BelongsTo::class)
        ->and($productSeller->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($productSeller->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($productSeller->vendor()->getRelated())->toBeInstanceOf(Vendor::class);
});

it('defines product detail relationships', function () {
    $product = new Product();
    $campaign = new Campaign();
    $productBanner = new ProductBanner();
    $productBadge = new ProductBadge();
    $productFeature = new ProductFeature();
    $productCampaign = new ProductCampaign();
    $productSafetyImage = new ProductSafetyImage();
    $productSafetyDocument = new ProductSafetyDocument();

    expect($product->productBanners())->toBeInstanceOf(HasMany::class)
        ->and($product->productBanners()->getRelated())->toBeInstanceOf(ProductBanner::class)
        ->and($product->productBadges())->toBeInstanceOf(HasMany::class)
        ->and($product->productBadges()->getRelated())->toBeInstanceOf(ProductBadge::class)
        ->and($product->productFeatures())->toBeInstanceOf(HasMany::class)
        ->and($product->productFeatures()->getRelated())->toBeInstanceOf(ProductFeature::class)
        ->and($product->campaigns())->toBeInstanceOf(BelongsToMany::class)
        ->and($product->campaigns()->getRelated())->toBeInstanceOf(Campaign::class)
        ->and($product->productCampaigns())->toBeInstanceOf(HasMany::class)
        ->and($product->productCampaigns()->getRelated())->toBeInstanceOf(ProductCampaign::class)
        ->and($productBanner->product())->toBeInstanceOf(BelongsTo::class)
        ->and($productBanner->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($productBadge->product())->toBeInstanceOf(BelongsTo::class)
        ->and($productBadge->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($productFeature->product())->toBeInstanceOf(BelongsTo::class)
        ->and($productFeature->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($productCampaign->product())->toBeInstanceOf(BelongsTo::class)
        ->and($productCampaign->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($productCampaign->campaign())->toBeInstanceOf(BelongsTo::class)
        ->and($productCampaign->campaign()->getRelated())->toBeInstanceOf(Campaign::class)
        ->and($product->safetyImages())->toBeInstanceOf(HasMany::class)
        ->and($product->safetyImages()->getRelated())->toBeInstanceOf(ProductSafetyImage::class)
        ->and($product->safetyDocuments())->toBeInstanceOf(HasMany::class)
        ->and($product->safetyDocuments()->getRelated())->toBeInstanceOf(ProductSafetyDocument::class)
        ->and($productSafetyImage->product())->toBeInstanceOf(BelongsTo::class)
        ->and($productSafetyImage->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($productSafetyDocument->product())->toBeInstanceOf(BelongsTo::class)
        ->and($productSafetyDocument->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($campaign->products())->toBeInstanceOf(BelongsToMany::class)
        ->and($campaign->products()->getRelated())->toBeInstanceOf(Product::class)
        ->and($campaign->productCampaigns())->toBeInstanceOf(HasMany::class)
        ->and($campaign->productCampaigns()->getRelated())->toBeInstanceOf(ProductCampaign::class);
});

it('defines alert relationships', function () {
    $product = new Product();
    $user = new User();
    $priceAlert = new PriceAlert();
    $stockAlert = new StockAlert();

    expect($product->priceAlerts())->toBeInstanceOf(HasMany::class)
        ->and($product->priceAlerts()->getRelated())->toBeInstanceOf(PriceAlert::class)
        ->and($product->stockAlerts())->toBeInstanceOf(HasMany::class)
        ->and($product->stockAlerts()->getRelated())->toBeInstanceOf(StockAlert::class)
        ->and($user->priceAlerts())->toBeInstanceOf(HasMany::class)
        ->and($user->priceAlerts()->getRelated())->toBeInstanceOf(PriceAlert::class)
        ->and($user->stockAlerts())->toBeInstanceOf(HasMany::class)
        ->and($user->stockAlerts()->getRelated())->toBeInstanceOf(StockAlert::class)
        ->and($priceAlert->user())->toBeInstanceOf(BelongsTo::class)
        ->and($priceAlert->user()->getRelated())->toBeInstanceOf(User::class)
        ->and($priceAlert->product())->toBeInstanceOf(BelongsTo::class)
        ->and($priceAlert->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($stockAlert->user())->toBeInstanceOf(BelongsTo::class)
        ->and($stockAlert->user()->getRelated())->toBeInstanceOf(User::class)
        ->and($stockAlert->product())->toBeInstanceOf(BelongsTo::class)
        ->and($stockAlert->product()->getRelated())->toBeInstanceOf(Product::class);
});

it('defines user content relationships', function () {
    $user = new User();
    $blogPost = new BlogPost();
    $contactMessage = new ContactMessage();
    $notificationPreference = new NotificationPreference();
    $searchHistory = new SearchHistory();

    expect($user->blogPosts())->toBeInstanceOf(HasMany::class)
        ->and($user->blogPosts()->getRelated())->toBeInstanceOf(BlogPost::class)
        ->and($blogPost->user())->toBeInstanceOf(BelongsTo::class)
        ->and($blogPost->user()->getRelated())->toBeInstanceOf(User::class)
        ->and($user->contactMessages())->toBeInstanceOf(HasMany::class)
        ->and($user->contactMessages()->getRelated())->toBeInstanceOf(ContactMessage::class)
        ->and($contactMessage->user())->toBeInstanceOf(BelongsTo::class)
        ->and($contactMessage->user()->getRelated())->toBeInstanceOf(User::class)
        ->and($user->notificationPreference())->toBeInstanceOf(HasOne::class)
        ->and($user->notificationPreference()->getRelated())->toBeInstanceOf(NotificationPreference::class)
        ->and($notificationPreference->user())->toBeInstanceOf(BelongsTo::class)
        ->and($notificationPreference->user()->getRelated())->toBeInstanceOf(User::class)
        ->and($user->searchHistories())->toBeInstanceOf(HasMany::class)
        ->and($user->searchHistories()->getRelated())->toBeInstanceOf(SearchHistory::class)
        ->and($searchHistory->user())->toBeInstanceOf(BelongsTo::class)
        ->and($searchHistory->user()->getRelated())->toBeInstanceOf(User::class);
});

it('defines review image relationships', function () {
    $reviewImage = new ReviewImage();
    $productReview = new ProductReview();

    expect($reviewImage->productReview())->toBeInstanceOf(BelongsTo::class)
        ->and($reviewImage->productReview()->getRelated())->toBeInstanceOf(ProductReview::class)
        ->and($productReview->reviewImages())->toBeInstanceOf(HasMany::class)
        ->and($productReview->reviewImages()->getRelated())->toBeInstanceOf(ReviewImage::class);
});

it('defines product catalog relationships', function () {
    $product = new Product();
    $brand = new Brand();
    $category = new Category();
    $productImage = new ProductImage();
    $productVideo = new ProductVideo();
    $productReview = new ProductReview();
    $productQuestion = new ProductQuestion();
    $productGuarantee = new ProductGuarantee();
    $productFaq = new ProductFaq();
    $similarProduct = new SimilarProduct();
    $relatedProduct = new RelatedProduct();

    expect($product->brand())->toBeInstanceOf(BelongsTo::class)
        ->and($product->brand()->getRelated())->toBeInstanceOf(Brand::class)
        ->and($product->category())->toBeInstanceOf(BelongsTo::class)
        ->and($product->category()->getRelated())->toBeInstanceOf(Category::class)
        ->and($product->images())->toBeInstanceOf(HasMany::class)
        ->and($product->images()->getRelated())->toBeInstanceOf(ProductImage::class)
        ->and($product->videos())->toBeInstanceOf(HasMany::class)
        ->and($product->videos()->getRelated())->toBeInstanceOf(ProductVideo::class)
        ->and($product->variants())->toBeInstanceOf(HasMany::class)
        ->and($product->variants()->getRelated())->toBeInstanceOf(ProductVariant::class)
        ->and($product->attributes())->toBeInstanceOf(HasMany::class)
        ->and($product->attributes()->getRelated())->toBeInstanceOf(ProductAttribute::class)
        ->and($product->reviews())->toBeInstanceOf(HasMany::class)
        ->and($product->reviews()->getRelated())->toBeInstanceOf(ProductReview::class)
        ->and($product->questions())->toBeInstanceOf(HasMany::class)
        ->and($product->questions()->getRelated())->toBeInstanceOf(ProductQuestion::class)
        ->and($product->guarantees())->toBeInstanceOf(HasMany::class)
        ->and($product->guarantees()->getRelated())->toBeInstanceOf(ProductGuarantee::class)
        ->and($product->faqs())->toBeInstanceOf(HasMany::class)
        ->and($product->faqs()->getRelated())->toBeInstanceOf(ProductFaq::class)
        ->and($product->similarProducts())->toBeInstanceOf(HasMany::class)
        ->and($product->similarProducts()->getRelated())->toBeInstanceOf(SimilarProduct::class)
        ->and($product->relatedProducts())->toBeInstanceOf(HasMany::class)
        ->and($product->relatedProducts()->getRelated())->toBeInstanceOf(RelatedProduct::class)
        ->and($brand->products())->toBeInstanceOf(HasMany::class)
        ->and($brand->products()->getRelated())->toBeInstanceOf(Product::class)
        ->and($category->products())->toBeInstanceOf(HasMany::class)
        ->and($category->products()->getRelated())->toBeInstanceOf(Product::class)
        ->and($productImage->product())->toBeInstanceOf(BelongsTo::class)
        ->and($productImage->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($productVideo->product())->toBeInstanceOf(BelongsTo::class)
        ->and($productVideo->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($productReview->product())->toBeInstanceOf(BelongsTo::class)
        ->and($productReview->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($productQuestion->product())->toBeInstanceOf(BelongsTo::class)
        ->and($productQuestion->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($productGuarantee->product())->toBeInstanceOf(BelongsTo::class)
        ->and($productGuarantee->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($productFaq->product())->toBeInstanceOf(BelongsTo::class)
        ->and($productFaq->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($productFaq->category())->toBeInstanceOf(BelongsTo::class)
        ->and($productFaq->category()->getRelated())->toBeInstanceOf(Category::class)
        ->and($similarProduct->product())->toBeInstanceOf(BelongsTo::class)
        ->and($similarProduct->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($similarProduct->similarProduct())->toBeInstanceOf(BelongsTo::class)
        ->and($similarProduct->similarProduct()->getRelated())->toBeInstanceOf(Product::class)
        ->and($relatedProduct->product())->toBeInstanceOf(BelongsTo::class)
        ->and($relatedProduct->product()->getRelated())->toBeInstanceOf(Product::class)
        ->and($relatedProduct->relatedProduct())->toBeInstanceOf(BelongsTo::class)
        ->and($relatedProduct->relatedProduct()->getRelated())->toBeInstanceOf(Product::class);
});

it('defines review helpful vote relationships', function () {
    $productReview = new ProductReview();
    $reviewHelpfulVote = new ReviewHelpfulVote();

    expect($productReview->helpfulVotes())->toBeInstanceOf(HasMany::class)
        ->and($productReview->helpfulVotes()->getRelated())->toBeInstanceOf(ReviewHelpfulVote::class)
        ->and($reviewHelpfulVote->productReview())->toBeInstanceOf(BelongsTo::class)
        ->and($reviewHelpfulVote->productReview()->getRelated())->toBeInstanceOf(ProductReview::class)
        ->and($reviewHelpfulVote->user())->toBeInstanceOf(BelongsTo::class)
        ->and($reviewHelpfulVote->user()->getRelated())->toBeInstanceOf(User::class);
});

it('defines return image relationships', function () {
    $productReturn = new ProductReturn();
    $returnImage = new ReturnImage();

    expect($productReturn->orderItem())->toBeInstanceOf(BelongsTo::class)
        ->and($productReturn->orderItem()->getRelated())->toBeInstanceOf(OrderItem::class)
        ->and($productReturn->returnImages())->toBeInstanceOf(HasMany::class)
        ->and($productReturn->returnImages()->getRelated())->toBeInstanceOf(ReturnImage::class)
        ->and($returnImage->productReturn())->toBeInstanceOf(BelongsTo::class)
        ->and($returnImage->productReturn()->getRelated())->toBeInstanceOf(ProductReturn::class);
});

it('defines wishlist relationships', function () {
    $wishlist = new Wishlist();
    $wishlistItem = new WishlistItem();

    expect($wishlist->user())->toBeInstanceOf(BelongsTo::class)
        ->and($wishlist->user()->getRelated())->toBeInstanceOf(User::class)
        ->and($wishlist->items())->toBeInstanceOf(HasMany::class)
        ->and($wishlist->items()->getRelated())->toBeInstanceOf(WishlistItem::class)
        ->and($wishlistItem->wishlist())->toBeInstanceOf(BelongsTo::class)
        ->and($wishlistItem->wishlist()->getRelated())->toBeInstanceOf(Wishlist::class)
        ->and($wishlistItem->product())->toBeInstanceOf(BelongsTo::class)
        ->and($wishlistItem->product()->getRelated())->toBeInstanceOf(Product::class);
});

it('defines vendor relationships', function () {
    $vendor = new Vendor();
    $vendorTier = new VendorTier();
    $brand = new Brand();
    $category = new Category();
    $sellerPage = new SellerPage();
    $campaign = new Campaign();
    $productQuestion = new ProductQuestion();
    $productReview = new ProductReview();
    $sellerReview = new SellerReview();
    $shippingRule = new ShippingRule();
    $returnPolicy = new ReturnPolicy();
    $vendorScore = new VendorScore();
    $vendorPenalty = new VendorPenalty();
    $vendorFollower = new VendorFollower();
    $address = new Address();
    $sellerBadge = new SellerBadge();
    $coupon = new Coupon();
    $orderItem = new OrderItem();

    expect($vendor->user())->toBeInstanceOf(BelongsTo::class)
        ->and($vendor->user()->getRelated())->toBeInstanceOf(User::class)
        ->and($vendor->tier())->toBeInstanceOf(BelongsTo::class)
        ->and($vendor->tier()->getRelated())->toBeInstanceOf(VendorTier::class)
        ->and($vendor->ownedBrands())->toBeInstanceOf(HasMany::class)
        ->and($vendor->ownedBrands()->getRelated())->toBeInstanceOf(Brand::class)
        ->and($vendor->authorizedBrands())->toBeInstanceOf(BelongsToMany::class)
        ->and($vendor->authorizedBrands()->getRelated())->toBeInstanceOf(Brand::class)
        ->and($vendor->categories())->toBeInstanceOf(BelongsToMany::class)
        ->and($vendor->categories()->getRelated())->toBeInstanceOf(Category::class)
        ->and($vendor->products())->toBeInstanceOf(BelongsToMany::class)
        ->and($vendor->products()->getRelated())->toBeInstanceOf(Product::class)
        ->and($vendor->sellerBadges())->toBeInstanceOf(BelongsToMany::class)
        ->and($vendor->sellerBadges()->getRelated())->toBeInstanceOf(SellerBadge::class)
        ->and($vendor->sellerPages())->toBeInstanceOf(HasMany::class)
        ->and($vendor->sellerPages()->getRelated())->toBeInstanceOf(SellerPage::class)
        ->and($vendor->campaigns())->toBeInstanceOf(HasMany::class)
        ->and($vendor->campaigns()->getRelated())->toBeInstanceOf(Campaign::class)
        ->and($vendor->coupons())->toBeInstanceOf(HasMany::class)
        ->and($vendor->coupons()->getRelated())->toBeInstanceOf(Coupon::class)
        ->and($vendor->productQuestions())->toBeInstanceOf(HasMany::class)
        ->and($vendor->productQuestions()->getRelated())->toBeInstanceOf(ProductQuestion::class)
        ->and($vendor->productReviews())->toBeInstanceOf(HasMany::class)
        ->and($vendor->productReviews()->getRelated())->toBeInstanceOf(ProductReview::class)
        ->and($vendor->sellerReviews())->toBeInstanceOf(HasMany::class)
        ->and($vendor->sellerReviews()->getRelated())->toBeInstanceOf(SellerReview::class)
        ->and($vendor->vendorScores())->toBeInstanceOf(HasMany::class)
        ->and($vendor->vendorScores()->getRelated())->toBeInstanceOf(VendorScore::class)
        ->and($vendor->vendorPenalties())->toBeInstanceOf(HasMany::class)
        ->and($vendor->vendorPenalties()->getRelated())->toBeInstanceOf(VendorPenalty::class)
        ->and($vendor->vendorFollowers())->toBeInstanceOf(HasMany::class)
        ->and($vendor->vendorFollowers()->getRelated())->toBeInstanceOf(VendorFollower::class)
        ->and($vendor->shippingRules())->toBeInstanceOf(HasMany::class)
        ->and($vendor->shippingRules()->getRelated())->toBeInstanceOf(ShippingRule::class)
        ->and($vendor->returnPolicies())->toBeInstanceOf(HasMany::class)
        ->and($vendor->returnPolicies()->getRelated())->toBeInstanceOf(ReturnPolicy::class)
        ->and($vendor->addresses())->toBeInstanceOf(HasMany::class)
        ->and($vendor->addresses()->getRelated())->toBeInstanceOf(Address::class)
        ->and($vendor->orderItems())->toBeInstanceOf(HasMany::class)
        ->and($vendor->orderItems()->getRelated())->toBeInstanceOf(OrderItem::class)
        ->and($sellerPage->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($sellerPage->vendor()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($campaign->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($campaign->vendor()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($coupon->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($coupon->vendor()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($productQuestion->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($productQuestion->vendor()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($productReview->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($productReview->vendor()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($sellerReview->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($sellerReview->vendor()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($sellerReview->user())->toBeInstanceOf(BelongsTo::class)
        ->and($sellerReview->user()->getRelated())->toBeInstanceOf(User::class)
        ->and($shippingRule->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($shippingRule->vendor()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($returnPolicy->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($returnPolicy->vendor()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($vendorScore->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($vendorScore->vendor()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($vendorPenalty->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($vendorPenalty->vendor()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($vendorFollower->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($vendorFollower->vendor()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($address->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($address->vendor()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($brand->owner())->toBeInstanceOf(BelongsTo::class)
        ->and($brand->owner()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($brand->authorizedVendors())->toBeInstanceOf(BelongsToMany::class)
        ->and($brand->authorizedVendors()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($vendorTier->vendors())->toBeInstanceOf(HasMany::class)
        ->and($vendorTier->vendors()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($sellerBadge->vendors())->toBeInstanceOf(BelongsToMany::class)
        ->and($sellerBadge->vendors()->getRelated())->toBeInstanceOf(Vendor::class)
        ->and($category->vendors())->toBeInstanceOf(BelongsToMany::class)
        ->and($category->vendors()->getRelated())->toBeInstanceOf(Vendor::class);
});

it('registers product detail relation managers on products', function () {
    expect(ProductResource::getRelations())
        ->toContain(ImagesRelationManager::class)
        ->toContain(VariantsRelationManager::class)
        ->toContain(AttributesRelationManager::class)
        ->toContain(ProductSellersRelationManager::class)
        ->toContain(SimilarProductsRelationManager::class)
        ->toContain(ProductBannersRelationManager::class)
        ->toContain(ProductBadgesRelationManager::class)
        ->toContain(ProductFeaturesRelationManager::class)
        ->toContain(ProductSafetyImagesRelationManager::class)
        ->toContain(ProductSafetyDocumentsRelationManager::class)
        ->toContain(ProductVideosRelationManager::class)
        ->toContain(CampaignsRelationManager::class);
});

it('registers vendor relation managers', function () {
    expect(VendorResource::getRelations())
        ->toContain(SellerPagesRelationManager::class)
        ->toContain(VendorProductsRelationManager::class)
        ->toContain(VendorCampaignsRelationManager::class)
        ->toContain(VendorCouponsRelationManager::class)
        ->toContain(VendorOrderItemsRelationManager::class)
        ->toContain(VendorProductQuestionsRelationManager::class)
        ->toContain(VendorProductReviewsRelationManager::class)
        ->toContain(SellerReviewsRelationManager::class)
        ->toContain(VendorScoresRelationManager::class)
        ->toContain(VendorBadgesRelationManager::class)
        ->toContain(VendorPenaltiesRelationManager::class)
        ->toContain(VendorFollowersRelationManager::class)
        ->toContain(VendorShippingRulesRelationManager::class)
        ->toContain(VendorReturnPoliciesRelationManager::class);
});
