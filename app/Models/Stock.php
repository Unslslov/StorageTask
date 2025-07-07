<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    public $incrementing = false;
    protected $primaryKey = null;

    use HasFactory;
    protected $guarded = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
