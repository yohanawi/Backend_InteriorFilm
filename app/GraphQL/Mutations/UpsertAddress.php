<?php

namespace App\GraphQL\Mutations;

use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpsertAddress
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\Customer $customer */
        $customer = auth('sanctum')->user();

        $isPrimary = array_key_exists('is_primary', $args) ? (bool) $args['is_primary'] : false;

        return DB::transaction(function () use ($args, $customer, $isPrimary) {
            if (!empty($args['id'])) {
                $address = Address::query()
                    ->where('id', $args['id'])
                    ->where('customer_id', $customer->id)
                    ->first();

                if (!$address) {
                    throw ValidationException::withMessages([
                        'id' => ['Address not found.'],
                    ]);
                }
            } else {
                $address = new Address();
                // Keep legacy column non-null by mirroring customer_id into user_id.
                $address->user_id = $customer->id;
                $address->customer_id = $customer->id;
            }

            $address->address_line_1 = $args['address_line_1'];
            $address->address_line_2 = $args['address_line_2'] ?? null;
            $address->city = $args['city'];
            $address->postal_code = $args['postal_code'];
            $address->state = $args['state'];
            $address->country = $args['country'];
            $address->type = (int) $args['type'];
            $address->is_primary = $isPrimary;

            // Ensure ownership fields are set
            $address->customer_id = $customer->id;
            $address->user_id = $customer->id;

            if ($isPrimary) {
                Address::query()
                    ->where('customer_id', $customer->id)
                    ->where('id', '!=', $address->id ?? 0)
                    ->update(['is_primary' => false]);
            }

            $address->save();

            return $address;
        });
    }
}
