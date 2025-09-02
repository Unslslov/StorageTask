<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Token;
use App\Services\DataFetchingService;
use Illuminate\Console\Command;

class FetchAllData extends Command
{
    protected $signature = 'app:fetch-all-data';
    protected $description = 'Fetch data from all APIs for all accounts';

    public function handle()
    {
        $this->info('Starting data fetch process for all accounts...');

        // Получаем все аккаунты с их токенами
        $accounts = Account::with('tokens.apiService')->get();

        if ($accounts->isEmpty()) {
            $this->warn('No accounts found. Please create accounts first.');
            return;
        }

        $this->info("Found {$accounts->count()} accounts to process.");

        foreach ($accounts as $account) {
            $this->info("Processing account: {$account->name} (ID: {$account->id})");

            if ($account->tokens->isEmpty()) {
                $this->warn("No tokens found for account: {$account->name}");
                continue;
            }

            // Обрабатываем каждый токен аккаунта
            foreach ($account->tokens as $token) {
                $this->info("Processing token for service: {$token->apiService->name}");

                try {
                    switch ($token->apiService->name) {
                        case 'Wildberries API':
                            $this->fetchWildberriesData($account, $token);
                            break;
                        case 'Ozon API':
                            $this->fetchOzonData($account, $token);
                            break;
                        default:
                            $this->warn("Unknown API service: {$token->apiService->name}");
                            break;
                    }
                } catch (\Exception $e) {
                    $this->error("Error processing account {$account->name}: " . $e->getMessage());
                    Log::error("Error processing account {$account->name}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }

                // Добавляем задержку между запросами к разным API
                sleep(1);
            }
        }

        $this->info('Data fetch process completed.');
    }

    private function fetchWildberriesData(Account $account, Token $token)
    {
        $dateFrom = now()->subDay(7)->format('Y-m-d');
        $dateTo = now()->addDay(7)->format('Y-m-d');

        $this->info("Fetching Wildberries data from {$dateFrom} to {$dateTo}");

        $this->fetchData($account, $token, $dateFrom, $dateTo);
    }

    private function fetchOzonData(Account $account, Token $token)
    {
        $dateFrom = now()->subDay(7)->format('Y-m-d');
        $dateTo = now()->addDay(7)->format('Y-m-d');

        $this->info("Fetching Ozon data from {$dateFrom} to {$dateTo}");

        $this->fetchData($account, $token, $dateFrom, $dateTo);
    }

    private function fetchData(Account $account, Token $token, $dateFrom, $dateTo)
    {
        $this->call('incomes:import', [
            '--account' => $account->id,
            '--token' => $token->id,
            '--dateFrom' => $dateFrom,
            '--dateTo' => $dateTo
        ]);

        $this->call('orders:import', [
            '--account' => $account->id,
            '--token' => $token->id,
            '--dateFrom' => $dateFrom,
            '--dateTo' => $dateTo
        ]);

        $this->call('sales:import', [
            '--account' => $account->id,
            '--token' => $token->id,
            '--dateFrom' => $dateFrom,
            '--dateTo' => $dateTo
        ]);

        $this->call('stocks:import', [
            '--account' => $account->id,
            '--token' => $token->id,
            '--dateFrom' => now()->format('Y-m-d'),
            '--dateTo' => $dateTo
        ]);
    }
}
