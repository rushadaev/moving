<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Create a payment intent
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createPaymentIntent(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'currency' => 'required|string|size:3',
                'request_id' => 'required|exists:requests,id'
            ]);

            $result = $this->paymentService->createPaymentIntent(
                $request->amount,
                $request->currency,
                $request->request_id
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Confirm a payment
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmPayment(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'payment_intent_id' => 'required|string',
                'request_id' => 'required|exists:requests,id'
            ]);

            $result = $this->paymentService->confirmPayment(
                $request->payment_intent_id,
                $request->request_id
            );

            if ($result) {
                return response()->json([
                    'message' => 'Payment confirmed successfully'
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

    /**
     * Get payment status
     *
     * @param string $paymentIntentId
     * @return JsonResponse
     */
    public function getPaymentStatus(string $paymentIntentId): JsonResponse
    {
        try {
            $result = $this->paymentService->getPaymentStatus($paymentIntentId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
} 