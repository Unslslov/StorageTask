<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = null;
    protected $guarded = false;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function income()
    {
        return $this->hasOne(Income::class);
    }
}
