<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Auth;
use GraphQL\Error\Error;

class UpdateOrderStatus
{
    public function __invoke($rootValue, array $args, $context, $resolveInfo)
    {
        $order = Order::find($args['order_id']);

        if (!$order) {
            throw new Error('Order not found');
        }

        $validStatuses = ['pending', 'processing', 'confirmed', 'shipped', 'delivered', 'cancelled', 'refunded'];
        if (!in_array($args['status'], $validStatuses)) {
            throw new Error('Invalid order status');
        }

        $oldStatus = $order->status;
        $order->status = $args['status'];

        // Update timestamps based on status
        if ($args['status'] === 'shipped' && !$order->shipped_at) {
            $order->shipped_at = now();
        }

        if ($args['status'] === 'delivered' && !$order->delivered_at) {
            $order->delivered_at = now();
        }

        $order->save();

        // Create status history
        $user = Auth::user();
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id' => $user ? $user->id : null,
            'status' => $args['status'],
            'comment' => $args['comment'] ?? "Status changed from {$oldStatus} to {$args['status']}",
            'notify_customer' => $args['notify_customer'] ?? false,
        ]);

        // TODO: Send email notification if notify_customer is true

        return $order->load(['items.product', 'customer', 'statusHistories']);
    }
}
