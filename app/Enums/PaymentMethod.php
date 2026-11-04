<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case MobileMoney = 'mobile_money';

    public function label(): string
    {
        return match ($this) {
            self::Cash => __('Espèces à la livraison'),
            self::MobileMoney => __('Mobile Money (Orange, MTN, Moov, Wave)'),
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Cash => __('Espèces à la livraison'),
            self::MobileMoney => __('Mobile Money'),
        };
    }
}
