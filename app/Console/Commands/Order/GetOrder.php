<?php

namespace App\Console\Commands\Order;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GetOrder extends Command
{
    protected $retryDelay = 1;

    protected $signature = 'orders:import';

    protected $description = 'Import orders from external API';

    protected $dateFrom = '2022-07-04';
    protected $dateTo = '2027-07-10';
    protected $page = 1;
    protected $limit = 500;
    protected $key = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';
    protected $url = "http://109.73.206.144:6969/api/orders";
    protected $chunkSize = 100;

    public function handle()
    {
        ini_set('memory_limit', '512M');

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
                            'g_number' => $item['g_number'] ?? null,
                            'date' => isset($item['date']) ? Carbon::parse($item['date']) : null,
                            'last_change_date' => isset($item['last_change_date']) ? Carbon::parse($item['last_change_date']) : null,
                            'supplier_article' => isset($item['supplier_article']) ? substr($item['supplier_article'], 0, 64) : null,
                            'tech_size' => isset($item['tech_size']) ? substr($item['tech_size'], 0, 64) : null,
                            'barcode' => isset($item['barcode']) ? (string)$item['barcode'] : null,
                            'total_price' => isset($item['total_price']) ? round((float)$item['total_price'], 2) : 0,
                            'discount_percent' => isset($item['discount_percent']) ? round((float)$item['discount_percent'], 2) : 0,
                            'warehouse_name' => isset($item['warehouse_name']) ? substr($item['warehouse_name'], 0, 64) : null,
                            'oblast' => $item['oblast'] ?? null,
                            'income_id' => isset($item['income_id']) ? (int)$item['income_id'] : 0,
                            'odid' => isset($item['odid']) ? (int)$item['odid'] : 0,
                            'nm_id' => $item['nm_id'] ?? null,
                            'subject' => isset($item['subject']) ? substr($item['subject'], 0, 64) : null,
                            'category' => isset($item['category']) ? substr($item['category'], 0, 64) : null,
                            'brand' => isset($item['brand']) ? substr($item['brand'], 0, 64) : null,
                            'is_cancel' => $item['is_cancel'] ?? false,
                            'cancel_dt' => isset($item['cancel_dt']) ? Carbon::parse($item['cancel_dt']) : null,
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

            Order::insert($records);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Ошибка при вставке чанка: " . $e->getMessage());
            throw $e;
        }
    }
}
