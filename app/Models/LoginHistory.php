<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginHistory extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'os',
        'country',
        'city',
        'is_suspicious',
        'is_new_location',
        'is_new_device',
        'logged_in_at',
        'logged_out_at',
    ];

    protected function casts(): array
    {
        return [
            'is_suspicious' => 'boolean',
            'is_new_location' => 'boolean',
            'is_new_device' => 'boolean',
            'logged_in_at' => 'datetime',
            'logged_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
