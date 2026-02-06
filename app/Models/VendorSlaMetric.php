<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorSlaMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'metric_date',
        'total_orders',
        'cancelled_orders',
        'returned_orders',
        'late_shipments',
        'on_time_shipments',
        'cancel_rate',
        'return_rate',
        'late_shipment_rate',
        'avg_shipment_time',
        'avg_response_time',
        'total_questions_answered',
        'total_reviews_responded',
        'customer_satisfaction_score',
        'sla_violations',
    ];

    protected function casts(): array
    {
        return [
            'metric_date' => 'date',
            'total_orders' => 'integer',
            'cancelled_orders' => 'integer',
            'returned_orders' => 'integer',
            'late_shipments' => 'integer',
            'on_time_shipments' => 'integer',
            'cancel_rate' => 'decimal:2',
            'return_rate' => 'decimal:2',
            'late_shipment_rate' => 'decimal:2',
            'avg_shipment_time' => 'integer',
            'avg_response_time' => 'integer',
            'total_questions_answered' => 'integer',
            'total_reviews_responded' => 'integer',
            'customer_satisfaction_score' => 'decimal:2',
            'sla_violations' => 'array',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
