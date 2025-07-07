<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\DateFrom;
use App\Http\Filters\DateTo;
use App\Http\Requests\FilterRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\OrderService;
use App\Services\PaginationService;
use App\Services\SaleService;
use Illuminate\Pipeline\Pipeline;

class SaleController extends Controller
{
    protected SaleService $saleService;

    public function __construct(PaginationService $paginationService, SaleService $saleService)
    {
        parent::__construct($paginationService);
        $this->saleService = $saleService;
    }

    public function index(FilterRequest $request)
    {
        $data = $request->validated();

        [$dateFrom, $dateTo] = $this->getDatesFromRequest($data);

        $sales = $this->saleService->getFilteredSales($dateFrom, $dateTo);

        return $this->paginate(
            $sales,
            $data,
            SaleResource::class
        );
    }
}
