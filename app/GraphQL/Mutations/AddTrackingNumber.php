<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use GraphQL\Error\Error;

class AddTrackingNumber
{
    public function __invoke($rootValue, array $args, $context, $resolveInfo)
    {
        $order = Order::find($args['order_id']);

        if (!$order) {
            throw new Error('Order not found');
        }

        $order->tracking_number = $args['tracking_number'];
        $order->save();

        // TODO: Send email notification to customer with tracking number

        return $order->load(['items.product', 'customer', 'statusHistories']);
    }
}
