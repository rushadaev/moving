<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function store(HttpRequest $request): JsonResponse
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string',
            'mover_ratings' => 'nullable|array',
        ]);

        $movingRequest = Request::findOrFail($validated['request_id']);

        // Check if user owns this request
        if ($movingRequest->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if review already exists
        if ($movingRequest->review) {
            return response()->json(['error' => 'Review already submitted'], 422);
        }

        $review = Review::create([
            'request_id' => $validated['request_id'],
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'] ?? null,
            'mover_ratings' => $validated['mover_ratings'] ?? null,
            'is_published' => false, // Will be reviewed by admin before publishing
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review
        ], 201);
    }

    public function show(string $requestId): JsonResponse
    {
        $review = Review::where('request_id', $requestId)->first();

        if (!$review) {
            return response()->json(['error' => 'Review not found'], 404);
        }

        return response()->json($review);
    }

    public function update(HttpRequest $request, string $id): JsonResponse
    {
        $review = Review::findOrFail($id);

        // Check if user owns this review
        if ($review->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'review_text' => 'nullable|string',
            'mover_ratings' => 'nullable|array',
        ]);

        $review->update($validated);

        return response()->json([
            'message' => 'Review updated successfully',
            'review' => $review
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $review = Review::findOrFail($id);

        // Check if user owns this review
        if ($review->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully'
        ]);
    }
}
