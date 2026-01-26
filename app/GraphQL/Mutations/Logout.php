<?php

namespace App\GraphQL\Mutations;

class Logout
{
    /**
     * Logout the customer.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $customer = auth('sanctum')->user();

        if ($customer) {
            $customer->tokens()->delete();
        }

        return [
            'message' => 'Logged out successfully.',
            'success' => true,
        ];
    }
}
