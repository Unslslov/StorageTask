<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'api_service_id',
        'token_type_id',
        'value',
        'meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function apiService(): BelongsTo
    {
        return $this->belongsTo(ApiServices::class);
    }

    public function tokenType(): BelongsTo
    {
        return $this->belongsTo(TokenTypes::class);
    }
}
