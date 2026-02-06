<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_campaigns',
        'email_orders',
        'email_promotions',
        'email_reviews',
        'sms_campaigns',
        'sms_orders',
        'sms_promotions',
        'push_enabled',
        'push_campaigns',
        'push_orders',
        'push_messages',
    ];

    protected function casts(): array
    {
        return [
            'email_campaigns' => 'boolean',
            'email_orders' => 'boolean',
            'email_promotions' => 'boolean',
            'email_reviews' => 'boolean',
            'sms_campaigns' => 'boolean',
            'sms_orders' => 'boolean',
            'sms_promotions' => 'boolean',
            'push_enabled' => 'boolean',
            'push_campaigns' => 'boolean',
            'push_orders' => 'boolean',
            'push_messages' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
