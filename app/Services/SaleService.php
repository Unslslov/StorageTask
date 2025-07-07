<?php

namespace App\Services;

use App\Http\Filters\DateFrom;
use App\Http\Filters\DateTo;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

class SaleService
{
    public function getFilteredSales($dateFrom, $dateTo): Builder
    {
        $query = Sale::query();

        return app(Pipeline::class)
            ->send($query)
            ->through([
                new DateFrom($dateFrom),
                new DateTo($dateTo),
            ])
            ->thenReturn();
    }
}
