<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingService extends Model
{
    protected $fillable = [
        'title',
        'icon',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
