@props(['product', 'size' => 'base'])
@php
    use App\Support\Money;
    $big = $size === 'lg';
@endphp
<p {{ $attributes->merge(['class' => 'flex flex-wrap items-baseline gap-x-2 gap-y-0.5']) }}>
    <span @class(['font-display font-extrabold tracking-tight [font-stretch:85%]', 'text-3xl sm:text-4xl' => $big, 'text-lg' => ! $big, 'text-rouge' => $product->isOnSale()])>
        @if ($product->isOnSale())<span class="sr-only">{{ __('Prix promo :') }}</span>@endif
        {{ Money::fcfa($product->price) }}
    </span>
    @if ($product->isOnSale())
        <s @class(['text-gris', 'text-base' => $big, 'text-sm' => ! $big])><span class="sr-only">{{ __('au lieu de') }}</span> {{ Money::fcfa($product->compare_at_price) }}</s>
    @endif
</p>
