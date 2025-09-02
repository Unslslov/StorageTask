<?php

namespace App\Console\Commands;

use App\Models\ApiService;
use App\Models\TokenType;
use Illuminate\Console\Command;

class ApiServiceCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-service:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавление нового API сервиса';

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
        $this->info('Adding a new API service...');

        $name = $this->ask('API service name (e.g., Wildberries API, Ozon API)');

        // Выводим список типов токенов для выбора
        $tokenTypes = TokenType::all();

        if ($tokenTypes->isEmpty()) {
            $this->error('No token types found. Please create token types first.');
            return 1;
        }

        $tokenTypeOptions = $tokenTypes->pluck('name', 'id')->toArray();
        $allowedTokenTypes = $this->choice(
            'Select allowed token types (comma separated)',
            $tokenTypeOptions,
            null,
            null,
            true
        );

        try {
            $apiService = ApiService::create([
                'name' => $name,
                'allowed_token_types' => $allowedTokenTypes
            ]);

            $this->info("API service created successfully!");
            $this->info("ID: {$apiService->id}");
            $this->info("Name: {$apiService->name}");
            $this->info("Allowed token types: " . implode(', ', $allowedTokenTypes));

        } catch (\Exception $e) {
            $this->error("Error creating API service: " . $e->getMessage());
            return 1;
        }


        return 0;
    }
}
