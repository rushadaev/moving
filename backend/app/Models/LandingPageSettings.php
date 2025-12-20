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
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
