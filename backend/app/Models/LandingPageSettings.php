<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageSettings extends Model
{
    protected $fillable = [
        'logo',
        'company_name',
        'tagline',
        'description',
        'photo_title',
        'photo_url',
        'video_title',
        'video_url',
        'phone',
        'email',
        'instagram_url',
        'facebook_url',
        'youtube_url',
        'hourly_rate',
        'floor_fee',
        'transportation_fee_per_mile',
        'small_box_price',
        'medium_box_price',
        'large_box_price',
        'wardrobe_box_price',
        'paper_price',
        'plastic_tape_price',
        'bubble_wrap_price',
        'piano_fee',
        'gun_safe_fee',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'hourly_rate' => 'decimal:2',
        'floor_fee' => 'decimal:2',
        'transportation_fee_per_mile' => 'decimal:2',
        'small_box_price' => 'decimal:2',
        'medium_box_price' => 'decimal:2',
        'large_box_price' => 'decimal:2',
        'wardrobe_box_price' => 'decimal:2',
        'paper_price' => 'decimal:2',
        'plastic_tape_price' => 'decimal:2',
        'bubble_wrap_price' => 'decimal:2',
        'piano_fee' => 'decimal:2',
        'gun_safe_fee' => 'decimal:2',
    ];
}
