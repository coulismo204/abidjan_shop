<?php

namespace App\Http\Controllers;

use App\Models\ProductSize;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function index(): View
    {
        $lines = $this->cart->lines();

        return view('public.cart', ['lines' => $lines, 'subtotal' => $this->cart->subtotal($lines)]);
    }

    /** « Ajouter au panier » ou « Commander maintenant » (même formulaire, bouton différent). */
    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'size_id' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.Cart::MAX_PER_LINE],
        ], ['size_id.required' => __('Choisissez votre pointure.')]);

        $size = ProductSize::with('product.category', 'product.brand')->find($data['size_id']);
        if (! $size || ! $size->product->isPublished() || $size->stock < 1) {
            throw ValidationException::withMessages(['size_id' => __('Cette pointure n\'est plus disponible.')]);
        }

        $this->cart->add($size, (int) ($data['quantity'] ?? 1));

        if ($request->input('action') === 'buy') {
            return redirect()->route('checkout');
        }

        return back()->with('cart_added', __(':p, pointure :s, ajouté au panier.', [
            'p' => $size->product->fullName(),
            's' => $size->size,
        ]));
    }

    public function update(Request $request, int $sizeId): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:'.Cart::MAX_PER_LINE]]);
        $this->cart->update($sizeId, (int) $data['quantity']);

        return redirect()->route('cart');
    }

    public function remove(int $sizeId): RedirectResponse
    {
        $this->cart->update($sizeId, 0);

        return redirect()->route('cart')->with('status', __('Article retiré du panier.'));
    }
}
