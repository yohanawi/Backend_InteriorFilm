<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\NgeniusClient;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoidNgeniusPayment
{
    /**
     * Void an authorized N-Genius payment.
     * 
     * This cancels an authorization before it's captured.
     */
    public function __invoke($rootValue, array $args, $context, $resolveInfo)
    {
        // Admin only - verify permission
        $user = Auth::guard('sanctum')->user();
        // You may want to add admin check here

        $orderNumber = (string)($args['order_number'] ?? '');
        $reason = $args['reason'] ?? 'Order cancelled';

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

        if ($order->ngenius_last_payment_state === 'CAPTURED') {
            throw new Error('Cannot void captured payment. Use refund instead.');
        }

        DB::beginTransaction();

        try {
            $ngenius = NgeniusClient::fromConfig();

            $response = $ngenius->voidPayment($order->ngenius_reference);

            // Update order status
            $order->payment_status = 'cancelled';
            $order->status = 'cancelled';
            $order->ngenius_last_payment_state = 'VOIDED';
            $order->save();

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'comment' => 'Payment authorization voided via N-Genius. Reason: ' . $reason,
                'notify_customer' => true,
            ]);

            DB::commit();

            $order->load(['items.product', 'customer']);

            return [
                'order' => $order,
                'message' => 'Payment voided successfully',
                'success' => true,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('N-Genius void payment failed', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage(),
            ]);

            throw new Error('Failed to void payment: ' . $e->getMessage());
        }
    }
}
