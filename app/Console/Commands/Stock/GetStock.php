<?php

namespace App\Console\Commands\Stock;

use App\Models\Account;
use App\Models\Stock;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class GetStock extends Command
{
    protected $retryDelay = 1;
    protected $signature = 'stocks:import {--account=} {--token=} {--dateFrom=} {--dateTo=}';
    protected $description = 'Импорт stocks с внешнего API';

    protected $url = "http://109.73.206.144:6969/api/stocks";
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

        $this->info("Начало импорта stock для аккаунта: {$account->name}");

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

            Stock::insert($records);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Ошибка при вставке чанка: " . $e->getMessage());
            throw $e;
        }
    }
}
