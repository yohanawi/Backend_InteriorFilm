<?php

namespace App\GraphQL\Queries;

use App\Models\WrappingArea;
use GraphQL\Error\Error;

class GetWrappingArea
{
    /**
     * Get a single wrapping area by slug
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $wrappingArea = WrappingArea::where('slug', $args['slug'])
            ->where('is_active', true)
            ->with(['products.category.catalog'])
            ->first();

        if (!$wrappingArea) {
            throw new Error('Wrapping area not found');
        }

        return $wrappingArea;
    }
}
