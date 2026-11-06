@props(['product', 'size' => 'base'])
@php
    use App\Support\Money;
    $big = $size === 'lg';
@endphp
{{-- Prix bien visible : c'est la première chose que le client regarde. --}}
<p {{ $attributes->merge(['class' => 'flex flex-wrap items-baseline gap-x-2 gap-y-0.5 tabular-nums']) }}>
    <span @class(['font-extrabold tracking-tight', 'text-3xl' => $big, 'text-lg' => ! $big, 'text-rouge' => $product->isOnSale()])>
        @if ($product->isOnSale())<span class="sr-only">{{ __('Prix promo :') }}</span>@endif
        {{ Money::fcfa($product->price) }}
    </span>
    @if ($product->isOnSale())
        <s @class(['text-gris', 'text-base' => $big, 'text-xs' => ! $big])><span class="sr-only">{{ __('au lieu de') }}</span> {{ Money::fcfa($product->compare_at_price) }}</s>
    @endif
</p>
