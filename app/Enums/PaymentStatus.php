<?php

namespace App\Enums;

enum PaymentStatus: int
{
    case PAID = 0;
    case UNPAID = 1;

    public function label(): string
    {
        return match ($this) {
            self::PAID => __('Paid'),
            self::OVERDUE => __('Unpaid'),
        };
    }
}
