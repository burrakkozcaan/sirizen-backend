<?php

namespace App\Models;

use App\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'email_verified',
        'google_id',
        'apple_id',
        'avatar',
        'email_verified_at',
        'email_verification_code',
        'email_verification_code_expires_at',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'role' => UserRole::class,
            'email_verified' => 'boolean',
            'email_verification_code_expires_at' => 'datetime',
        ];
    }

    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function sellerReviews(): HasMany
    {
        return $this->hasMany(SellerReview::class);
    }

    public function recentlyViewed(): HasMany
    {
        return $this->hasMany(RecentlyViewed::class);
    }

    public function notificationPreference(): HasOne
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function searchHistories(): HasMany
    {
        return $this->hasMany(SearchHistory::class);
    }

    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    public function priceAlerts(): HasMany
    {
        return $this->hasMany(PriceAlert::class);
    }

    public function stockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isVendor(): bool
    {
        return $this->role === UserRole::VENDOR;
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::CUSTOMER;
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function consents(): HasMany
    {
        return $this->hasMany(UserConsent::class);
    }

    public function dataDeletionRequests(): HasMany
    {
        return $this->hasMany(DataDeletionRequest::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class);
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }
}
