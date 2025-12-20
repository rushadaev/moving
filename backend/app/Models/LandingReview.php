<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingReview extends Model
{
    protected $fillable = [
        'customer_name',
        'review_text',
        'rating',
        'order',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
