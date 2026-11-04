<?php

namespace App\Support;

/**
 * Lieux de livraison proposés au moment de la commande.
 */
class Communes
{
    public const OUTSIDE = 'Hors Abidjan';

    public const ABIDJAN = [
        'Abobo', 'Adjamé', 'Anyama', 'Attécoubé', 'Bingerville', 'Cocody', 'Koumassi',
        'Marcory', 'Plateau', 'Port-Bouët', 'Songon', 'Treichville', 'Yopougon',
    ];

    /** @return list<string> */
    public static function all(): array
    {
        return [...self::ABIDJAN, self::OUTSIDE];
    }

    public static function isInAbidjan(string $commune): bool
    {
        return in_array($commune, self::ABIDJAN, true);
    }
}
