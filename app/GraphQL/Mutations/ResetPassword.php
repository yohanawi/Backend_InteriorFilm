<?php

namespace App\GraphQL\Mutations;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ResetPassword
{
    /**
     * Reset customer password.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $customer = Customer::where('email', $args['email'])
            ->where('reset_token', $args['token'])
            ->first();

        if (!$customer) {
            throw ValidationException::withMessages([
                'token' => ['Invalid or expired reset token.'],
            ]);
        }

        // Check if token is expired
        if ($customer->reset_token_expires_at < Carbon::now()) {
            throw ValidationException::withMessages([
                'token' => ['Reset token has expired. Please request a new one.'],
            ]);
        }

        // Update password
        $customer->update([
            'password' => Hash::make($args['password']),
            'reset_token' => null,
            'reset_token_expires_at' => null,
        ]);

        return [
            'message' => 'Password has been reset successfully. You can now login with your new password.',
            'success' => true,
        ];
    }
}
