<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerStatementAccount extends Model
{
    protected $fillable = [
        'customer_id',
        'beginning_balance',
        'start_date',
        'end_date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactions()
    {
        return $this->hasMany(CustomerStatementTransaction::class)
            ->orderBy('transaction_date')
            ->orderBy('id');
    }

}
