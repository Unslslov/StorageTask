<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = null;
    protected $guarded = false;
    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function income()
    {
        return $this->hasOne(Income::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
