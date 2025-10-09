<?php

namespace App\Console\Commands;

use App\Models\TokenType;
use Illuminate\Console\Command;

class TokenTypeCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token-type:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавление нового типа токена';

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
        $this->info('Adding a new token type...');

        $name = $this->ask('Token type name (e.g., api-key, bearer, login-password)');
        $description = $this->ask('Token type description');

        try {
            $tokenType = TokenType::create([
                'name' => $name,
                'description' => $description
            ]);

            $this->info("Token type created successfully!");
            $this->info("ID: {$tokenType->id}");
            $this->info("Name: {$tokenType->name}");
            $this->info("Description: {$tokenType->description}");

        } catch (\Exception $e) {
            $this->error("Error creating token type: " . $e->getMessage());
            return;
        }

        return 0;
    }
}
