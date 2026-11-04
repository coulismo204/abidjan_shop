@php
    use App\Models\Setting;
    use App\Support\Money;
    use App\Support\WhatsApp;
@endphp
<x-layouts.public :title="__('Mon panier')" noindex>
    <div class="container-page pt-8 sm:pt-12">
        @include('public.partials.steps', ['current' => 1])
        <h1 class="mt-6 text-5xl sm:text-6xl">{{ __('Mon panier') }}</h1>

        @if ($lines->isEmpty())
            <div class="mt-8 rounded-3xl border border-ligne bg-white px-6 py-14 text-center">
                <x-icon name="bag" class="mx-auto size-14 text-gris" />
                <p class="mt-4 font-display text-3xl uppercase">{{ __('Votre panier est vide') }}</p>
                <p class="mt-2 text-gris">{{ __('Trouvez la paire qu\'il vous faut parmi nos modèles.') }}</p>
                <a href="{{ route('shop') }}" class="btn-primary mt-6">{{ __('Voir les chaussures') }}<x-icon name="arrow" class="size-5" /></a>
            </div>
        @else
            <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_24rem]">
                <ul class="divide-y divide-ligne rounded-3xl border border-ligne bg-white px-4 sm:px-6">
                    @foreach ($lines as $line)
                        @include('public.partials.line', ['line' => $line, 'editable' => true])
                    @endforeach
                </ul>

                <aside class="h-fit rounded-3xl bg-orange p-6 text-encre lg:sticky lg:top-24">
                    <h2 class="text-2xl">{{ __('Récapitulatif') }}</h2>
                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-encre/80">{{ trans_choice('Sous-total (:count paire)|Sous-total (:count paires)', $lines->sum('quantity')) }}</dt><dd class="font-semibold">{{ Money::fcfa($subtotal) }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-encre/80">{{ __('Livraison à Abidjan') }}</dt><dd class="text-right font-semibold">{{ WhatsApp::deliveryLabel(Setting::deliveryFee()) }}</dd></div>
                    </dl>
                    <div class="mt-5 flex items-baseline justify-between border-t border-encre/20 pt-5">
                        <p class="font-semibold">{{ __('Total') }}</p>
                        <p class="font-display text-3xl font-extrabold [font-stretch:85%]">{{ Money::fcfa($subtotal + (Setting::deliveryFee() ?? 0)) }}</p>
                    </div>
                    <a href="{{ route('checkout') }}" class="btn-dark mt-6 w-full py-4 text-base hover:bg-white hover:text-encre">{{ __('Passer la commande') }}<x-icon name="arrow" class="size-5" /></a>
                    <p class="mt-4 flex items-start gap-2 text-xs leading-relaxed text-encre/80"><x-icon name="wallet" class="size-4 shrink-0" />{{ __('Rien à payer en ligne : vous réglez à la livraison ou par Mobile Money.') }}</p>
                </aside>
            </div>
            <a href="{{ route('shop') }}" class="mt-6 inline-flex items-center gap-2 font-semibold hover:underline"><x-icon name="back" class="size-4" />{{ __('Continuer mes achats') }}</a>
        @endif
    </div>
</x-layouts.public>
