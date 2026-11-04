<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /** Valeurs par défaut, modifiables depuis l'administration (Réglages). */
    public const DEFAULTS = [
        'whatsapp_number' => '2250700000000',
        'hero_title' => 'Le style au pied, livré chez vous',
        'hero_subtitle' => 'Baskets, mocassins, chaussures de ville : les modèles qui comptent, en vraies pointures, livrés partout à Abidjan.',
        'delivery_fee' => '1500',
        'delivery_delay' => 'Livraison en 24 h à Abidjan',
        'delivery_outside' => 'Intérieur du pays : expédition par car, frais confirmés sur WhatsApp.',
        'exchange_policy' => "Pointure qui ne va pas ? Échange sous 48 h, chaussures non portées, dans leur boîte d'origine.",
        'contact_phone' => '',
        'contact_email' => '',
        'contact_address' => 'Abidjan, Côte d\'Ivoire',
        'contact_hours' => 'Commandes en ligne 24 h/24 · Réponses sur WhatsApp de 8 h à 21 h',
        'instagram_url' => '',
        'facebook_url' => '',
        'tiktok_url' => '',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $all = Cache::rememberForever('settings', fn () => static::query()->pluck('value', 'key')->all());

        $value = $all[$key] ?? null;

        return ($value === null || $value === '') ? ($default ?? self::DEFAULTS[$key] ?? null) : $value;
    }

    /** @param  array<string, string|null>  $values */
    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget('settings');
    }

    /** Frais de livraison à Abidjan en FCFA, null s'ils ne sont pas fixés (à confirmer). */
    public static function deliveryFee(): ?int
    {
        $fee = static::get('delivery_fee');

        return is_numeric($fee) ? (int) $fee : null;
    }
}
