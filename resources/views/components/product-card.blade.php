@props(['product', 'loading' => 'lazy'])
@php
    $available = $product->sizes->where('stock', '>', 0)->pluck('size');
@endphp
{{-- Carte produit « boutique populaire » : visuel, prix gros, pointures, et un bouton pour commander. --}}
<article class="group relative flex h-full flex-col overflow-hidden rounded-xl border border-ligne bg-white transition hover:border-vert hover:shadow-lg hover:shadow-encre/5">
    <div class="relative aspect-square overflow-hidden bg-sable">
        <x-product-visual :product="$product" :loading="$loading" class="transition duration-500 group-hover:scale-105" />
        <div class="absolute top-2 left-2 flex flex-col items-start gap-1">
            @if ($product->isOnSale())
                <span class="badge bg-rouge text-sm text-white">−{{ $product->discountPercent() }}%</span>
            @endif
            @if ($product->isNew())
                <span class="badge bg-jaune text-encre">{{ __('Nouveau') }}</span>
            @endif
        </div>
    </div>
    <div class="flex flex-1 flex-col p-3">
        <h3 class="line-clamp-2 text-sm leading-snug font-semibold tracking-normal">
            {{-- Le lien couvre toute la carte (pseudo-élément) : une seule cible, simple pour les lecteurs d'écran. --}}
            <a href="{{ route('product', $product) }}" class="after:absolute after:inset-0 focus-visible:outline-none group-has-[a:focus-visible]:underline">
                @if ($product->brand)<span class="text-gris">{{ $product->brand->name }}</span> @endif{{ $product->name }}
            </a>
        </h3>
        @if ($product->color)
            <p class="mt-0.5 text-xs text-gris">{{ $product->color->name }}</p>
        @endif
        <x-price :product="$product" class="mt-2" />
        @if ($available->isNotEmpty())
            <p class="mt-1 text-xs text-gris">
                <span class="sr-only">{{ __('Pointures disponibles :') }}</span>
                @if ($available->count() <= 2)
                    <span class="font-semibold text-rouge">{{ __('Plus que :s', ['s' => $available->implode(', ')]) }}</span>
                @else
                    {{ __('Pointures :min à :max', ['min' => $available->first(), 'max' => $available->last()]) }}
                @endif
            </p>
        @endif
        <span class="h-3 shrink-0" aria-hidden="true"></span>
        <span class="btn-primary pointer-events-none mt-auto w-full py-2.5 text-sm" aria-hidden="true">{{ __('Commander') }}</span>
    </div>
</article>
