<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\ApiService;
use App\Models\TokenType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory()->create()->id,
            'api_service_id' => ApiService::factory()->create()->id,
            'token_type_id' => TokenType::factory()->create()->id,
            'value' => 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie'
        ];
    }
}
