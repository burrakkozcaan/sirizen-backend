<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchTag extends Model
{
    protected $fillable = [
        'label',
        'url',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
