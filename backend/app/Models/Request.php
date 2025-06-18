<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Request extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'request_number',
        'property_type',
        'square_feet',
        'additional_objects',
        'movers_count',
        'hourly_rate',
        'departure_time',
        'labor_included',
        'package_type',
        'price',
        'status',
        'user_id',
        'operator_id'
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'price' => 'decimal:2',
        'square_feet' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'additional_objects' => 'array',
        'labor_included' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class)->orderBy('order');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function getLoadingAddressAttribute()
    {
        return $this->addresses()->where('type', 'loading')->first();
    }

    public function getUnloadingAddressAttribute()
    {
        return $this->addresses()->where('type', 'unloading')->first();
    }

    public function getIntermediateAddressesAttribute()
    {
        return $this->addresses()->where('type', 'intermediate')->get();
    }

    public function canBeModified(): bool
    {
        return in_array($this->status, ['pending']);
    }
}
