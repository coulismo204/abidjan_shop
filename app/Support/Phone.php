<?php

namespace App\Support;

/**
 * Numéros ivoiriens : 10 chiffres depuis 2021 (01, 05, 07 mobiles ; 21, 25, 27 fixes).
 * Le client peut écrire « +225 07 08 09 10 11 », « 0708091011 » ou « 07.08.09.10.11 ».
 */
class Phone
{
    public static function normalize(?string $input): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $input);

        if (strlen($digits) === 13 && str_starts_with($digits, '225')) {
            $digits = substr($digits, 3);
        } elseif (strlen($digits) === 15 && str_starts_with($digits, '00225')) {
            $digits = substr($digits, 5);
        }

        return preg_match('/^(01|05|07|21|25|27)\d{8}$/', $digits) ? $digits : null;
    }
}
