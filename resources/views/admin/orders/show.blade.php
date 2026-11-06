@php
    use App\Enums\OrderStatus;
    use App\Support\Money;
    use App\Support\WhatsApp;
    $replyMessage = __('Bonjour :name, ici Abidjan Shop. Nous avons bien reçu votre commande :ref.', ['name' => $order->customer_name, 'ref' => $order->reference]);
@endphp
<x-layouts.admin :title="$order->reference">
    <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-gris hover:text-encre">← {{ __('Commandes') }}</a>
    <div class="mt-2 flex flex-wrap items-center gap-3">
        <h1 class="font-mono text-3xl normal-case">{{ $order->reference }}</h1>
        <span class="badge {{ $order->status->badge() }} px-3 py-1 text-sm">{{ $order->status->label() }}</span>
    </div>
    <p class="mt-1 text-sm text-gris">
        {{ __('Passée le :d', ['d' => $order->created_at->translatedFormat('l d F Y à H:i')]) }}
        · {{ $order->whatsapp_opened_at ? __('le client a ouvert WhatsApp') : __('le client n\'a pas (encore) ouvert WhatsApp') }}
    </p>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <section class="card p-5">
            <h2 class="text-2xl">{{ __('Articles') }}</h2>
            <ul class="mt-3 divide-y divide-ligne">
                @foreach ($order->items as $item)
                    <li class="flex justify-between gap-4 py-3">
                        <div>
                            @if ($item->product)
                                <a href="{{ route('admin.products.edit', $item->product) }}" class="font-semibold hover:underline">{{ $item->label() }}</a>
                            @else
                                <p class="font-semibold">{{ $item->label() }} <span class="text-xs text-gris">({{ __('produit supprimé') }})</span></p>
                            @endif
                            <p class="text-sm"><span class="badge bg-encre text-white">{{ __('Pointure :s', ['s' => $item->size]) }}</span> × {{ $item->quantity }} · {{ Money::fcfa($item->unit_price) }}</p>
                        </div>
                        <p class="font-semibold whitespace-nowrap">{{ Money::fcfa($item->lineTotal()) }}</p>
                    </li>
                @endforeach
            </ul>
            <dl class="space-y-1.5 border-t border-ligne pt-4 text-sm">
                <div class="flex justify-between"><dt class="text-gris">{{ __('Sous-total') }}</dt><dd>{{ Money::fcfa($order->subtotal) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gris">{{ __('Livraison') }}</dt><dd>{{ WhatsApp::deliveryLabel($order->delivery_fee) }}</dd></div>
                <div class="flex justify-between text-base font-bold"><dt>{{ __('Total à encaisser') }}</dt><dd>{{ Money::fcfa($order->total) }}@if ($order->delivery_fee === null) + {{ __('livraison') }}@endif</dd></div>
            </dl>
        </section>

        <div class="space-y-6">
            <section class="card p-5">
                <h2 class="text-2xl">{{ __('Client') }}</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div><dt class="text-gris">{{ __('Nom') }}</dt><dd class="font-semibold">{{ $order->customer_name }}</dd></div>
                    <div><dt class="text-gris">{{ __('Téléphone') }}</dt><dd><a href="tel:+{{ $order->whatsappNumber() }}" class="font-semibold underline">{{ $order->formattedPhone() }}</a></dd></div>
                    <div><dt class="text-gris">{{ __('Adresse') }}</dt><dd class="font-semibold">{{ $order->commune }} · {{ $order->address }}</dd></div>
                    <div><dt class="text-gris">{{ __('Paiement') }}</dt><dd class="font-semibold">{{ $order->payment_method->label() }}</dd></div>
                    @if ($order->note)<div><dt class="text-gris">{{ __('Remarque') }}</dt><dd class="rounded-xl bg-vert-pale p-2">{{ $order->note }}</dd></div>@endif
                </dl>
                <a href="{{ WhatsApp::url($replyMessage, $order->whatsappNumber()) }}" target="_blank" rel="noopener" class="btn-wa mt-4 w-full py-2.5 text-sm"><x-icon name="whatsapp" class="size-4" />{{ __('Écrire au client') }}</a>
            </section>

            @if ($order->status !== OrderStatus::Cancelled)
                <section class="card p-5">
                    <h2 class="text-2xl">{{ __('Suivi') }}</h2>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ([OrderStatus::Confirmed, OrderStatus::Delivered] as $next)
                            @if ($order->status !== $next)
                                <form method="post" action="{{ route('admin.orders.update', $order) }}">
                                    @csrf @method('patch')
                                    <input type="hidden" name="status" value="{{ $next->value }}">
                                    <button class="a-btn-primary">{{ __('Marquer « :s »', ['s' => $next->label()]) }}</button>
                                </form>
                            @endif
                        @endforeach
                        <form method="post" action="{{ route('admin.orders.update', $order) }}" data-confirm="{{ __('Annuler cette commande ? Les paires seront remises en stock. Cette action est définitive.') }}">
                            @csrf @method('patch')
                            <input type="hidden" name="status" value="{{ OrderStatus::Cancelled->value }}">
                            <button class="a-btn-danger">{{ __('Annuler la commande') }}</button>
                        </form>
                    </div>
                    <p class="mt-3 text-xs text-gris">{{ __('Le stock est réservé dès la commande. L\'annulation le libère.') }}</p>
                </section>
            @endif
        </div>
    </div>
</x-layouts.admin>
