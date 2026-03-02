<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturnDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_return_id',
        'product_id',
        'quantity',
        'unit_price',
        'total',
    ];

    /* ===============================
        RELATIONSHIPS
    =============================== */

    public function orderReturn()
    {
        return $this->belongsTo(OrderReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
