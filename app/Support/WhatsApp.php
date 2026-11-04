<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;

/**
 * Liens wa.me avec un message pré-rempli.
 */
class WhatsApp
{
    public static function number(): string
    {
        return preg_replace('/\D+/', '', (string) Setting::get('whatsapp_number'));
    }

    public static function url(string $message, ?string $number = null): string
    {
        return 'https://wa.me/'.($number ?? self::number()).'?text='.rawurlencode($message);
    }

    /** Récapitulatif envoyé par le client après sa commande : la boutique a tout sous les yeux. */
    public static function orderMessage(Order $order): string
    {
        $lines = [
            __('Bonjour Abidjan Shop, je viens de passer la commande :ref.', ['ref' => $order->reference]),
            '',
        ];

        foreach ($order->items as $item) {
            $lines[] = '• '.$item->quantity.' × '.$item->label().' – '.__('pointure :s', ['s' => $item->size]).' – '.Money::fcfa($item->lineTotal());
        }

        $lines[] = '';
        $lines[] = __('Sous-total : :a', ['a' => Money::fcfa($order->subtotal)]);
        $lines[] = __('Livraison : :a', ['a' => self::deliveryLabel($order->delivery_fee)]);
        $lines[] = __('Total : :a', ['a' => Money::fcfa($order->total)]).($order->delivery_fee === null ? ' '.__('(+ livraison)') : '');
        $lines[] = '';
        $lines[] = __('Nom : :v', ['v' => $order->customer_name]);
        $lines[] = __('Téléphone : :v', ['v' => $order->formattedPhone()]);
        $lines[] = __('Livraison à : :c, :a', ['c' => $order->commune, 'a' => $order->address]);
        $lines[] = __('Paiement : :v', ['v' => $order->payment_method->shortLabel()]);

        if ($order->note) {
            $lines[] = __('Remarque : :v', ['v' => $order->note]);
        }

        return implode("\n", $lines);
    }

    public static function productQuestion(Product $product): string
    {
        return __('Bonjour Abidjan Shop, j\'ai une question sur : :p (:price).', [
            'p' => $product->fullName(),
            'price' => Money::fcfa($product->price),
        ])."\n".route('product', $product);
    }

    public static function contactMessage(): string
    {
        return __('Bonjour Abidjan Shop, j\'ai une question :');
    }

    public static function deliveryLabel(?int $fee): string
    {
        return match (true) {
            $fee === null => __('à confirmer'),
            $fee === 0 => __('offerte'),
            default => Money::fcfa($fee),
        };
    }
}
