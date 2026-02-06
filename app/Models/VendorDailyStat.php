<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorDailyStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'stat_date',
        'total_sales',
        'revenue',
        'commission',
        'net_revenue',
        'orders_count',
        'products_sold',
        'new_customers',
        'returning_customers',
        'avg_order_value',
        'page_views',
        'product_views',
        'conversion_rate',
    ];

    protected function casts(): array
    {
        return [
            'stat_date' => 'date',
            'total_sales' => 'integer',
            'revenue' => 'decimal:2',
            'commission' => 'decimal:2',
            'net_revenue' => 'decimal:2',
            'orders_count' => 'integer',
            'products_sold' => 'integer',
            'new_customers' => 'integer',
            'returning_customers' => 'integer',
            'avg_order_value' => 'decimal:2',
            'page_views' => 'integer',
            'product_views' => 'integer',
            'conversion_rate' => 'decimal:2',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
