<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetails extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unitcost',
        'returned_quantity',
        'markup_price',
        'total',
        'order_date',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $with = ['product'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function remainingQty(): int
    {
        return $this->quantity - $this->returned_quantity;
    }
}
