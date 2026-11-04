<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Exceptions\OutOfStock;
use App\Models\Order;
use App\Models\ProductSize;
use App\Models\Setting;
use App\Support\Communes;
use Illuminate\Support\Facades\DB;

/**
 * Enregistre une commande et réserve le stock.
 *
 * Les pointures sont verrouillées (lockForUpdate) pendant la transaction : si deux clients
 * commandent la dernière paire au même moment, le second reçoit une erreur au lieu d'une
 * commande impossible à livrer.
 */
class PlaceOrder
{
    /**
     * @param  array<int, int>  $cart  [id de pointure => quantité]
     * @param  array{customer_name: string, phone: string, commune: string, address: string, payment_method: string, note: ?string}  $customer
     *
     * @throws OutOfStock
     */
    public function handle(array $cart, array $customer): Order
    {
        return DB::transaction(function () use ($cart, $customer) {
            $sizes = ProductSize::with(['product.brand', 'product.color', 'product.category'])
                ->whereIn('id', array_keys($cart))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $items = [];
            foreach ($cart as $sizeId => $quantity) {
                $size = $sizes->get($sizeId);
                if (! $size || ! $size->product->isPublished() || $size->stock < $quantity) {
                    throw new OutOfStock;
                }
                $items[] = [$size, $quantity];
            }

            $subtotal = array_sum(array_map(fn ($i) => $i[0]->product->price * $i[1], $items));
            $deliveryFee = Communes::isInAbidjan($customer['commune']) ? Setting::deliveryFee() : null;

            $order = Order::create([
                ...$customer,
                'reference' => Order::newReference(),
                'status' => OrderStatus::New,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $subtotal + ($deliveryFee ?? 0),
            ]);

            foreach ($items as [$size, $quantity]) {
                $product = $size->product;
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_size_id' => $size->id,
                    'product_name' => $product->name,
                    'brand_name' => $product->brand?->name,
                    'color_name' => $product->color?->name,
                    'size' => $size->size,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                ]);
                $size->decrement('stock', $quantity);
            }

            return $order;
        });
    }
}
