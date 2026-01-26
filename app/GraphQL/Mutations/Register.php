<?php

namespace App\GraphQL\Mutations;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Register
{
    /**
     * Register a new customer.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        // Create the customer
        $customer = Customer::create([
            'first_name' => $args['first_name'],
            'last_name' => $args['last_name'],
            'email' => $args['email'],
            'company' => $args['company'] ?? null,
            'password' => Hash::make($args['password']),
            'phone' => $args['phone'] ?? null,
            'address' => $args['address'] ?? null,
            'city' => $args['city'] ?? null,
            'state' => $args['state'] ?? null,
            'country' => $args['country'] ?? null,
            'postal_code' => $args['postal_code'] ?? null,
            'status' => 'active',
        ]);

        // Create token
        $token = $customer->createToken('auth_token', ['customer'])->plainTextToken;

        return [
            'token' => $token,
            'customer' => $customer,
            'message' => 'Registration successful! Welcome to our store.',
        ];
    }
}
