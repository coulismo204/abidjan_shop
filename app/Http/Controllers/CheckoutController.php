<?php

namespace App\Http\Controllers;

use App\Actions\PlaceOrder;
use App\Enums\PaymentMethod;
use App\Exceptions\OutOfStock;
use App\Models\Order;
use App\Models\Setting;
use App\Support\Cart;
use App\Support\Communes;
use App\Support\Phone;
use App\Support\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function show(Request $request): View|RedirectResponse
    {
        $lines = $this->cart->lines();
        if ($lines->isEmpty()) {
            return redirect()->route('shop')->with('status', __('Votre panier est vide.'));
        }

        return view('public.checkout', [
            'lines' => $lines,
            'subtotal' => $this->cart->subtotal($lines),
            'deliveryFee' => Setting::deliveryFee(),
            // Coordonnées de la dernière commande : le client fidèle n'a rien à retaper.
            'remembered' => $request->session()->get('customer', []),
        ]);
    }

    public function store(Request $request, PlaceOrder $placeOrder): RedirectResponse
    {
        // Piège à robots : champ invisible pour un humain, rempli par les robots de spam.
        if ($request->filled('website')) {
            return redirect()->route('home');
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'string', 'max:25'],
            'commune' => ['required', Rule::in(Communes::all())],
            'address' => ['required', 'string', 'min:3', 'max:255'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'commune.required' => __('Choisissez votre commune de livraison.'),
            'address.required' => __('Indiquez votre quartier et un repère pour le livreur.'),
        ], [
            'customer_name' => __('nom'),
            'phone' => __('téléphone'),
            'address' => __('adresse'),
        ]);

        $data['phone'] = Phone::normalize($data['phone'])
            ?? throw ValidationException::withMessages(['phone' => __('Numéro invalide : 10 chiffres, par exemple 07 08 09 10 11.')]);

        $cart = $this->cart->raw();
        if ($cart === []) {
            return redirect()->route('shop');
        }

        try {
            $order = $placeOrder->handle($cart, $data);
        } catch (OutOfStock) {
            $this->cart->lines(); // retire ou ajuste les articles devenus indisponibles

            return redirect()->route('cart')->with('error', __('Désolé, un article vient d\'être vendu ou son stock a changé. Vérifiez votre panier puis validez à nouveau.'));
        }

        $this->cart->clear();
        $request->session()->put('customer', collect($data)->only(['customer_name', 'phone', 'commune', 'address'])->all());
        $request->session()->push('orders', $order->reference);

        return redirect()->route('order.done', $order);
    }

    /** Page de confirmation, visible uniquement depuis le navigateur qui a passé la commande. */
    public function done(Request $request, Order $order): View
    {
        $this->authorizeOrder($request, $order);
        $order->load('items');

        return view('public.order-done', compact('order'));
    }

    public function whatsapp(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);
        $order->load('items');
        $order->whatsapp_opened_at ??= now();
        $order->save();

        return redirect()->away(WhatsApp::url(WhatsApp::orderMessage($order)))
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    private function authorizeOrder(Request $request, Order $order): void
    {
        abort_unless(in_array($order->reference, $request->session()->get('orders', []), true), 404);
    }
}
