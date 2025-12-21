<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PackingMaterial;
use Illuminate\Http\Request;

class PackingMaterialController extends Controller
{
    /**
     * Get all active packing materials.
     *
     * Returns full service option and individual materials separately
     * for easier frontend handling.
     */
    public function index()
    {
        $materials = PackingMaterial::active()
            ->orderBy('order')
            ->get();

        return response()->json([
            'full_service' => $materials->where('is_full_service', true)->first(),
            'individual_materials' => $materials->where('is_full_service', false)->values(),
        ]);
    }
}
