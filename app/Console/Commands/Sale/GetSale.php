<?php

namespace App\Console\Commands\Sale;

use App\Models\Sale;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GetSale extends Command
{
    protected $retryDelay = 1;
    protected $signature = 'sales:import';

    protected $description = 'Import sales from external API';

    protected $dateFrom = '2022-07-04';
    protected $dateTo = '2027-07-10';
    protected $page = 1;
    protected $limit = 500;
    protected $key = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';
    protected $url = "http://109.73.206.144:6969/api/sales";
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
                            'sale_id' => $item['sale_id'] ?? null,
                            'g_number' => $item['g_number'] ?? null,
                            'date' => $item['date'] ?? null,
                            'last_change_date' => $item['last_change_date'] ?? null,
                            'supplier_article' => $item['supplier_article'] ?? null,
                            'tech_size' => $item['tech_size'] ?? null,
                            'barcode' => isset($item['barcode']) ? (string)$item['barcode'] : null,
                            'total_price' => isset($item['total_price']) ? round((float)$item['total_price'], 2) : 0,
                            'discount_percent' => isset($item['discount_percent']) ? round((float)$item['discount_percent'], 2) : 0,
                            'is_supply' => $item['is_supply'] ?? false,
                            'is_realization' => $item['is_realization'] ?? false,
                            'promo_code_discount' => isset($item['promo_code_discount']) ? round((float)$item['promo_code_discount'], 2) : null,
                            'warehouse_name' => $item['warehouse_name'] ?? null,
                            'country_name' => $item['country_name'] ?? null,
                            'oblast_okrug_name' => $item['oblast_okrug_name'] ?? null,
                            'region_name' => $item['region_name'] ?? null,
                            'income_id' => $item['income_id'] ?? null,
                            'odid' => $item['odid'] ?? null,
                            'spp' => isset($item['spp']) ? round((float)$item['spp'], 2) : 0,
                            'for_pay' => isset($item['for_pay']) ? round((float)$item['for_pay'], 2) : 0,
                            'finished_price' => isset($item['finished_price']) ? round((float)$item['finished_price'], 2) : 0,
                            'price_with_disc' => isset($item['price_with_disc']) ? round((float)$item['price_with_disc'], 2) : 0,
                            'nm_id' => $item['nm_id'],
                            'subject' => $item['subject'] ?? null,
                            'category' => $item['category'] ?? null,
                            'brand' => $item['brand'] ?? null,
                            'is_storno' => $item['is_storno'] ?? null,
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

            Sale::insert($records);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Ошибка при вставке чанка: " . $e->getMessage());
            throw $e;
        }
    }
}
