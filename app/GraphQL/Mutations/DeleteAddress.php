<?php

namespace App\GraphQL\Mutations;

use App\Models\Address;
use Illuminate\Validation\ValidationException;

class DeleteAddress
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\Customer $customer */
        $customer = auth('sanctum')->user();

        $address = Address::query()
            ->where('id', $args['id'])
            ->where('customer_id', $customer->id)
            ->first();

        if (!$address) {
            throw ValidationException::withMessages([
                'id' => ['Address not found.'],
            ]);
        }

        $address->delete();

        return [
            'success' => true,
            'message' => 'Address deleted.',
        ];
    }
}
