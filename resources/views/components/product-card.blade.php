@props(['product', 'loading' => 'lazy'])
@php
    $available = $product->sizes->where('stock', '>', 0)->pluck('size');
@endphp
<article class="group relative flex h-full flex-col">
    <div class="relative aspect-square overflow-hidden rounded-2xl {{ $product->tintClass() }}">
        <x-product-visual :product="$product" :loading="$loading" class="transition duration-500 ease-out-soft group-hover:scale-[1.06]" />
        <div class="absolute top-3 left-3 flex flex-col items-start gap-1.5">
            @if ($product->isOnSale())
                <span class="badge bg-rouge text-white">−{{ $product->discountPercent() }} %</span>
            @endif
            @if ($product->isNew())
                <span class="badge bg-encre text-white">{{ __('Nouveau') }}</span>
            @endif
        </div>
        @if ($available->count() > 0 && $available->count() <= 2)
            <span class="absolute right-3 bottom-3 badge bg-white/90 text-encre">{{ __('Dernières pointures') }}</span>
        @endif
    </div>
    <div class="flex flex-1 flex-col pt-3">
        @if ($product->brand)
            <p class="text-[11px] font-bold tracking-[.16em] text-gris uppercase">{{ $product->brand->name }}</p>
        @endif
        <h3 class="mt-0.5 font-sans text-[15px] leading-snug font-semibold [font-stretch:100%] normal-case">
            {{-- Le lien couvre toute la carte (pseudo-élément) : une seule cible, simple pour les lecteurs d'écran. --}}
            <a href="{{ route('product', $product) }}" class="after:absolute after:inset-0 after:rounded-2xl focus-visible:outline-none group-has-[a:focus-visible]:underline">{{ $product->name }}</a>
        </h3>
        @if ($product->color)
            <p class="mt-1 flex items-center gap-1.5 text-xs text-gris"><x-color-dot :color="$product->color" class="size-3" />{{ $product->color->name }}</p>
        @endif
        <div class="mt-auto pt-2">
            <x-price :product="$product" />
            @if ($available->isNotEmpty())
                <p class="mt-0.5 text-xs text-gris"><span class="sr-only">{{ __('Pointures disponibles :') }}</span>{{ $available->implode(' · ') }}</p>
            @endif
        </div>
    </div>
</article>
