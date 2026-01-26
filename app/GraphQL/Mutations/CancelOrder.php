<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Auth;
use GraphQL\Error\Error;

class CancelOrder
{
    public function __invoke($rootValue, array $args, $context, $resolveInfo)
    {
        $customer = Auth::guard('sanctum')->user();

        $order = Order::where('id', $args['order_id'])
            ->where('customer_id', $customer->id)
            ->first();

        if (!$order) {
            throw new Error('Order not found');
        }

        if (!$order->canBeCancelled()) {
            throw new Error('Order cannot be cancelled at this stage');
        }

        // Restore stock to warehouse
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->stock_warehouse += $item->quantity;
                $item->product->save();
            }
        }

        $order->status = 'cancelled';
        $order->save();

        // Create status history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'cancelled',
            'comment' => 'Order cancelled by customer',
            'notify_customer' => false,
        ]);

        return [
            'message' => 'Order cancelled successfully',
            'success' => true,
        ];
    }
}
