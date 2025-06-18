<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Request as MovingRequest;
use App\Models\Address;
use App\Models\Material;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    public function index()
    {
        $requests = MovingRequest::with(['addresses', 'materials'])->paginate(10);
        return response()->json($requests);
    }

    public function getUserRequests()
    {
        $userId = auth()->id();
        $requests = MovingRequest::where('user_id', $userId)
            ->with(['addresses', 'materials'])
            ->latest()
            ->paginate(10);
        
        return response()->json($requests);
    }

    public function store(HttpRequest $request)
    {
        $validated = $request->validate([
            'property_type' => 'required|in:commercial,residential',
            'square_feet' => 'nullable|numeric',
            'additional_objects' => 'nullable|array',
            'movers_count' => 'required|integer|min:1',
            'hourly_rate' => 'required|numeric|min:0',
            'departure_time' => 'required|date',
            'labor_included' => 'required|boolean',
            'package_type' => 'nullable|string',
            'addresses' => 'required|array|min:2',
            'addresses.*.address' => 'required|string',
            'addresses.*.type' => 'required|in:loading,unloading,intermediate',
            'addresses.*.order' => 'required|integer|min:0',
            'addresses.*.latitude' => 'required|numeric|between:-90,90',
            'addresses.*.longitude' => 'required|numeric|between:-180,180',
            'materials' => 'nullable|array',
            'materials.*.name' => 'required|string',
            'materials.*.quantity' => 'required|integer|min:1',
        ]);

        $validated['request_number'] = 'REQ-' . strtoupper(Str::random(8));
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        $movingRequest = MovingRequest::create($validated);

        // Create addresses
        foreach ($request->addresses as $address) {
            $movingRequest->addresses()->create([
                'address' => $address['address'],
                'type' => $address['type'],
                'order' => $address['order'],
                'latitude' => $address['latitude'],
                'longitude' => $address['longitude']
            ]);
        }

        // Create materials if provided
        if ($request->has('materials')) {
            foreach ($request->materials as $material) {
                $movingRequest->materials()->create($material);
            }
        }

        return response()->json($movingRequest->load(['addresses', 'materials']), 201);
    }

    public function show($id)
    {
        $request = MovingRequest::with(['addresses', 'materials'])->findOrFail($id);
        return response()->json($request);
    }

    public function update(HttpRequest $httpRequest, $id)
    {
        $request = MovingRequest::findOrFail($id);
        
        $validated = $httpRequest->validate([
            'property_type' => 'sometimes|required|in:commercial,residential',
            'square_feet' => 'nullable|numeric',
            'additional_objects' => 'nullable|array',
            'movers_count' => 'sometimes|required|integer|min:1',
            'hourly_rate' => 'sometimes|required|numeric|min:0',
            'departure_time' => 'sometimes|required|date',
            'labor_included' => 'sometimes|required|boolean',
            'package_type' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,confirmed,active,break,completed,cancelled',
            'addresses' => 'sometimes|required|array|min:2',
            'addresses.*.address' => 'required|string',
            'addresses.*.type' => 'required|in:loading,unloading,intermediate',
            'addresses.*.order' => 'required|integer|min:0',
            'addresses.*.latitude' => 'required|numeric|between:-90,90',
            'addresses.*.longitude' => 'required|numeric|between:-180,180',
            'materials' => 'nullable|array',
            'materials.*.name' => 'required|string',
            'materials.*.quantity' => 'required|integer|min:1',
        ]);

        $request->update($validated);

        if ($httpRequest->has('addresses')) {
            $request->addresses()->delete();
            foreach ($httpRequest->addresses as $address) {
                $request->addresses()->create([
                    'address' => $address['address'],
                    'type' => $address['type'],
                    'order' => $address['order'],
                    'latitude' => $address['latitude'],
                    'longitude' => $address['longitude']
                ]);
            }
        }

        if ($httpRequest->has('materials')) {
            $request->materials()->delete();
            foreach ($httpRequest->materials as $material) {
                $request->materials()->create($material);
            }
        }

        return response()->json($request->load(['addresses', 'materials']));
    }

    public function destroy($id)
    {
        $request = MovingRequest::findOrFail($id);
        $request->delete();
        return response()->noContent();
    }
}
