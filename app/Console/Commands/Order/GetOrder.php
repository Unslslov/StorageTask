<?php

namespace App\Console\Commands\Order;

use App\DataTransferObjects\OrderDto;
use App\Models\Account;
use App\Models\Income;
use App\Models\Order;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GetOrder extends Command
{
    protected $retryDelay = 1;
    protected $signature = 'orders:import {--account=} {--token=} {--dateFrom=} {--dateTo=}';
    protected $description = 'Импорт orders с внешнего API';

    protected $url = "http://109.73.206.144:6969/api/orders";
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
            return;
        }

        $this->info("Начало импорта order для аккаунта: {$account->name}");

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
                    $this->warn("Превышен лимит скорости. Повторная попытка через {$this->retryDelay} секунд...");
                    sleep($this->retryDelay);
                    continue;
                }

                $this->retryDelay = 1;

                if(!$response->successful()) {
                    throw new \Exception("ошибка API: " . $response->body());
                }

                $data = $response->json('data');

                if (empty($data)) {
                    $this->info("Нет данных для импорта на страницу {$page}");
                    break;
                }

                $records = [];
                $processedCount = 0;

                foreach ($data as $item) {
                    try {
                        $dto = OrderDto::fromArray($item, $account->id);
                        $records[] = $dto->toArray();

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

            // Используем updateOrCreate для предотвращения дубликатов
//            foreach ($records as $record) {
//                Income::updateOrCreate(
//                    [
//                        'income_id' => $record['income_id'],
//                        'account_id' => $record['account_id']
//                    ],
//                    $record
//                );
//            }

            Order::insert($records);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Ошибка при вставке чанка: " . $e->getMessage());
            throw $e;
        }
    }
}
