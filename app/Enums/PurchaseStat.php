<?php

namespace App\Enums;

enum PurchaseStat: int
{
    case UNRECEIVED = 0;
    case RECEIVED = 1;

    public function label(): string
    {
        return match ($this) {
            self::UNRECEIVED => __('Unreceived'),
            self::RECEIVED => __('Received'),
        };
    }
}
