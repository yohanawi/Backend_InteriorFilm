<?php

namespace App\GraphQL\Mutations;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class Login
{
    /**
     * Login a customer.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $customer = Customer::where('email', $args['email'])->first();

        if (!$customer || !Hash::check($args['password'], $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if customer is active
        if ($customer->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Your account is not active. Please contact support.'],
            ]);
        }

        // Revoke all previous tokens
        $customer->tokens()->delete();

        // Create new token
        $token = $customer->createToken('auth_token', ['customer'])->plainTextToken;

        return [
            'token' => $token,
            'customer' => $customer,
            'message' => 'Login successful! Welcome back.',
        ];
    }
}
