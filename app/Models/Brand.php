<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    /** @use HasFactory<\Database\Factories\BrandFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'vendor_id',
        'is_vendor_brand',
    ];

    protected function casts(): array
    {
        return [
            'is_vendor_brand' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * The vendor that owns this brand (if it's a vendor-created brand)
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Vendors authorized to sell this brand
     */
    public function authorizedVendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'brand_vendor')
            ->withPivot([
                'is_authorized',
                'authorized_at',
                'authorization_type',
                'authorization_document',
                'invoice_document',
                'valid_from',
                'valid_until',
                'status',
            ])
            ->withTimestamps();
    }

    public function isVendorAuthorized(Vendor $vendor): bool
    {
        if ($this->vendor_id === $vendor->id) {
            return true;
        }

        return $this->authorizedVendors()
            ->where('vendor_id', $vendor->id)
            ->where('status', 'approved')
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->exists();
    }
}
