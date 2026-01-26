<?php

namespace App\GraphQL\Queries;

class Me
{
    /**
     * Get the authenticated customer.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return auth('sanctum')->user();
    }
}
