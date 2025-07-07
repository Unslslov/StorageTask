<?php

namespace App\Console\Commands\Income;

use App\Models\Income;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GetIncome extends Command
{
    protected $retryDelay = 1;
    protected $signature = 'incomes:import';
    protected $description = 'Import incomes from external API';

    protected $dateFrom = '2022-07-04';
    protected $dateTo = '2027-07-10';
    protected $page = 1;
    protected $limit = 500;
    protected $key = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';
    protected $url = "http://109.73.206.144:6969/api/incomes";
    protected $chunkSize = 100;

    public function handle()
    {
        $this->info("Начало импорта данных за период {$this->dateFrom} - {$this->dateTo}");

        try {
            do {
                $response = Http::get($this->url, [
                    'dateFrom' => $this->dateFrom,
                    'dateTo' => $this->dateTo,
                    'page' => $this->page,
                    'key' => $this->key,
                    'limit' => $this->limit
                ]);

                if ($response->status() === 429) {
                    $this->retryDelay *= 2;
                    sleep($this->retryDelay);
                    continue;
                }

                $this->retryDelay = 1;

                if(!$response->successful())
                {
                    throw new \Exception("Ошибка API: " . $response->body());
                }

                $data = $response->json('data');

                if (empty($data)) {
                    $this->info("Нет данных для импорта на странице {$this->page}");
                    break;
                }

                $records = [];
                $processedCount = 0;

                foreach ($data as $item) {
                    try {
                        $records[] = [
                            'income_id' => $item['income_id'] ?? null,
                            'number' => isset($item['number']) ? (string)$item['number'] : null,
                            'date' => $item['date'] ?? null,
                            'last_change_date' => $item['last_change_date'] ?? null,
                            'supplier_article' => isset($item['supplier_article']) ? (string)$item['supplier_article'] : null,
                            'tech_size' => isset($item['tech_size']) ? (string)$item['tech_size'] : null,
                            'barcode' => isset($item['barcode']) ? (string)$item['barcode'] : null,
                            'quantity' => $item['quantity'] ?? null,
                            'total_price' => isset($item['total_price']) ? round((float)$item['total_price'], 2) : 0.00, // Новое поле, обязательное
                            'date_close' => $item['date_close'] ?? null,
                            'warehouse_name' => $item['warehouse_name'] ?? null,
                            'nm_id' => $item['nm_id'] ?? null
                            ];

                        if(count($records) >= $this->chunkSize) {
                            $this->insertChunk($records);
                            $processedCount += count($records);
                            $records = [];
                        }
                    } catch (\Exception $e)
                    {
                        $this->info("Ошибка при обработке элемента: " . $e->getMessage());
                        continue;
                    }

                }
                if (!empty($records))
                {
                    $this->insertChunk($records);
                    $processedCount += count($records);
                }

                $this->info("Обработано страница {$this->page}: {$processedCount} записей");
                $this->page++;
            }
            while (count($data) === $this->limit);

            $this->info("Иморт завершен успешно");
        } catch (\Exception $e)
        {
            $this->error("Ошибка: " . $e->getMessage());

            return 1;
        }

        return 0;
    }

    protected function insertChunk(array $records)
    {
        try {
            DB::beginTransaction();

            Income::insert($records);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Ошибка при вставке чанка: " . $e->getMessage());
            throw $e;
        }
    }
}
