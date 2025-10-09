<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\ApiService;
use App\Models\Token;
use App\Models\TokenType;
use Illuminate\Console\Command;

class TokenCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавление нового токена';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Adding a new token...');

        // Выводим список аккаунтов для выбора
        $accounts = Account::with('company')->get();

        if ($accounts->isEmpty()) {
            $this->error('No accounts found. Please create an account first.');
            return 1;
        }

        $accountOptions = [];
        foreach ($accounts as $account) {
            $accountOptions[$account->id] = "{$account->company->name} - {$account->name}";
        }

        $selectedAccount = $this->choice('Select account', $accountOptions);
        $accountId = array_search($selectedAccount, $accountOptions);

        // Выводим список API сервисов для выбора
        $apiServices = ApiService::all();

        if ($apiServices->isEmpty()) {
            $this->error('No API services found. Please create an API service first.');
            return 1;
        }

        $apiServiceOptions = $apiServices->pluck('name', 'id')->toArray();
        $selectedApiService = $this->choice('Select API service', $apiServiceOptions);

        // Получаем разрешенные типы токенов для выбранного API сервиса
        $apiServiceId = array_search($selectedApiService, $apiServiceOptions);

        $apiService = ApiService::find($apiServiceId);
        $allowedTokenTypes = $apiService->allowed_token_types;

        $tokenTypes = TokenType::whereIn('name', $allowedTokenTypes)->get();

        if ($tokenTypes->isEmpty()) {
            $this->error('No allowed token types found for this API service.');
            return 1;
        }

        $tokenTypeOptions = $tokenTypes->pluck('name', 'id')->toArray();
        $selectedTokenType = $this->choice('Выберите тип токена', $tokenTypeOptions);

        $tokenTypeId = array_search($selectedTokenType, $tokenTypeOptions);
        $tokenType = TokenType::find($tokenTypeId);

        $value = $this->ask("Enter {$tokenType->name} value");

        // Для типов токенов, требующих дополнительных данных
        $meta = null;
        if ($tokenType->name === 'login-password') {
            $login = $this->ask('Enter login');
            $password = $this->ask('Enter password');
            $meta = ['login' => $login, 'password' => $password];
        }

        try {
            $token = Token::create([
                'account_id' => $accountId,
                'api_service_id' => $apiServiceId,
                'token_type_id' => $tokenTypeId,
                'value' => $value,
                'meta' => $meta
            ]);

            $this->info("Token created successfully!");
            $this->info("ID: {$token->id}");
            $this->info("Account: {$accounts->find($accountId)->company->name} - {$accounts->find($accountId)->name}");
            $this->info("API Service: {$apiService->name}");
            $this->info("Token Type: {$tokenType->name}");
            $this->info("Value: {$value}");

            if ($meta) {
                $this->info("Login: {$meta['login']}");
                $this->info("Password: {$meta['password']}");
            }

        } catch (\Exception $e) {
            $this->error("Error creating token: " . $e->getMessage());
            return;
        }

        return 0;
    }
}
