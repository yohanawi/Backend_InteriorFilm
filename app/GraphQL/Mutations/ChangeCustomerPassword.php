<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ChangeCustomerPassword
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
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'string'],
        ]);

        $validator->validate();

        if (!Hash::check($args['current_password'], $customer->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $customer->password = $args['new_password'];
        $customer->save();

        return [
            'success' => true,
            'message' => 'Password updated successfully.',
        ];
    }
}
