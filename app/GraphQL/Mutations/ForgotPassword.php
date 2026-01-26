<?php

namespace App\GraphQL\Mutations;

use App\Models\Customer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPassword
{
    /**
     * Send password reset link to customer.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $customer = Customer::where('email', $args['email'])->first();

        if (!$customer) {
            return [
                'message' => 'If the email exists in our system, you will receive a password reset link shortly.',
                'success' => true,
            ];
        }

        // Generate reset token
        $token = Str::random(60);

        $customer->update([
            'reset_token' => $token,
            'reset_token_expires_at' => Carbon::now()->addHours(1),
        ]);

        // Send email (you'll need to create the email template)
        // Mail::to($customer->email)->send(new ResetPasswordMail($token));

        return [
            'message' => 'If the email exists in our system, you will receive a password reset link shortly.',
            'success' => true,
        ];
    }
}
