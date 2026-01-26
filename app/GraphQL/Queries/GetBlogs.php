<?php

namespace App\GraphQL\Queries;

use App\Models\Blog;

class GetBlogs
{
    public function __invoke($rootValue, array $args)
    {
        $query = Blog::query()
            ->with(['catalog'])
            ->orderByRaw('publish_date IS NULL')
            ->orderByDesc('publish_date')
            ->orderByDesc('created_at');

        $status = $args['status'] ?? 'published';
        if ($status) {
            $query->where('status', $status);
        }

        if (!empty($args['catalog_id'])) {
            $query->where('catalog_id', $args['catalog_id']);
        }

        if (!empty($args['search'])) {
            $search = $args['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%")
                    ->orWhere('meta_title', 'like', "%{$search}%")
                    ->orWhere('meta_description', 'like', "%{$search}%")
                    ->orWhere('keywords', 'like', "%{$search}%");
            });
        }

        $perPage = $args['first'] ?? 15;
        $page = $args['page'] ?? 1;

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'paginatorInfo' => [
                'count' => $paginator->count(),
                'currentPage' => $paginator->currentPage(),
                'firstItem' => $paginator->firstItem(),
                'hasMorePages' => $paginator->hasMorePages(),
                'lastItem' => $paginator->lastItem(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }
}
