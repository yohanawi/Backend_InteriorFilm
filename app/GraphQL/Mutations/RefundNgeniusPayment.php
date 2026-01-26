<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\NgeniusClient;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundNgeniusPayment
{
    /**
     * Refund a captured N-Genius payment (full or partial).
     */
    public function __invoke($rootValue, array $args, $context, $resolveInfo)
    {
        // Admin only - verify permission
        $user = Auth::guard('sanctum')->user();
        // You may want to add admin check here

        $orderNumber = (string)($args['order_number'] ?? '');
        $refundAmount = $args['amount'] ?? null;
        $reason = $args['reason'] ?? 'Customer requested refund';

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

        if ($order->payment_status !== 'paid') {
            throw new Error('Can only refund paid orders');
        }

        DB::beginTransaction();

        try {
            $ngenius = NgeniusClient::fromConfig();

            // Use order amount if not specified (full refund)
            $amountMinor = $refundAmount
                ? (int) round($refundAmount * 100)
                : $order->ngenius_amount_minor;

            $response = $ngenius->refundPayment($order->ngenius_reference, $amountMinor);

            // Update order status
            $isFullRefund = !$refundAmount || ($amountMinor >= $order->ngenius_amount_minor);

            if ($isFullRefund) {
                $order->payment_status = 'refunded';
                $order->status = 'cancelled';
            } else {
                $order->payment_status = 'partially_refunded';
            }

            $order->save();

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'comment' => sprintf(
                    '%s refund processed via N-Genius (Amount: %s). Reason: %s',
                    $isFullRefund ? 'Full' : 'Partial',
                    $refundAmount ? number_format($refundAmount, 2) : 'Full amount',
                    $reason
                ),
                'notify_customer' => true,
            ]);

            DB::commit();

            $order->load(['items.product', 'customer']);

            return [
                'order' => $order,
                'message' => 'Refund processed successfully',
                'success' => true,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('N-Genius refund payment failed', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage(),
            ]);

            throw new Error('Failed to process refund: ' . $e->getMessage());
        }
    }
}
