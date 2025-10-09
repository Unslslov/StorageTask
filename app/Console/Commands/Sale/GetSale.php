<?php

namespace App\Console\Commands\Sale;

use App\Console\Commands\BaseImportCommand;
use App\DataTransferObjects\SaleDto;
use App\Models\Sale;


class GetSale extends BaseImportCommand
{
    protected $signature = 'sales:import
        {--account= : ID аккаунта}
        {--token= : ID токена}
        {--dateFrom= : Дата начала}
        {--dateTo= : Дата окончания}';

    protected $description = 'Импорт sales с внешнего API';
    protected $url = "http://109.73.206.144:6969/api/sales";

    protected function getDtoClass(): string
    {
        return SaleDto::class;
    }

    protected function getModelClass(): string
    {
        return Sale::class;
    }

    protected function getImportType(): string
    {
        return 'sales';
    }
}
