<?php

namespace App\GraphQL\Mutations;

use App\Models\Product;
use Illuminate\Validation\ValidationException;

class AddToWishlist
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
            throw ValidationException::withMessages([
                'auth' => ['Unauthenticated.'],
            ]);
        }

        $product = Product::query()->where('id', $args['product_id'])->first();
        if (!$product) {
            throw ValidationException::withMessages([
                'product_id' => ['Product not found.'],
            ]);
        }

        $customer->wishlistProducts()->syncWithoutDetaching([(int) $product->id]);

        return [
            'success' => true,
            'message' => 'Added to wishlist.',
        ];
    }
}
