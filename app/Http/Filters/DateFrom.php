<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class DateFrom
{
    protected $dateFrom;

    public function __construct($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    public function handle(Builder $builder, \Closure $next)
    {
        if($this->dateFrom)
        {
            $builder->whereDate('date', '>=', $this->dateFrom);
        }

        return $next($builder);
    }
}
