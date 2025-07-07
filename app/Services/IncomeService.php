<?php

namespace App\Services;

use App\Http\Filters\DateFrom;
use App\Http\Filters\DateTo;
use App\Models\Income;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

class IncomeService
{
    public function getFilteredIncomes($dateFrom, $dateTo): Builder
    {
        $query = Income::query();

        return app(Pipeline::class)
            ->send($query)
            ->through([
                new DateFrom($dateFrom),
                new DateTo($dateTo),
            ])
            ->thenReturn();
    }
}
