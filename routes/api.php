<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\VendorRegisterController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\EngagementController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PdpController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PricingController;
use App\Http\Controllers\Api\ProductCampaignController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductCoreController;
use App\Http\Controllers\Api\ProductQuestionController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\QuickLinkController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SellerController;
use App\Http\Controllers\Api\SimilarProductsController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\CargoWebhookController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Next.js Frontend
|--------------------------------------------------------------------------
|
| These routes serve the Next.js App Router frontend.
| All routes use Laravel Sanctum for authentication.
|
*/

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================

// Authentication
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/vendor/register', [VendorRegisterController::class, 'register']); // Satıcı kaydı
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-email-code', [AuthController::class, 'verifyEmailCode']); // E-posta doğrulama kodu
    Route::post('/resend-verification-code', [AuthController::class, 'resendVerificationCode']); // Doğrulama kodu tekrar gönder

    // Social Auth
    Route::post('/google', [SocialAuthController::class, 'googleCallback']); // Google ile giriş
    Route::post('/apple', [SocialAuthController::class, 'appleCallback']);   // Apple ile giriş (ileride)
});

// Products
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);              // GET /api/products
    Route::get('/bestsellers', [ProductController::class, 'bestsellers']); // GET /api/products/bestsellers?limit=12
    Route::get('/new-arrivals', [ProductController::class, 'newArrivals']); // GET /api/products/new-arrivals?limit=12
    Route::get('/buy-more-save-more', [ProductController::class, 'buyMoreSaveMore']); // GET /api/products/buy-more-save-more?limit=12
    Route::get('/recommended', [ProductController::class, 'recommended']); // GET /api/products/recommended?limit=12
    Route::get('/flash-sales', [ProductController::class, 'flashSales']); // GET /api/products/flash-sales?limit=12
    Route::get('/{slug}', [ProductController::class, 'show']);         // GET /api/products/{slug}
    Route::get('/{id}/reviews', [ProductController::class, 'reviews']);// GET /api/products/{id}/reviews
    Route::get('/{id}/questions', [ProductController::class, 'questions']); // GET /api/products/{id}/questions
    Route::get('/{id}/bundles', [ProductController::class, 'bundles']); // GET /api/products/{id}/bundles
    Route::get('/{id}/similar', [SimilarProductsController::class, 'similar']); // GET /api/products/{id}/similar
    Route::get('/{id}/related', [SimilarProductsController::class, 'related']); // GET /api/products/{id}/related?type=cross|up|also_bought
});

// PDP Micro Endpoints (Trendyol-style architecture)
Route::get('/product-core/{id}', [ProductCoreController::class, 'show']); // GET /api/product-core/{id|slug} - Long cache (1 day)
Route::get('/pricing/{productId}', [PricingController::class, 'show']);     // GET /api/pricing/{productId}?variant={id} - Short cache (30s)
Route::get('/seller/{sellerId}', [SellerController::class, 'show']);       // GET /api/seller/{sellerId} - Medium cache (5 min)
Route::get('/campaigns', [ProductCampaignController::class, 'index']);     // GET /api/campaigns?productId={id} - User cache (10 min)
Route::get('/engagement/{productId}', [EngagementController::class, 'show']); // GET /api/engagement/{productId} - Very short cache (30s)
Route::get('/pdp-blocks/{productId}', [BlockController::class, 'show']); // GET /api/pdp-blocks/{productId} - Medium cache (6 hours)

// Trendyol-style PDP API
Route::prefix('pdp')->group(function () {
    Route::get('/{slug}', [PdpController::class, 'show']);                    // GET /api/pdp/{slug} - Full PDP data
    Route::get('/{slug}/badges', [PdpController::class, 'badges']);          // GET /api/pdp/{slug}/badges
    Route::get('/{slug}/social-proof', [PdpController::class, 'socialProof']); // GET /api/pdp/{slug}/social-proof
    Route::get('/{slug}/highlights', [PdpController::class, 'highlights']);  // GET /api/pdp/{slug}/highlights
    Route::get('/{slug}/variant', [PdpController::class, 'getVariant']);     // GET /api/pdp/{slug}/variant?variant_id=123
    Route::get('/{slug}/reviews', [PdpController::class, 'getReviews']);     // GET /api/pdp/{slug}/reviews
    Route::get('/{slug}/questions', [PdpController::class, 'getQuestions']); // GET /api/pdp/{slug}/questions
    Route::get('/{slug}/related', [PdpController::class, 'getRelatedProducts']); // GET /api/pdp/{slug}/related
    Route::post('/{slug}/recalculate-badges', [PdpController::class, 'recalculateBadges']); // POST /api/pdp/{slug}/recalculate-badges
});

// Filter API
Route::prefix('filters')->group(function () {
    Route::get('/category/{slug}', [FilterController::class, 'byCategory']);     // GET /api/filters/category/{slug}
    Route::get('/category-group/{id}', [FilterController::class, 'byCategoryGroup']); // GET /api/filters/category-group/{id}
    Route::get('/campaign/{slug}', [FilterController::class, 'byCampaign']);     // GET /api/filters/campaign/{slug}
});

// Cart Modal API (Trendyol-style)
Route::prefix('cart-modal')->group(function () {
    Route::get('/{slug}', [\App\Http\Controllers\Api\CartModalController::class, 'show']);              // GET /api/cart-modal/{slug}
    Route::post('/{slug}/validate-variant', [\App\Http\Controllers\Api\CartModalController::class, 'validateVariant']); // POST /api/cart-modal/{slug}/validate-variant
    Route::get('/{slug}/layout-config', [\App\Http\Controllers\Api\CartModalController::class, 'getLayoutConfig']);    // GET /api/cart-modal/{slug}/layout-config
});

// Locations (Cities & Districts)
Route::prefix('locations')->group(function () {
    Route::get('/cities', [LocationController::class, 'cities']); // GET /api/locations/cities?search=...
    Route::get('/cities/{cityId}/districts', [LocationController::class, 'districts']); // GET /api/locations/cities/{id}/districts?search=...
    Route::get('/estimate', [LocationController::class, 'estimate']); // GET /api/locations/estimate?city_id={id}&district_id={id}
});

// Categories
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);             // GET /api/categories
    Route::get('/{slug}/vendors', [CategoryController::class, 'vendors']); // GET /api/categories/{slug}/vendors
    Route::get('/{slug}/products', [CategoryController::class, 'products']); // GET /api/categories/{slug}/products
    Route::get('/{slug}', [CategoryController::class, 'show']);        // GET /api/categories/{slug}
});

// Brands
Route::prefix('brands')->group(function () {
    Route::get('/', [BrandController::class, 'index']);                // GET /api/brands
    Route::get('/{slug}', [BrandController::class, 'show']);           // GET /api/brands/{slug}
    Route::get('/{slug}/products', [BrandController::class, 'products']); // GET /api/brands/{slug}/products
});

// Vendors (Stores)
Route::prefix('vendors')->group(function () {
    Route::get('/', [VendorController::class, 'index']);               // GET /api/vendors
    Route::get('/featured', [VendorController::class, 'featured']);    // GET /api/vendors/featured?limit=12
    Route::get('/followed', [VendorController::class, 'followed']);    // GET /api/vendors/followed (must be before /{slug})
    Route::get('/{slug}', [VendorController::class, 'show']);          // GET /api/vendors/{slug}
    Route::get('/{slug}/products', [VendorController::class, 'products']); // GET /api/vendors/{slug}/products
    Route::get('/{slug}/check-follow', [VendorController::class, 'checkFollow']); // GET /api/vendors/{slug}/check-follow
    Route::post('/{slug}/follow', [VendorController::class, 'follow']); // POST /api/vendors/{slug}/follow
    Route::delete('/{slug}/follow', [VendorController::class, 'unfollow']); // DELETE /api/vendors/{slug}/follow
    Route::get('/{id}/reviews', [VendorController::class, 'reviews']); // GET /api/vendors/{id}/reviews
});

// Campaigns
Route::prefix('campaigns')->group(function () {
    Route::get('/active', [CampaignController::class, 'active']);      // GET /api/campaigns/active
    Route::get('/hero', [CampaignController::class, 'hero']);          // GET /api/campaigns/hero
    Route::get('/{slug}/products', [CampaignController::class, 'products']); // GET /api/campaigns/{slug}/products?limit=24
    Route::get('/{slug}', [CampaignController::class, 'show']);        // GET /api/campaigns/{slug}
});

// Quick Links
Route::get('/quick-links', [QuickLinkController::class, 'index']);     // GET /api/quick-links

// Home Engine (Dynamic Homepage Sections)
Route::get('/home', [HomeController::class, 'index']);                  // GET /api/home

// Collections (Vendor Collections for Homepage)
Route::prefix('collections')->group(function () {
    Route::get('/vendor', [CollectionController::class, 'vendorCollections']); // GET /api/collections/vendor?limit=6
    Route::get('/{id}', [CollectionController::class, 'show']);                 // GET /api/collections/{id}
    Route::get('/', [CollectionController::class, 'index']);                    // GET /api/collections?ids=1,2,3
});

// Search
Route::get('/search', [SearchController::class, 'search']);            // GET /api/search?q=...


// ============================================
// PAYMENT & CARGO WEBHOOKS (No Auth - Signature Verified)
// ============================================
Route::prefix('webhooks')->group(function () {
    // Payment Webhooks
    Route::post('/payment/paytr', [PaymentWebhookController::class, 'handlePaytrCallback']);   // POST /api/webhooks/payment/paytr
    Route::post('/payment/iyzico', [PaymentWebhookController::class, 'handleIyzicoCallback']); // POST /api/webhooks/payment/iyzico
    Route::any('/payment/test', [PaymentWebhookController::class, 'handleTestCallback']);      // ANY /api/webhooks/payment/test

    // Cargo Webhooks
    Route::post('/cargo/aras', [CargoWebhookController::class, 'handleArasWebhook']);       // POST /api/webhooks/cargo/aras
    Route::post('/cargo/yurtici', [CargoWebhookController::class, 'handleYurticiWebhook']); // POST /api/webhooks/cargo/yurtici
    Route::post('/cargo/mng', [CargoWebhookController::class, 'handleMngWebhook']);         // POST /api/webhooks/cargo/mng
});

// ============================================
// PROTECTED ROUTES (Authentication Required)
// ============================================

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);             // GET /api/auth/me
        Route::post('/logout', [AuthController::class, 'logout']);    // POST /api/auth/logout
    });

    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);            // GET /api/cart
        Route::post('/add', [CartController::class, 'add']);          // POST /api/cart/add
        Route::put('/{id}', [CartController::class, 'update']);       // PUT /api/cart/{id}
        Route::delete('/{id}', [CartController::class, 'remove']);    // DELETE /api/cart/{id}
        Route::delete('/', [CartController::class, 'clear']);         // DELETE /api/cart (clear all)
    });

    // Favorites
    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);        // GET /api/favorites
        Route::post('/', [FavoriteController::class, 'add']);         // POST /api/favorites
        Route::delete('/{productId}', [FavoriteController::class, 'remove']); // DELETE /api/favorites/{productId}
    });

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/status-counts', [OrderController::class, 'statusCounts']); // GET /api/orders/status-counts (must be before /{id})
        Route::get('/', [OrderController::class, 'index']);           // GET /api/orders
        Route::get('/{id}', [OrderController::class, 'show']);        // GET /api/orders/{id}
        Route::post('/', [OrderController::class, 'store']);          // POST /api/orders
        Route::get('/{id}/tracking', [OrderController::class, 'tracking']); // GET /api/orders/{id}/tracking
        Route::post('/{id}/cancel', [OrderController::class, 'cancel']); // POST /api/orders/{id}/cancel
    });

    // Payment
    Route::prefix('payments')->group(function () {
        Route::post('/paytr/token', [PaymentController::class, 'createPayTRToken']); // POST /api/payments/paytr/token
        Route::get('/status/{orderId}', [PaymentController::class, 'checkPaymentStatus']); // GET /api/payments/status/{orderId}
    });

    // Addresses
    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index']);         // GET /api/addresses
        Route::post('/', [AddressController::class, 'store']);        // POST /api/addresses
        Route::put('/{id}/set-default', [AddressController::class, 'setDefault']); // PUT /api/addresses/{id}/set-default (must be before /{id})
        Route::put('/{id}', [AddressController::class, 'update']);    // PUT /api/addresses/{id}
        Route::delete('/{id}', [AddressController::class, 'destroy']); // DELETE /api/addresses/{id}
    });

    // Reviews
    Route::prefix('reviews')->group(function () {
        Route::post('/', [ReviewController::class, 'store']);         // POST /api/reviews
        Route::put('/{id}', [ReviewController::class, 'update']);     // PUT /api/reviews/{id}
        Route::delete('/{id}', [ReviewController::class, 'destroy']); // DELETE /api/reviews/{id}
    });

    // Product Questions (User's questions)
    Route::prefix('product-questions')->group(function () {
        Route::get('/', [ProductQuestionController::class, 'index']); // GET /api/product-questions (user's questions)
        Route::post('/', [ProductQuestionController::class, 'store']); // POST /api/product-questions (ask a question)
    });

    // Checkout / Payment
    Route::prefix('checkout')->group(function () {
        Route::post('/pay', [CheckoutController::class, 'pay']);         // POST /api/checkout/pay
        Route::get('/success', [CheckoutController::class, 'success']);  // GET /api/checkout/success
        Route::get('/fail', [CheckoutController::class, 'fail']);        // GET /api/checkout/fail
    });
});
