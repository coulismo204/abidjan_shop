<?php

namespace App\Support;

class Money
{
    /** 15000 → « 15 000 FCFA », avec une espace fine insécable : le prix ne se coupe jamais en deux lignes. */
    public static function fcfa(int $amount): string
    {
        return number_format($amount, 0, ',', "\u{202F}")."\u{00A0}FCFA";
    }
}
