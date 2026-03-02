<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerStatementTransaction extends Model
{
    protected $fillable = [
        'customer_statement_account_id',
        'transaction_date',
        'ref_no',
        'due_date',
        'description',
        'debit',
        'credit',
        'balance',
    ];

    public function statementAccount()
    {
        return $this->belongsTo(
            CustomerStatementAccount::class,
            'customer_statement_account_id'
        );
    }
    
}

