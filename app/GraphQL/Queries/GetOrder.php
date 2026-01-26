<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use GraphQL\Error\Error;

class GetOrder
{
    public function __invoke($rootValue, array $args, $context, $resolveInfo)
    {
        $customer = Auth::guard('sanctum')->user();

        $query = Order::where('customer_id', $customer->id);

        if (isset($args['id'])) {
            $query->where('id', $args['id']);
        } elseif (isset($args['order_number'])) {
            $query->where('order_number', $args['order_number']);
        } else {
            throw new Error('Either id or order_number must be provided');
        }

        $order = $query->with(['items.product', 'statusHistories.user', 'customer'])->first();

        if (!$order) {
            throw new Error('Order not found');
        }

        return $order;
    }
}
