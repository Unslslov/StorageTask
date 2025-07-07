<?php

namespace App\Console\Commands\Stock;

use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class GetStock extends Command
{
    protected $retryDelay = 1;
    protected $signature = 'stocks:import';

    protected $description = 'Import stocks from external API';

    protected $dateFrom = '2025-07-07';
    protected $dateTo = '2025-07-10';
    protected $page = 1;
    protected $limit = 500;
    protected $key = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';
    protected $url = "http://109.73.206.144:6969/api/stocks";
    protected $chunkSize = 100;

    public function handle()
    {
        $this->info("Начало импорта данных за период {$this->dateFrom} — {$this->dateTo}");

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

                if (!$response->successful()) {
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
                            'date' => $item['date'] ?? null,
                            'last_change_date' => $item['last_change_date'] ?? null,
                            'supplier_article' => isset($item['supplier_article']) ? (string)$item['supplier_article'] : null,
                            'tech_size' => isset($item['tech_size']) ? (string)$item['tech_size'] : null,
                            'barcode' => isset($item['barcode']) ? (string)$item['barcode'] : null,
                            'quantity' => $item['quantity'] ?? null,
                            'is_supply' => $item['is_supply'] ?? null,
                            'is_realization' => $item['is_realization'] ?? null,
                            'quantity_full' => $item['quantity_full'] ?? null,
                            'warehouse_name' => $item['warehouse_name'] ?? null,
                            'in_way_to_client' => $item['in_way_to_client'] ?? null,
                            'in_way_from_client' => $item['in_way_from_client'] ?? null,
                            'nm_id' => $item['nm_id'] ?? null,
                            'subject' => isset($item['subject']) ? (string)$item['subject'] : null,
                            'category' => isset($item['category']) ? (string)$item['category'] : null,
                            'brand' => isset($item['brand']) ? (string)$item['brand'] : null,
                            'sc_code' => isset($item['sc_code']) ? (string)$item['sc_code'] : null,
                            'price' => isset($item['price']) ? round((float)$item['price'], 2) : null,
                            'discount' => isset($item['discount']) ? round((float)$item['discount'], 2) : null,
                        ];

                        if (count($records) >= $this->chunkSize) {
                            $this->insertChunk($records);
                            $processedCount += count($records);
                            $records = [];
                        }
                    } catch (\Exception $e) {
                        $this->error("Ошибка при обработке элемента: " . $e->getMessage());
                        continue;
                    }
                }

                if (!empty($records)) {
                    $this->insertChunk($records);
                    $processedCount += count($records);
                }

                $this->info("Обработано страница {$this->page}: {$processedCount} записей");
                $this->page++;

            } while (count($data) === $this->limit);

            $this->info("Импорт завершен успешно!");
        } catch (\Exception $e) {
            $this->error("Ошибка: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Вставка чанка данных с обработкой ошибок
     */
    protected function insertChunk(array $records)
    {
        try {
            DB::beginTransaction();

            Stock::insert($records);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Ошибка при вставке чанка: " . $e->getMessage());
            throw $e;
        }
    }
}
