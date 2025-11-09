<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait PaginationTrait
{
    public function paginate($items, int $perPage = 15, ?int $page = null): LengthAwarePaginator
    {
        if ($items instanceof \Illuminate\Support\Collection) {
            $items = $items->toArray();
        }

        $page = $page ?? request('page', 1);
        $total = count($items);
        $lastPage = (int) ceil($total / $perPage);

        if ($page > $lastPage && $total > 0) {
            $page = $lastPage;
        }

        $paginatedItems = array_values(array_slice($items, ($page - 1) * $perPage, $perPage));

        return new LengthAwarePaginator(
            $paginatedItems,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    public function getPaginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
