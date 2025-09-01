<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = null;
    protected $guarded = false;
    public $timestamps = false;
//    public function customer()
//    {
//        return $this->belongsTo(Customer::class);
//    }
//
//    public function product()
//    {
//        return $this->belongsTo(Product::class);
//    }

    public function sale()
    {
        return $this->hasOne(Sale::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
