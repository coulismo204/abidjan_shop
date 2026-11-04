<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'product_size_id', 'product_name', 'brand_name',
        'color_name', 'size', 'unit_price', 'quantity',
    ];

    protected function casts(): array
    {
        return ['size' => 'integer', 'unit_price' => 'integer', 'quantity' => 'integer'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function lineTotal(): int
    {
        return $this->unit_price * $this->quantity;
    }

    public function label(): string
    {
        return trim(($this->brand_name ? $this->brand_name.' ' : '').$this->product_name)
            .($this->color_name ? ' – '.$this->color_name : '');
    }
}
