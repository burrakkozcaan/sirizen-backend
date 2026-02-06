<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCampaign extends Model
{
    /** @use HasFactory<\Database\Factories\ProductCampaignFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'campaign_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
