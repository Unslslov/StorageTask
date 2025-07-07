<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\DateFrom;
use App\Http\Filters\DateTo;
use App\Http\Requests\FilterRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaginationService;
use Illuminate\Pipeline\Pipeline;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(PaginationService $paginationService, OrderService $orderService)
    {
        parent::__construct($paginationService);
        $this->orderService = $orderService;
    }

    public function index(FilterRequest $request)
    {
        $data = $request->validated();

        [$dateFrom, $dateTo] = $this->getDatesFromRequest($data);

        $orders = $this->orderService->getFilteredOrders($dateFrom, $dateTo);

        return $this->paginate(
            $orders,
            $data,
            OrderResource::class
        );
    }
}
