<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Company;
use Illuminate\Console\Command;

class AccountCreateCommand extends Command
{
    protected $signature = 'account:add';
    protected $description = 'Добавление нового пользователя';

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
        $this->info('Adding a new account...');

        // Выводим список компаний для выбора
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->error('No companies found. Please create a company first.');
            return 1;
        }

        $companyOptions = $companies->pluck('name', 'id')->toArray();
        $companyName = $this->choice('Select company', $companyOptions);

        $selectedCompany = $companies->firstWhere('name', $companyName);
        $companyId = $selectedCompany->id;
        $name = $this->ask('Account name');

        try {
            $account = Account::create([
                'company_id' => $companyId,
                'name' => $name
            ]);

            $this->info("Account created successfully!");
            $this->info("ID: {$account->id}");
            $this->info("Name: {$account->name}");
            $this->info("Company: {$companies->find($companyId)->name}");

        } catch (\Exception $e) {
            $this->error("Error creating account: " . $e->getMessage());
            return;
        }

        return 0;
    }
}
