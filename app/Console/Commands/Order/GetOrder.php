<?php

namespace App\Console\Commands\Order;

use App\Console\Commands\BaseImportCommand;
use App\DataTransferObjects\OrderDto;
use App\Models\Order;
use Carbon\Carbon;

class GetOrder extends BaseImportCommand
{
    protected $signature = 'orders:import
        {--account= : ID аккаунта}
        {--token= : ID токена}
        {--dateFrom= : Дата начала}
        {--dateTo= : Дата окончания}';

    protected $description = 'Импорт orders с внешнего API';
    protected $url = "http://109.73.206.144:6969/api/orders";

    public function handle()
    {
        return parent::handle();
    }

    protected function getDtoClass(): string
    {
        return OrderDto::class;
    }

    protected function getModelClass(): string
    {
        return Order::class;
    }

    protected function getImportType(): string
    {
        return 'orders';
    }
}
