<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'price',
        'icon',
        'description',
        'is_active',
        'is_full_service',
        'order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_full_service' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFullService($query)
    {
        return $query->where('is_full_service', true);
    }

    public function scopeIndividual($query)
    {
        return $query->where('is_full_service', false);
    }
}
