<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use GraphQL\Error\Error;

class UpdateOrderPaymentStatus
{
    public function __invoke($rootValue, array $args, $context, $resolveInfo)
    {
        $order = Order::find($args['order_id']);

        if (!$order) {
            throw new Error('Order not found');
        }

        $validStatuses = ['pending', 'paid', 'failed', 'refunded'];
        if (!in_array($args['payment_status'], $validStatuses)) {
            throw new Error('Invalid payment status');
        }

        $order->payment_status = $args['payment_status'];

        if ($args['payment_status'] === 'paid' && !$order->paid_at) {
            $order->paid_at = now();
        }

        $order->save();

        return $order->load(['items.product', 'customer', 'statusHistories']);
    }
}
