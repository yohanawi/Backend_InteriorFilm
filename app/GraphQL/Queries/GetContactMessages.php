<?php

namespace App\GraphQL\Queries;

use App\Models\ContactMessage;

class GetContactMessages
{
    /**
     * Get paginated contact messages with optional filters.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $query = ContactMessage::query()->latest();

        // Filter by status
        if (isset($args['status']) && $args['status'] !== 'all') {
            $query->where('status', $args['status']);
        }

        // Search functionality
        if (isset($args['search']) && !empty($args['search'])) {
            $search = $args['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $perPage = $args['first'] ?? 15;
        $page = $args['page'] ?? 1;

        $contacts = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $contacts->items(),
            'paginatorInfo' => [
                'count' => count($contacts->items()),
                'currentPage' => $contacts->currentPage(),
                'firstItem' => $contacts->firstItem(),
                'hasMorePages' => $contacts->hasMorePages(),
                'lastItem' => $contacts->lastItem(),
                'lastPage' => $contacts->lastPage(),
                'perPage' => $contacts->perPage(),
                'total' => $contacts->total(),
            ],
        ];
    }
}
