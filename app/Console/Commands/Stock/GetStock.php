<?php

namespace App\Console\Commands\Stock;

use App\Console\Commands\BaseImportCommand;
use App\DataTransferObjects\StockDto;
use App\Models\Stock;

class GetStock extends BaseImportCommand
{
    protected $signature = 'stocks:import
        {--account= : ID аккаунта}
        {--token= : ID токена}
        {--dateFrom= : Дата начала}
        {--dateTo= : Дата окончания}';

    protected $description = 'Импорт stocks с внешнего API';
    protected $url = "http://109.73.206.144:6969/api/stocks";


    protected function getDtoClass(): string
    {
        return StockDto::class;
    }

    protected function getModelClass(): string
    {
        return Stock::class;
    }

    protected function getImportType(): string
    {
        return 'stocks';
    }
}
