<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandingPageSettings;
use App\Models\LandingService;
use App\Models\LandingReview;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        return response()->json([
            'settings' => LandingPageSettings::first(),
            'services' => LandingService::where('is_active', true)
                ->orderBy('order')
                ->get(),
            'reviews' => LandingReview::where('is_active', true)
                ->orderBy('order')
                ->get(),
        ]);
    }

    public function settings()
    {
        return response()->json(LandingPageSettings::first());
    }

    public function services()
    {
        return response()->json(
            LandingService::where('is_active', true)
                ->orderBy('order')
                ->get()
        );
    }

    public function reviews()
    {
        return response()->json(
            LandingReview::where('is_active', true)
                ->orderBy('order')
                ->get()
        );
    }
}
