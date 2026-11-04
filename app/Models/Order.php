<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'reference', 'status', 'customer_name', 'phone', 'commune', 'address',
        'payment_method', 'note', 'subtotal', 'delivery_fee', 'total', 'whatsapp_opened_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_method' => PaymentMethod::class,
            'subtotal' => 'integer',
            'delivery_fee' => 'integer',
            'total' => 'integer',
            'whatsapp_opened_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Référence courte à dicter au téléphone : AS-241104-K7Q2 (sans 0/O ni 1/I). */
    public static function newReference(): string
    {
        do {
            $reference = 'AS-'.now()->format('ymd').'-'.collect(range(1, 4))
                ->map(fn () => Str::of('23456789ABCDEFGHJKLMNPQRSTUVWXYZ')->charAt(random_int(0, 31)))
                ->implode('');
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    public function itemsCount(): int
    {
        return (int) $this->items->sum('quantity');
    }

    /** Numéro au format international, pour écrire au client sur WhatsApp. */
    public function whatsappNumber(): string
    {
        return '225'.$this->phone;
    }

    public function formattedPhone(): string
    {
        return trim(chunk_split($this->phone, 2, ' '));
    }
}
