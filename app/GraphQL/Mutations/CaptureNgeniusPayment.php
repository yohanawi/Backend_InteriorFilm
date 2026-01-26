<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\NgeniusClient;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CaptureNgeniusPayment
{
    /**
     * Capture an authorized N-Genius payment.
     * 
     * This is used when the order was created with action=AUTH
     * and you want to capture the payment after order fulfillment.
     */
    public function __invoke($rootValue, array $args, $context, $resolveInfo)
    {
        // Admin only - verify permission
        $user = Auth::guard('sanctum')->user();
        // You may want to add admin check here

        $orderNumber = (string)($args['order_number'] ?? '');
        $amountToCapture = $args['amount'] ?? null;

        if (!$orderNumber) {
            throw new Error('Missing order_number');
        }

        /** @var Order $order */
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            throw new Error('Order not found');
        }

        if ($order->payment_method !== 'ngenius_hpp') {
            throw new Error('Order is not an N-Genius payment');
        }

        if (!$order->ngenius_reference) {
            throw new Error('Order does not have N-Genius reference');
        }

        if ($order->payment_status === 'paid' && $order->ngenius_last_payment_state === 'CAPTURED') {
            throw new Error('Payment already captured');
        }

        DB::beginTransaction();

        try {
            $ngenius = NgeniusClient::fromConfig();

            // Use order amount if not specified
            $amountMinor = $amountToCapture
                ? (int) round($amountToCapture * 100)
                : $order->ngenius_amount_minor;

            $response = $ngenius->capturePayment($order->ngenius_reference, $amountMinor);

            // Parse response
            $parsed = $ngenius->parsePaymentState($response);
            $order->ngenius_last_payment_state = $parsed['state'];
            $order->payment_status = 'paid';

            if ($order->status === 'pending') {
                $order->status = 'processing';
            }

            if (!$order->paid_at) {
                $order->paid_at = now();
            }

            $order->save();

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'comment' => 'Payment captured via N-Genius' . ($amountToCapture ? " (Amount: {$amountToCapture})" : ''),
                'notify_customer' => true,
            ]);

            DB::commit();

            $order->load(['items.product', 'customer']);

            return [
                'order' => $order,
                'message' => 'Payment captured successfully',
                'success' => true,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('N-Genius capture payment failed', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage(),
            ]);

            throw new Error('Failed to capture payment: ' . $e->getMessage());
        }
    }
}
