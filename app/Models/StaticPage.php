<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{
    /** @use HasFactory<\Database\Factories\StaticPageFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
