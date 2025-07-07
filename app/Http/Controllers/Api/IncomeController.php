<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Http\Resources\IncomeResource;

use App\Services\IncomeService;
use App\Services\PaginationService;

class IncomeController extends Controller
{
    protected IncomeService $incomeService;

    public function __construct(PaginationService $paginationService, IncomeService $incomeService)
    {
        parent::__construct($paginationService);
        $this->incomeService = $incomeService;
    }

    public function index(FilterRequest $request)
    {
        $data = $request->validated();

        [$dateFrom, $dateTo] = $this->getDatesFromRequest($data);

        $incomes = $this->incomeService->getFilteredIncomes($dateFrom, $dateTo);

        return $this->paginate(
            $incomes,
            $data,
            IncomeResource::class
        );
    }
}
