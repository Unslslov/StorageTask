<?php

namespace Database\Seeders;

use App\Models\ApiService;
use Illuminate\Database\Seeder;

class ApiServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ApiService::factory()->create();
    }
}
