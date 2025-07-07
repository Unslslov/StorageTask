<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class DateTo
{
    protected $dateTo;

    public function __construct($dateTo)
    {
        $this->dateTo = $dateTo;
    }

    public function handle(Builder $builder, \Closure $next)
    {
        if($this->dateTo)
        {
            $builder->whereDate('date', '<=', $this->dateTo);
        }

        return $next($builder);
    }
}
