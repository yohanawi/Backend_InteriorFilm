<?php

namespace App\GraphQL\Queries;

use App\Models\Blog;

class GetBlog
{
    public function __invoke($rootValue, array $args, $context, $resolveInfo)
    {
        $slug = $args['slug'] ?? null;
        if (!$slug) {
            return null;
        }

        return Blog::query()
            ->with(['catalog'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();
    }
}
