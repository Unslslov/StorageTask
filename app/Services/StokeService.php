<?php

namespace App\Services;

use App\Http\Filters\DateFrom;
use App\Http\Filters\DateTo;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

class StokeService
{
    public function getFilteredStokes($dateFrom): Builder
    {
        $query = Stock::query();

        return app(Pipeline::class)
        ->send($query)
        ->through([
            new DateFrom($dateFrom)
        ])
        ->thenReturn();
    }
}
