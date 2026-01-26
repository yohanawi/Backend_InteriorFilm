<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateCustomer
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\Customer $customer */
        $customer = auth('sanctum')->user();

        $validator = Validator::make($args, [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customer->id)],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
        ]);

        $validator->validate();

        $customer->fill($validator->validated());
        $customer->save();

        return $customer->fresh();
    }
}
