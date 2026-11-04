@php
    use App\Support\Money;
    use App\Support\WhatsApp;
@endphp
<x-layouts.public :title="__('Commande :ref', ['ref' => $order->reference])" noindex :floating-wa="false">
    <div class="container-page max-w-3xl pt-8 sm:pt-12">
        @include('public.partials.steps', ['current' => 3])

        <div class="mt-8 rounded-3xl bg-vert-vif p-6 text-encre sm:p-10">
            <span class="flex size-14 items-center justify-center rounded-full bg-white"><x-icon name="check" class="size-8" /></span>
            <h1 class="mt-5 text-5xl sm:text-6xl">{{ __('Merci :name !', ['name' => \Illuminate\Support\Str::of($order->customer_name)->explode(' ')->first()]) }}</h1>
            <p class="mt-3 text-lg font-medium">{{ __('Commande') }} <strong class="font-mono">{{ $order->reference }}</strong> {{ __('enregistrée. Vos paires sont réservées.') }}</p>

            <div class="mt-8 rounded-2xl bg-white p-5">
                <p class="font-display text-2xl uppercase">{{ __('Dernière étape : envoyez-la sur WhatsApp') }}</p>
                <p class="mt-2 text-sm leading-relaxed text-gris">{{ __('Le récapitulatif est déjà écrit, il suffit d\'appuyer sur « Envoyer ». On vous répond pour fixer l\'heure de livraison.') }}</p>
                <a href="{{ route('wa.order', $order) }}" rel="nofollow" class="btn-wa mt-5 w-full py-4 text-base sm:w-auto sm:px-10"><x-icon name="whatsapp" class="size-6" />{{ __('Envoyer ma commande sur WhatsApp') }}</a>
                <p class="mt-3 text-xs text-gris">{{ __('Pas de WhatsApp ? Pas de souci : on vous appelle au :p.', ['p' => $order->formattedPhone()]) }}</p>
            </div>
        </div>

        <section class="mt-6 rounded-3xl border border-ligne bg-white p-5 sm:p-8">
            <h2 class="text-2xl">{{ __('Récapitulatif') }}</h2>
            <ul class="mt-3 divide-y divide-ligne">
                @foreach ($order->items as $item)
                    <li class="flex justify-between gap-4 py-3">
                        <div>
                            <p class="font-semibold">{{ $item->label() }}</p>
                            <p class="text-sm text-gris">{{ __('Pointure :s', ['s' => $item->size]) }} · {{ __('Qté :q', ['q' => $item->quantity]) }}</p>
                        </div>
                        <p class="font-semibold whitespace-nowrap">{{ Money::fcfa($item->lineTotal()) }}</p>
                    </li>
                @endforeach
            </ul>
            <dl class="mt-2 space-y-2 border-t border-ligne pt-4 text-sm">
                <div class="flex justify-between"><dt class="text-gris">{{ __('Sous-total') }}</dt><dd>{{ Money::fcfa($order->subtotal) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gris">{{ __('Livraison') }}</dt><dd>{{ WhatsApp::deliveryLabel($order->delivery_fee) }}</dd></div>
                <div class="flex justify-between text-base font-bold"><dt>{{ __('Total') }}</dt><dd>{{ Money::fcfa($order->total) }}</dd></div>
            </dl>
            <dl class="mt-6 grid gap-4 border-t border-ligne pt-5 text-sm sm:grid-cols-2">
                <div><dt class="text-gris">{{ __('Livraison à') }}</dt><dd class="mt-0.5 font-semibold">{{ $order->commune }} · {{ $order->address }}</dd></div>
                <div><dt class="text-gris">{{ __('Paiement') }}</dt><dd class="mt-0.5 font-semibold">{{ $order->payment_method->label() }}</dd></div>
            </dl>
        </section>
        <a href="{{ route('shop') }}" class="mt-6 inline-flex items-center gap-2 font-semibold hover:underline"><x-icon name="back" class="size-4" />{{ __('Retour à la boutique') }}</a>
    </div>
</x-layouts.public>
