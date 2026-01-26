<?php

namespace App\GraphQL\Mutations;

class RemoveFromWishlist
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\Customer|null $customer */
        $customer = auth('sanctum')->user();

        if (!$customer) {
            return [
                'success' => false,
                'message' => 'Unauthenticated.',
            ];
        }

        $customer->wishlistProducts()->detach([(int) $args['product_id']]);

        return [
            'success' => true,
            'message' => 'Removed from wishlist.',
        ];
    }
}
