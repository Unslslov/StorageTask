<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Token;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

abstract class BaseImportCommand extends Command
{
    protected $retryDelay = 1;
    protected $chunkSize = 100;
    protected $url;

    protected $signature = 'import:base
        {--account= : ID аккаунта}
        {--token= : ID токена}
        {--dateFrom= : Дата начала}
        {--dateTo= : Дата окончания}';

    protected $description = 'Базовая команда для импорта данных';

    abstract protected function getDtoClass(): string;
    abstract protected function getModelClass(): string;
    abstract protected function getImportType(): string;

    public function handle()
    {
        $accountId = $this->option('account');
        $tokenId = $this->option('token');
        $dateFrom = $this->option('dateFrom');
        $dateTo = $this->option('dateTo');

        $page = 1;
        $limit = 500;

        $account = Account::find($accountId);
        $token = Token::with('apiService')->find($tokenId);

        if (!$account || !$token) {
            $this->error('Аккаунт или токен не найден');
            return 1;
        }

        $importType = $this->getImportType();
        $this->info("Начало импорта {$importType} для аккаунта: {$account->name}");

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
                    $this->handleRateLimit();
                    continue;
                }

                $this->retryDelay = 1;

                if (!$response->successful()) {
                    throw new \Exception("Ошибка API: " . $response->body());
                }

                $data = $response->json('data');

                if (empty($data)) {
                    $this->info("Нет данных для импорта на странице {$page}");
                    break;
                }

                $processedCount = $this->processDataChunk($data, $account->id);
                $this->info("Обработана страница {$page}: записей {$processedCount}");
                $page++;
            } while (count($data) === $limit);

            $this->info("Импорт {$importType} успешно завершен для аккаунта: {$account->name}");
        } catch (\Exception $e) {
            $this->error("Ошибка: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function handleRateLimit(): void
    {
        $this->retryDelay *= 2;
        $this->warn("Превышен лимит скорости. Повторная попытка через {$this->retryDelay} секунд...");
        sleep($this->retryDelay);
    }

    protected function processDataChunk(array $data, int $accountId): int
    {
        $records = [];
        $dtoClass = $this->getDtoClass();

        foreach ($data as $item) {
            try {
                $dto = $dtoClass::fromArray($item, $accountId);
                $records[] = $dto->toArray();

                if (count($records) >= $this->chunkSize) {
                    $this->insertChunk($records);
                    $records = [];
                }
            } catch (\Exception $e) {
                $this->info("Ошибка обработки элемента: " . $e->getMessage());
                continue;
            }
        }

        if (!empty($records)) {
            $this->insertChunk($records);
        }

        return count($data);
    }

    protected function buildRequestParams($token, $params): array
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

    protected function insertChunk(array $records): void
    {
        DB::beginTransaction();
        try {
            $modelClass = $this->getModelClass();
            $modelClass::insert($records);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Ошибка при вставке чанка: " . $e->getMessage());
            throw $e;
        }
    }
}
