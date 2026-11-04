<?php

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case Confirmed = 'confirmed';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::New => __('Nouvelle'),
            self::Confirmed => __('Confirmée'),
            self::Delivered => __('Livrée'),
            self::Cancelled => __('Annulée'),
        };
    }

    /** Classes du badge dans l'administration. */
    public function badge(): string
    {
        return match ($this) {
            self::New => 'bg-orange text-encre',
            self::Confirmed => 'bg-encre text-white',
            self::Delivered => 'bg-vert text-white',
            self::Cancelled => 'bg-sable text-gris line-through',
        };
    }
}
