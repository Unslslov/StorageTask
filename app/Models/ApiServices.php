<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiServices extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'allowed_token_types'];

    protected $casts = [
        'allowed_token_types' => 'array',
    ];

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }
}
