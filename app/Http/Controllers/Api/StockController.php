<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\DateFrom;
use App\Http\Requests\FilterRequest;
use App\Http\Resources\StockResource;
use App\Models\Stock;
use App\Services\PaginationService;
use App\Services\StokeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Pipeline\Pipeline;

class StockController extends Controller
{
    protected StokeService $stokeService;

    public function __construct(PaginationService $paginationService, StokeService $stokeService)
    {
        parent::__construct($paginationService);
        $this->stokeService = $stokeService;
    }

    public function index(FilterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dateFrom = $request->query('dateFrom');

        $stokes = $this->stokeService->getFilteredStokes($dateFrom);

        return $this->paginate(
            $stokes,
            $data,
            StockResource::class
        );
    }
}
