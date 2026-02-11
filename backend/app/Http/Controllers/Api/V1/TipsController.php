<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Services\PaymentService;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\JsonResponse;

class TipsController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Calculate tips distribution among movers
     */
    public function calculate(HttpRequest $request): JsonResponse
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
            'amount' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $movingRequest = Request::findOrFail($validated['request_id']);

        // Calculate tips amount or percentage
        if (isset($validated['amount'])) {
            $tipsAmount = $validated['amount'];
            $tipsPercentage = ($movingRequest->price > 0)
                ? ($tipsAmount / $movingRequest->price) * 100
                : 0;
        } elseif (isset($validated['percentage'])) {
            $tipsPercentage = $validated['percentage'];
            $tipsAmount = ($movingRequest->price * $tipsPercentage) / 100;
        } else {
            return response()->json(['error' => 'Either amount or percentage must be provided'], 422);
        }

        // Calculate distribution per mover
        $moversCount = $movingRequest->movers_count ?? 2;
        $amountPerMover = $moversCount > 0 ? $tipsAmount / $moversCount : 0;

        $distribution = [];
        for ($i = 1; $i <= $moversCount; $i++) {
            $distribution[] = [
                'mover_number' => $i,
                'amount' => round($amountPerMover, 2)
            ];
        }

        return response()->json([
            'tips_amount' => round($tipsAmount, 2),
            'tips_percentage' => round($tipsPercentage, 2),
            'movers_count' => $moversCount,
            'per_mover' => round($amountPerMover, 2),
            'distribution' => $distribution
        ]);
    }

    /**
     * Save tips to request
     */
    public function store(HttpRequest $request): JsonResponse
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
            'tips_amount' => 'required|numeric|min:0',
            'tips_percentage' => 'required|numeric|min:0',
            'tips_distribution' => 'required|array',
        ]);

        $movingRequest = Request::findOrFail($validated['request_id']);

        // Check if user owns this request
        if ($movingRequest->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if request is completed
        if ($movingRequest->status !== 'completed') {
            return response()->json(['error' => 'Request must be completed before adding tips'], 422);
        }

        $movingRequest->update([
            'tips_amount' => $validated['tips_amount'],
            'tips_percentage' => $validated['tips_percentage'],
            'tips_distribution' => $validated['tips_distribution'],
            'tips_payment_status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Tips saved successfully',
            'request' => $movingRequest
        ]);
    }

    /**
     * Create payment intent for tips
     */
    public function createPaymentIntent(HttpRequest $request): JsonResponse
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
        ]);

        $movingRequest = Request::findOrFail($validated['request_id']);

        // Check if user owns this request
        if ($movingRequest->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$movingRequest->tips_amount || $movingRequest->tips_amount <= 0) {
            return response()->json(['error' => 'Tips amount must be greater than 0'], 422);
        }

        try {
            $result = $this->paymentService->createPaymentIntent(
                $movingRequest->tips_amount,
                'usd',
                $movingRequest->id
            );

            $movingRequest->update([
                'tips_stripe_session_id' => $result['sessionId']
            ]);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Confirm tips payment
     */
    public function confirmPayment(HttpRequest $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
            'request_id' => 'required|exists:requests,id'
        ]);

        $movingRequest = Request::findOrFail($validated['request_id']);

        try {
            $result = $this->paymentService->confirmPayment(
                $validated['session_id'],
                $validated['request_id']
            );

            if ($result) {
                $movingRequest->update([
                    'tips_payment_status' => 'paid'
                ]);

                return response()->json([
                    'message' => 'Tips payment confirmed successfully'
                ]);
            }

            return response()->json([
                'error' => 'Payment not succeeded'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
