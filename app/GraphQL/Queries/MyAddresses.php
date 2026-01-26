<?php

namespace App\GraphQL\Queries;

use App\Models\Address;

class MyAddresses
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

        return Address::query()
            ->where('customer_id', $customer->id)
            ->orderByDesc('is_primary')
            ->orderByDesc('id')
            ->get();
    }
}
