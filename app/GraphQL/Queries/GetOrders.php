<?php

namespace App\GraphQL\Queries;

use App\Models\Order;

class GetOrders
{
    public function __invoke($rootValue, array $args, $context, $resolveInfo)
    {
        $query = Order::with(['customer', 'items'])->orderBy('created_at', 'desc');

        // Filter by status
        if (isset($args['status'])) {
            $query->where('status', $args['status']);
        }

        // Filter by payment status
        if (isset($args['payment_status'])) {
            $query->where('payment_status', $args['payment_status']);
        }

        // Search by order number, customer name, or email
        if (isset($args['search']) && !empty($args['search'])) {
            $search = $args['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('shipping_email', 'like', "%{$search}%")
                    ->orWhere('shipping_first_name', 'like', "%{$search}%")
                    ->orWhere('shipping_last_name', 'like', "%{$search}%");
            });
        }

        $perPage = $args['first'] ?? 15;
        $page = $args['page'] ?? 1;

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'paginatorInfo' => [
                'count' => $paginator->count(),
                'currentPage' => $paginator->currentPage(),
                'firstItem' => $paginator->firstItem(),
                'hasMorePages' => $paginator->hasMorePages(),
                'lastItem' => $paginator->lastItem(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }
}
