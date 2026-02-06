<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformRevenueReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'period_type',
        'total_revenue',
        'total_commission',
        'vendor_payouts',
        'total_orders',
        'total_vendors',
        'active_vendors',
        'new_vendors',
        'total_customers',
        'new_customers',
        'total_products',
        'avg_order_value',
        'top_categories',
        'top_vendors',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'total_revenue' => 'decimal:2',
            'total_commission' => 'decimal:2',
            'vendor_payouts' => 'decimal:2',
            'total_orders' => 'integer',
            'total_vendors' => 'integer',
            'active_vendors' => 'integer',
            'new_vendors' => 'integer',
            'total_customers' => 'integer',
            'new_customers' => 'integer',
            'total_products' => 'integer',
            'avg_order_value' => 'decimal:2',
            'top_categories' => 'array',
            'top_vendors' => 'array',
        ];
    }
}
