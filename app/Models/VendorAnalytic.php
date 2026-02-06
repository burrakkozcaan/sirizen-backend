<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'date',
        'total_sales',
        'total_orders',
        'average_order_value',
        'units_sold',
        'commission_amount',
        'net_earnings',
        'pending_payout',
        'active_products',
        'out_of_stock_products',
        'products_views',
        'conversion_rate',
        'unique_customers',
        'new_customers',
        'returning_customers',
        'total_reviews',
        'average_rating',
        'questions_answered',
        'response_time_hours',
        'shipped_on_time',
        'late_shipments',
        'cancelled_orders',
        'returned_orders',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_sales' => 'decimal:2',
            'average_order_value' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'net_earnings' => 'decimal:2',
            'pending_payout' => 'decimal:2',
            'conversion_rate' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'response_time_hours' => 'decimal:2',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

