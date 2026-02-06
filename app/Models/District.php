<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model
{
    protected $fillable = [
        'city_id',
        'name',
        'slug',
        'extra_delivery_days',
    ];

    protected function casts(): array
    {
        return [
            'extra_delivery_days' => 'integer',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
