<?php

namespace App\Services;

use App\Models\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a new payment session
     *
     * @param float $amount Amount in cents
     * @param string $currency Currency code
     * @param int $requestId Request ID
     * @return array Payment session details
     * @throws \Exception
     */
    public function createPaymentIntent(float $amount, string $currency, int $requestId): array
    {
        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => 'Moving Request #' . $requestId,
                        ],
                        'unit_amount' => $amount * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => config('app.frontend_url') . '/payment/return?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('app.frontend_url') . '/payment/cancel',
                'metadata' => [
                    'request_id' => $requestId
                ]
            ]);

            return [
                'sessionId' => $session->id,
                'redirectUrl' => $session->url
            ];
        } catch (ApiErrorException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get payment status
     *
     * @param string $sessionId
     * @return array Payment status details
     * @throws \Exception
     */
    public function getPaymentStatus(string $sessionId): array
    {
        $cacheKey = "payment_status_{$sessionId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($sessionId) {
            try {
                $session = Session::retrieve($sessionId);
                
                return [
                    'status' => $session->payment_status,
                    'amount' => $session->amount_total / 100,
                    'currency' => $session->currency,
                    'request_id' => $session->metadata->request_id,
                    'redirectUrl' => $session->url
                ];
            } catch (ApiErrorException $e) {
                Log::error('Failed to retrieve payment status', [
                    'sessionId' => $sessionId,
                    'error' => $e->getMessage()
                ]);
                throw new \Exception('Failed to retrieve payment status: ' . $e->getMessage());
            }
        });
    }

    /**
     * Confirm payment
     *
     * @param string $sessionId
     * @param int $requestId Request ID
     * @return bool Confirmation result
     * @throws \Exception
     */
    public function confirmPayment(string $sessionId, int $requestId): bool
    {
        try {
            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $request = Request::find($requestId);
                $request->payment_status = 'paid';
                $request->save();

                return true;
            }

            return false;
        } catch (ApiErrorException $e) {
            Log::error('Payment confirmation failed', [
                'sessionId' => $sessionId,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Failed to confirm payment: ' . $e->getMessage());
        }
    }
} 