@php use App\Support\Money; @endphp
<x-layouts.admin :title="__('Tableau de bord')">
    <h1 class="text-4xl">{{ __('Tableau de bord') }}</h1>

    <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ([
            [__('À traiter'), $pendingCount, __('nouvelles commandes'), $pendingCount > 0],
            [__('Commandes'), $ordersCount, __('30 derniers jours, hors annulées'), false],
            [__('Ventes'), Money::fcfa($revenue), __('commandées sur 30 jours'), false],
            [__('Encaissé'), Money::fcfa($delivered), __('commandes livrées, 30 jours'), false],
        ] as [$label, $value, $help, $highlight])
            <div @class(['rounded-2xl p-4', 'bg-orange' => $highlight, 'card' => ! $highlight])>
                <p class="text-xs font-bold tracking-wider uppercase">{{ $label }}</p>
                <p class="mt-1 font-display text-3xl font-extrabold [font-stretch:85%]">{{ $value }}</p>
                <p @class(['text-xs', 'text-encre/75' => $highlight, 'text-gris' => ! $highlight])>{{ $help }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <section class="card p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl">{{ __('Nouvelles commandes') }}</h2>
                <a href="{{ route('admin.orders.index', ['statut' => 'new']) }}" class="text-sm font-semibold underline">{{ __('Tout voir') }}</a>
            </div>
            @forelse ($pending as $order)
                <a href="{{ route('admin.orders.show', $order) }}" class="mt-3 flex items-center justify-between gap-3 rounded-xl border border-ligne p-3 hover:border-encre">
                    <div class="min-w-0">
                        <p class="font-semibold">{{ $order->customer_name }} <span class="font-mono text-xs text-gris">{{ $order->reference }}</span></p>
                        <p class="truncate text-sm text-gris">{{ $order->commune }} · {{ $order->created_at->diffForHumans() }} @if ($order->whatsapp_opened_at)· <span class="text-vert-fonce">{{ __('WhatsApp ouvert') }}</span>@endif</p>
                    </div>
                    <p class="shrink-0 font-bold">{{ Money::fcfa($order->total) }}</p>
                </a>
            @empty
                <p class="mt-3 text-sm text-gris">{{ __('Aucune commande en attente. Tout est à jour !') }}</p>
            @endforelse
        </section>

        <section class="card p-5">
            <h2 class="text-2xl">{{ __('Stock à surveiller') }}</h2>
            <p class="text-sm text-gris">{{ __('Pointures à 0 ou 1 paire sur les produits en ligne.') }}</p>
            <ul class="mt-3 divide-y divide-ligne text-sm">
                @forelse ($lowStock as $size)
                    <li class="flex items-center justify-between gap-3 py-2">
                        <a href="{{ route('admin.products.edit', $size->product) }}" class="min-w-0 truncate hover:underline">{{ $size->product->fullName() }}</a>
                        <span class="shrink-0">{{ __('P. :s', ['s' => $size->size]) }} · <span @class(['badge', 'bg-rouge text-white' => $size->stock === 0, 'bg-orange-pale text-orange-fonce' => $size->stock === 1])>{{ $size->stock === 0 ? __('épuisée') : __('1 paire') }}</span></span>
                    </li>
                @empty
                    <li class="py-2 text-gris">{{ __('Rien à signaler.') }}</li>
                @endforelse
            </ul>
        </section>

        <section class="card p-5">
            <h2 class="text-2xl">{{ __('Meilleures ventes (30 j)') }}</h2>
            <ol class="mt-3 space-y-2 text-sm">
                @forelse ($topProducts as $item)
                    <li class="flex justify-between gap-3"><span>{{ trim($item->brand_name.' '.$item->product_name) }}@if ($item->color_name) <span class="text-gris">– {{ $item->color_name }}</span>@endif</span><span class="shrink-0 font-semibold">{{ trans_choice(':count paire|:count paires', (int) $item->sold) }}</span></li>
                @empty
                    <li class="text-gris">{{ __('Pas encore de vente sur la période.') }}</li>
                @endforelse
            </ol>
        </section>

        <section class="card p-5">
            <h2 class="text-2xl">{{ __('Les plus consultés') }}</h2>
            <ol class="mt-3 space-y-2 text-sm">
                @forelse ($mostViewed as $product)
                    <li class="flex justify-between gap-3"><a href="{{ route('admin.products.edit', $product) }}" class="hover:underline">{{ $product->fullName() }}</a><span class="shrink-0 text-gris">{{ trans_choice(':count vue|:count vues', $product->views_count) }}</span></li>
                @empty
                    <li class="text-gris">{{ __('Pas encore de visites.') }}</li>
                @endforelse
            </ol>
            <p class="mt-4 border-t border-ligne pt-3 text-xs text-gris">{{ __(':o produits en ligne sur :t au catalogue.', ['o' => $counts['online'], 't' => $counts['products']]) }}</p>
        </section>
    </div>
</x-layouts.admin>
