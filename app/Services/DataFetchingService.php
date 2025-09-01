<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Income;

class DataFetchingService
{
    /**
     * Maximum retries for API requests
     */
    const MAX_RETRIES = 3;

    /**
     * Delay between retries in seconds
     */
    const RETRY_DELAY = 5;

    /**
     * Fetch sales data from API
     */
    public function fetchSales($account, $token, $dateFrom, $dateTo)
    {
        $this->fetchData(
            $account,
            $token,
            'sales',
            '/api/sales',
            [
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ],
            Sale::class
        );
    }

    /**
     * Fetch orders data from API
     */
    public function fetchOrders($account, $token, $dateFrom, $dateTo)
    {
        $this->fetchData(
            $account,
            $token,
            'orders',
            '/api/orders',
            [
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ],
            Order::class
        );
    }

    /**
     * Fetch stocks data from API
     */
    public function fetchStocks($account, $token, $dateFrom)
    {
        $this->fetchData(
            $account,
            $token,
            'stocks',
            '/api/stocks',
            [
                'dateFrom' => $dateFrom
            ],
            Stock::class
        );
    }

    /**
     * Fetch incomes data from API
     */
    public function fetchIncomes($account, $token, $dateFrom, $dateTo)
    {
        $this->fetchData(
            $account,
            $token,
            'incomes',
            '/api/incomes',
            [
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ],
            Income::class
        );
    }

    /**
     * Generic method to fetch data from API
     */
    private function fetchData($account, $token, $dataType, $endpoint, $params, $modelClass)
    {
        $retryCount = 0;

        while ($retryCount < self::MAX_RETRIES) {
            try {
                // Формируем URL с учетом типа аутентификации
                $url = $this->buildUrl($token, $endpoint, $params);

                Log::info("Fetching {$dataType} for account {$account->id}", [
                    'url' => $url,
                    'account_id' => $account->id
                ]);

                // Отправляем запрос к API
                $response = Http::timeout(30)->get($url);

                // Обрабатываем ответ
                if ($response->successful()) {
                    $data = $response->json();

                    // Сохраняем данные в базу
                    $this->saveData($account, $data, $dataType, $modelClass);

                    Log::info("Successfully fetched {$dataType} for account {$account->id}", [
                        'count' => count($data['data'] ?? []),
                        'account_id' => $account->id
                    ]);

                    return;
                } elseif ($response->status() === 429) {
                    // Too Many Requests - повторяем попытку после задержки
                    $retryCount++;
                    $delay = self::RETRY_DELAY * $retryCount;

                    Log::warning("Rate limit exceeded for {$dataType}. Retrying in {$delay} seconds.", [
                        'account_id' => $account->id,
                        'retry' => $retryCount
                    ]);

                    sleep($delay);
                } else {
                    // Другие ошибки - логируем и прерываем попытку
                    Log::error("Error fetching {$dataType} for account {$account->id}", [
                        'status' => $response->status(),
                        'response' => $response->body(),
                        'account_id' => $account->id
                    ]);

                    return;
                }
            } catch (\Exception $e) {
                $retryCount++;

                Log::error("Exception fetching {$dataType} for account {$account->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'retry' => $retryCount,
                    'account_id' => $account->id
                ]);

                if ($retryCount >= self::MAX_RETRIES) {
                    return;
                }

                sleep(self::RETRY_DELAY * $retryCount);
            }
        }
    }

    /**
     * Build URL with authentication parameters
     */
    private function buildUrl($token, $endpoint, $params)
    {
        $baseUrl = 'http://109.73.206.144:6969/';

        // Добавляем параметры аутентификации в зависимости от типа токена
        switch ($token->tokenType->name) {
            case 'api-key':
                $params['key'] = $token->value;
                break;
            case 'bearer':
                // Для bearer токена добавляем заголовок Authorization
                // В этом примере мы добавляем его как параметр, но в реальности
                // нужно использовать withHeaders() в HTTP-запросе
                $params['Authorization'] = 'Bearer ' . $token->value;
                break;
            case 'login-password':
                // Для логина и пароля добавляем оба параметра
                $meta = $token->meta ?? [];
                $params['login'] = $meta['login'] ?? '';
                $params['password'] = $meta['password'] ?? '';
                break;
        }

        // Формируем URL с параметрами
        return $baseUrl . $endpoint . '?' . http_build_query($params);
    }

    /**
     * Save fetched data to database
     */
    private function saveData($account, $data, $dataType, $modelClass)
    {
        if (!isset($data['data']) || empty($data['data'])) {
            return;
        }

        foreach ($data['data'] as $item) {
            // Добавляем account_id к данным
            $item['account_id'] = $account->id;

            // Определяем уникальные поля для обновления существующих записей
            $uniqueFields = $this->getUniqueFields($dataType);

            // Создаем или обновляем запись
            if (!empty($uniqueFields)) {
                $conditions = [];
                foreach ($uniqueFields as $field) {
                    if (isset($item[$field])) {
                        $conditions[$field] = $item[$field];
                    }
                }

                if (!empty($conditions)) {
                    $modelClass::updateOrCreate($conditions, $item);
                } else {
                    $modelClass::create($item);
                }
            } else {
                $modelClass::create($item);
            }
        }
    }

    /**
     * Get unique fields for each data type
     */
    private function getUniqueFields($dataType)
    {
        switch ($dataType) {
            case 'sales':
                return ['sale_id']; // Замените на реальное поле
            case 'orders':
                return ['order_id']; // Замените на реальное поле
            case 'stocks':
                return ['stock_id']; // Замените на реальное поле
            case 'incomes':
                return ['income_id']; // Замените на реальное поле
            default:
                return [];
        }
    }
}
