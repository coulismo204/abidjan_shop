<?php

namespace App\Support;

class Bots
{
    public static function is(?string $userAgent): bool
    {
        if (! $userAgent) {
            return true;
        }

        return (bool) preg_match('/bot|crawl|spider|slurp|facebookexternalhit|whatsapp\/|linkpreview|curl|wget|python|headless|lighthouse/i', $userAgent);
    }
}
