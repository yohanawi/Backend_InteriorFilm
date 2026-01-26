<?php

namespace App\GraphQL\Queries;

use App\Models\WrappingArea;
use Illuminate\Support\Facades\Log;

class GetWrappingAreas
{
    /**
     * Get paginated wrapping areas
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $query = WrappingArea::query();

        // Filter by active status
        if (isset($args['is_active'])) {
            $query->where('is_active', $args['is_active']);
        }

        // Search functionality
        if (!empty($args['search'])) {
            $search = $args['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%")
                    ->orWhere('meta_description', 'LIKE', "%{$search}%");
            });
        }

        // Order by sort_order and created_at
        $query->ordered();

        // Pagination
        $perPage = $args['first'] ?? 15;
        $page = $args['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
