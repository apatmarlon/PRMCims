<?php

namespace App\Services;

use App\Models\CustomerStatementAccount;

class CustomerSOAService
{
    public static function getOrCreate(int $customerId): CustomerStatementAccount
    {
        return CustomerStatementAccount::firstOrCreate(
            ['customer_id' => $customerId],
            ['beginning_balance' => 0]
        );
    }
}