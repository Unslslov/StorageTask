<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class CompanyCreate extends Command
{
    protected $signature = 'company:add';
    protected $description = 'Добавление новой компании';
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
        $this->info('Adding a new company...');

        $name = $this->ask('Company name');

        try {
            $company = Company::create(['name' => $name]);

            $this->info("Company created successfully!");
            $this->info("ID: {$company->id}");
            $this->info("Name: {$company->name}");

        } catch (\Exception $e) {
            $this->error("Error creating company: " . $e->getMessage());
            return;
        }
        return 0;
    }
}
