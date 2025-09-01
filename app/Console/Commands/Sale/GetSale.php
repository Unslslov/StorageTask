<?php

namespace App\Console\Commands\Sale;

use App\Models\Account;
use App\Models\Sale;
use App\Models\Token;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GetSale extends Command
{
    protected $retryDelay = 1;
    protected $signature = 'sales:import {--account=} {--token=} {--dateFrom=} {--dateTo=}';

    protected $description = 'Импорт sales с внешнего API';

    protected $url = "http://109.73.206.144:6969/api/sales";
    protected $chunkSize = 100;

    public function handle()
    {
        $accountId = $this->option('account');
        $tokenId = $this->option('token');
        $dateFrom = $this->option('dateFrom') ?? '2025-07-04';
        $dateTo = $this->option('dateTo') ?? now()->format('Y-m-d');

        $page = 1;
        $limit = 500;

        $account = Account::find($accountId);
        $token = Token::with('apiService')->find($tokenId);

        if (!$account || !$token) {
            $this->error('Аккаунт или токен не найден');
            return 1;
        }

        $this->info("Начало импорта sale для аккаунта: {$account->name}");

//        ini_set('memory_limit', '512M');

        try {
            do {
                $params = $this->buildRequestParams($token, [
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'page' => $page,
                    'limit' => $limit
                ]);

                $response = Http::get($this->url, $params);

                if ($response->status() === 429) {
                    $this->retryDelay *= 2;
                    $this->warn("Rate limit exceeded. Retrying in {$this->retryDelay} seconds...");
                    sleep($this->retryDelay);
                    continue;
                }

                $this->retryDelay = 1;

                if(!$response->successful()) {
                    throw new \Exception("API error: " . $response->body());
                }

                $data = $response->json('data');

                if (empty($data)) {
                    $this->info("No data to import on page {$page}");
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
                            'account_id' => $account->id ?? null
                        ];

                        if(count($records) >= $this->chunkSize) {
                            $this->insertChunk($records);
                            $processedCount += count($records);
                            $records = [];
                        }
                    } catch (\Exception $e) {
                        $this->info("Error processing item: " . $e->getMessage());
                        continue;
                    }
                }

                if (!empty($records)) {
                    $this->insertChunk($records);
                    $processedCount += count($records);
                }

                $this->info("Processed page {$page}: {$processedCount} records");
                $page++;
            } while (count($data) === $limit);

            $this->info("Import completed successfully for account: {$account->name}");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function buildRequestParams($token, $params)
    {
        switch ($token->tokenType->name) {
            case 'api-key':
                $params['key'] = $token->value;
                break;
            case 'bearer':
                // Для bearer токена нужно добавить заголовок Authorization
                // Это можно сделать через Http::withToken()
                // Но в данном случае мы передаем параметры в URL
                $params['Authorization'] = 'Bearer ' . $token->value;
                break;
            case 'login-password':
                $meta = $token->meta ?? [];
                $params['login'] = $meta['login'] ?? '';
                $params['password'] = $meta['password'] ?? '';
                break;
        }

        return $params;
    }


    protected function insertChunk(array $records)
    {
        DB::beginTransaction();
        try {

            Sale::insert($records);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Ошибка при вставке чанка: " . $e->getMessage());
            throw $e;
        }
    }
}
