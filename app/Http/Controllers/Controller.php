<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Services\DateService;
use App\Services\PaginationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected PaginationService $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    protected function getDatesFromRequest(array $data): array
    {
        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];

        return [$dateFrom, $dateTo];
    }

    protected function paginate($query, array $params, $resourceClass = null)
    {
        $data = $this->paginationService->paginate($query, $params, $resourceClass);

        return $data;
    }
}
