<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerPage extends Model
{
    /** @use HasFactory<\Database\Factories\SellerPageFactory> */
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'seo_slug',
        'description',
        'banner',
        'logo',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
