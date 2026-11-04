<?php

namespace App\Support;

use App\Models\ProductSize;
use Illuminate\Support\Collection;

/**
 * Panier en session : [id de la pointure => quantité]. Aucun compte client nécessaire.
 * Les prix et le stock sont toujours relus en base : le panier ne garde que des identifiants.
 */
class Cart
{
    public const MAX_PER_LINE = 5;

    private const KEY = 'cart';

    /** @return array<int, int> */
    public function raw(): array
    {
        return session(self::KEY, []);
    }

    public function add(ProductSize $size, int $quantity = 1): void
    {
        $cart = $this->raw();
        $cart[$size->id] = min(($cart[$size->id] ?? 0) + $quantity, self::MAX_PER_LINE, $size->stock);
        session([self::KEY => $cart]);
    }

    public function update(int $sizeId, int $quantity): void
    {
        $cart = $this->raw();
        if ($quantity < 1) {
            unset($cart[$sizeId]);
        } elseif (isset($cart[$sizeId])) {
            $cart[$sizeId] = min($quantity, self::MAX_PER_LINE);
        }
        session([self::KEY => $cart]);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    /**
     * Lignes du panier, prêtes à afficher. Les articles devenus indisponibles sont retirés
     * et les quantités ramenées au stock réel.
     *
     * @return Collection<int, array{size: ProductSize, quantity: int, total: int}>
     */
    public function lines(): Collection
    {
        $cart = $this->raw();
        if ($cart === []) {
            return collect();
        }

        $sizes = ProductSize::with(['product.brand', 'product.color', 'product.category', 'product.coverPhoto'])
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $lines = collect();
        $clean = [];
        foreach ($cart as $id => $quantity) {
            $size = $sizes->get($id);
            if (! $size || $size->stock < 1 || ! $size->product->isPublished()) {
                continue;
            }
            $quantity = min($quantity, $size->stock, self::MAX_PER_LINE);
            $clean[$id] = $quantity;
            $lines->push(['size' => $size, 'quantity' => $quantity, 'total' => $size->product->price * $quantity]);
        }

        if ($clean !== $cart) {
            session([self::KEY => $clean]);
        }

        return $lines;
    }

    public function subtotal(?Collection $lines = null): int
    {
        return (int) ($lines ?? $this->lines())->sum('total');
    }
}
