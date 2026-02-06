<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\BrandAuthorizationController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CargoIntegrationController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductApprovalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImportLogController;
use App\Http\Controllers\ProductQuestionController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\RevenueReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SellerPageController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TierController;
use App\Http\Controllers\VendorAnalyticController;
use App\Http\Controllers\VendorDailyStatController;
use App\Http\Controllers\VendorDocumentController;
use App\Http\Controllers\VendorSlaMetricController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('/vendor-application/pending', function () {
    return Inertia::render('auth/vendor-application-pending');
})->name('vendor.application.pending');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
  
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('orders', [OrderController::class, 'index'])->name('order.index');
    Route::get('orders/{id}', [OrderController::class, 'show'])->name('order.show');
    Route::put('orders/{id}', [OrderController::class, 'update'])->name('order.update');
    Route::get('products', [ProductController::class, 'index'])->name('product.index');
    Route::post('products', [ProductController::class, 'store'])->name('product.store');
    Route::put('products/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::get('addresses', [AddressController::class, 'index'])->name('address.index');
    Route::get('shipping', [ShippingController::class, 'index'])->name('shipping.index');
    Route::put('shipping/{id}', [ShippingController::class, 'update'])->name('shipping.update');
    Route::get('returns', [ReturnController::class, 'index'])->name('return.index');
    Route::put('returns/{id}', [ReturnController::class, 'update'])->name('return.update');
    Route::get('followers', [FollowerController::class, 'index'])->name('follower.index');
    Route::get('reviews', [ReviewController::class, 'index'])->name('review.index');
    Route::put('reviews/{id}', [ReviewController::class, 'update'])->name('review.update');
    Route::get('balance', [BalanceController::class, 'index'])->name('balance.index');
    Route::get('payments', [PaymentController::class, 'index'])->name('payment.index');
    Route::get('tiers', [TierController::class, 'index'])->name('tier.index');
    Route::get('campaigns', [CampaignController::class, 'index'])->name('campaign.index');
    Route::post('campaigns', [CampaignController::class, 'store'])->name('campaign.store');
    Route::put('campaigns/{id}', [CampaignController::class, 'update'])->name('campaign.update');
    Route::get('coupons', [CouponController::class, 'index'])->name('coupon.index');
    Route::put('coupons/{id}', [CouponController::class, 'update'])->name('coupon.update');
    Route::get('support', [SupportController::class, 'index'])->name('support.index');
    Route::get('help', [SupportController::class, 'help'])->name('help');
    Route::put('support/{id}', [SupportController::class, 'update'])->name('support.update');
    Route::post('support/messages', [SupportController::class, 'storeMessage'])->name('support.messages.store');
    Route::get('product-questions', [ProductQuestionController::class, 'index'])
        ->name('product-question.index');
    Route::put('product-questions/{id}', [ProductQuestionController::class, 'update'])
        ->name('product-question.update');
    Route::post('addresses', [AddressController::class, 'store'])->name('address.store');
    Route::put('addresses/{id}', [AddressController::class, 'update'])->name('address.update');
    Route::delete('addresses/{id}', [AddressController::class, 'destroy'])->name('address.destroy');
    // Vendor Management
    Route::get('vendor-documents', [VendorDocumentController::class, 'index'])->name('vendor-document.index');
    // Finance
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoice.index');
    // Shipping
    Route::get('cargo-integrations', [CargoIntegrationController::class, 'index'])->name('cargo-integration.index');
    // Products
    Route::get('import-logs', [ProductImportLogController::class, 'index'])->name('product-import-log.index');
    // Analytics
    Route::get('sla-metrics', [VendorSlaMetricController::class, 'index'])->name('vendor-sla-metric.index');
    Route::get('daily-stats', [VendorDailyStatController::class, 'index'])->name('vendor-daily-stat.index');
    Route::get('revenue-reports', [RevenueReportController::class, 'index'])->name('revenue-report.index');
    // Seller Page
    Route::get('seller-page', [SellerPageController::class, 'index'])->name('seller-page.index');
    Route::put('seller-page', [SellerPageController::class, 'update'])->name('seller-page.update');
    // Analytics
    Route::get('vendor-analytics', [VendorAnalyticController::class, 'index'])->name('vendor-analytic.index');
    // Product Approvals
    Route::get('product-approvals', [ProductApprovalController::class, 'index'])->name('product-approval.index');
    // Brand Authorizations
    Route::get('brand-authorizations', [BrandAuthorizationController::class, 'index'])->name('brand-authorization.index');
    Route::post('brand-authorizations/authorize', [BrandAuthorizationController::class, 'authorizeVendor'])->name('brand-authorization.authorize');
    Route::post('brand-authorizations/revoke', [BrandAuthorizationController::class, 'revokeAuthorization'])->name('brand-authorization.revoke');
});

require __DIR__.'/settings.php';
