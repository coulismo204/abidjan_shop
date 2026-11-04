<?php

namespace App\Support;

/**
 * Tranches de prix du filtre de la boutique. Des tranches plutôt que deux champs « min / max » :
 * un seul toucher sur mobile, et des URL lisibles (?prix=20000-40000).
 */
class PriceRange
{
    /** @return array<string, array{label: string, min: ?int, max: ?int}> */
    public static function all(): array
    {
        return [
            '0-20000' => ['label' => __('Moins de 20 000 F'), 'min' => null, 'max' => 20000],
            '20000-40000' => ['label' => __('20 000 à 40 000 F'), 'min' => 20000, 'max' => 40000],
            '40000-70000' => ['label' => __('40 000 à 70 000 F'), 'min' => 40000, 'max' => 70000],
            '70000-plus' => ['label' => __('Plus de 70 000 F'), 'min' => 70000, 'max' => null],
        ];
    }

    /** @return array{label: string, min: ?int, max: ?int}|null */
    public static function find(?string $key): ?array
    {
        return self::all()[$key] ?? null;
    }
}
