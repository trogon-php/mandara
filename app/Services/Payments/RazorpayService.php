<?php

namespace App\Services\Payments;

use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
class RazorpayService
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(
            env('RAZORPAY_KEY_ID'),
            env('RAZORPAY_KEY_SECRET')
        );
    }

    /**
     * Create Razorpay order
     * 
     * @param array $orderData - ['receipt' => string, 'amount' => float, 'currency' => string, 'notes' => array]
     * @return array
     */
    public function createOrder(array $orderData): array
    {
        try {
            // Convert amount to paise if needed
            if (isset($orderData['amount']) && is_float($orderData['amount'])) {
                $orderData['amount'] = (int) ($orderData['amount'] * 100);
            }

            $razorpayOrder = $this->api->order->create($orderData);

            return [
                'status' => true,
                'order_id' => $razorpayOrder['id'],
                'amount' => $razorpayOrder['amount'],
                'currency' => $razorpayOrder['currency'],
                'receipt' => $razorpayOrder['receipt'],
                'key_id' => env('RAZORPAY_KEY_ID'),
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed', [
                'error' => $e->getMessage(),
                'order_data' => $orderData,
            ]);

            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment signature
     * 
     * @param array $paymentData - ['razorpay_order_id' => string, 'razorpay_payment_id' => string, 'razorpay_signature' => string]
     * @return bool
     */
    public function verifyPayment(array $paymentData): bool
    {
        $attributes = [
            'razorpay_order_id' => $paymentData['razorpay_order_id'],
            'razorpay_payment_id' => $paymentData['razorpay_payment_id'],
            'razorpay_signature' => $paymentData['razorpay_signature'],
        ];

        try {
            $this->api->utility->verifyPaymentSignature($attributes);
            return true;
        } catch (\Exception $e) {
            Log::error('Razorpay signature verification failed', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData,
            ]);
            return false;
        }
    }

    /**
     * Handle webhook events
     * 
     * @param string $payload - Raw webhook payload
     * @param string $signature - Webhook signature from header
     * @return array
     */
    public function handleWebhook(string $payload, string $signature): array
    {
        $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');
        
        if (!$this->verifyWebhookSignature($payload, $signature, $webhookSecret)) {
            return [
                'status' => false,
                'message' => 'Invalid webhook signature',
            ];
        }

        $event = json_decode($payload, true);
        
        return [
            'status' => true,
            'event' => $event['event'] ?? null,
            'payload' => $event['payload'] ?? [],
        ];
    }

    /**
     * Verify webhook signature
     */
    protected function verifyWebhookSignature(string $payload, string $signature, string $secret): bool
    {
        if (!$signature || !$secret) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get Razorpay key ID (for frontend)
     */
    public function getKeyId(): string
    {
        return env('RAZORPAY_KEY_ID');
    }
}
