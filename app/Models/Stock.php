<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    public $incrementing = false;
    protected $primaryKey = null;
    protected $guarded = false;
    public $timestamps = false;

    use HasFactory;

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
