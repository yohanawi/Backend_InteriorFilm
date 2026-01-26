<?php

namespace App\GraphQL\Queries;

class MyWishlist
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
            return [];
        }

        return $customer->wishlistProducts()->get();
    }
}
