<?php

namespace App\Console\Commands\Income;

use App\Console\Commands\BaseImportCommand;
use App\DataTransferObjects\IncomeDto;
use App\Models\Income;

class GetIncome extends BaseImportCommand
{
    protected $signature = 'incomes:import
        {--account= : ID аккаунта}
        {--token= : ID токена}
        {--dateFrom= : Дата начала}
        {--dateTo= : Дата окончания}';

    protected $description = 'Импорт incomes с внешнего API';
    protected $url = "http://109.73.206.144:6969/api/incomes";

    protected function getDtoClass(): string
    {
        return IncomeDto::class;
    }

    protected function getModelClass(): string
    {
        return Income::class;
    }

    protected function getImportType(): string
    {
        return 'incomes';
    }
}
