<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class MyOrders
{
    public function __invoke($rootValue, array $args, $context, $resolveInfo)
    {
        $customer = Auth::guard('sanctum')->user();

        return Order::where('customer_id', $customer->id)
            ->with(['items.product', 'statusHistories'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
