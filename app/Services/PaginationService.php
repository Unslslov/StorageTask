<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class PaginationService
{
    public function paginate(Builder $query, array $params, $resourceClass = null): JsonResponse
    {
        $perPage = min($params['limit'] ?? 500, 500);
        $page = $params['page'] ?? 1;

        $results = $query->paginate($perPage, ['*'], 'page', $page);

        $items = $results->items();
        $data = $resourceClass ? $resourceClass::collection($items) : $items;

        return response()->json([
            'data' => $data,
            'pagination' => [
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'last_page' => $results->lastPage(),
            ]
        ]);
    }
}
