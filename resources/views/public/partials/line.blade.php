{{-- Une ligne de panier ou de récapitulatif. $editable = sélecteur de quantité + suppression. --}}
@php
    $size = $line['size'];
    $product = $size->product;
    $editable ??= false;
@endphp
<li class="flex gap-4 py-4">
    <a href="{{ route('product', $product) }}" class="block size-20 shrink-0 overflow-hidden rounded-xl {{ $product->tintClass() }} sm:size-24">
        <x-product-visual :product="$product" />
    </a>
    <div class="flex min-w-0 flex-1 flex-col">
        <div class="flex justify-between gap-3">
            <div class="min-w-0">
                @if ($product->brand)<p class="text-[11px] font-bold tracking-[.16em] text-gris uppercase">{{ $product->brand->name }}</p>@endif
                <a href="{{ route('product', $product) }}" class="font-semibold hover:underline">{{ $product->name }}</a>
                <p class="mt-0.5 text-sm text-gris">
                    @if ($product->color){{ $product->color->name }} · @endif{{ __('Pointure :s', ['s' => $size->size]) }}
                    @unless ($editable) · {{ __('Qté :q', ['q' => $line['quantity']]) }} @endunless
                </p>
            </div>
            <p class="shrink-0 font-bold whitespace-nowrap">{{ \App\Support\Money::fcfa($line['total']) }}</p>
        </div>
        @if ($editable)
            <div class="mt-auto flex items-center gap-4 pt-2">
                <form method="post" action="{{ route('cart.update', $size->id) }}" class="flex items-center gap-2">
                    @csrf @method('patch')
                    <label for="qty-{{ $size->id }}" class="text-sm text-gris">{{ __('Qté') }}</label>
                    <select id="qty-{{ $size->id }}" name="quantity" data-autosubmit class="field w-auto py-1.5 pr-9 pl-3 text-sm">
                        @foreach (range(1, min(\App\Support\Cart::MAX_PER_LINE, max($size->stock, $line['quantity']))) as $n)
                            <option value="{{ $n }}" @selected($n === $line['quantity'])>{{ $n }}</option>
                        @endforeach
                    </select>
                    <noscript><button class="text-sm font-semibold underline">{{ __('OK') }}</button></noscript>
                </form>
                <form method="post" action="{{ route('cart.remove', $size->id) }}">
                    @csrf @method('delete')
                    <button class="inline-flex items-center gap-1 text-sm font-semibold text-gris hover:text-rouge"><x-icon name="trash" class="size-4" />{{ __('Retirer') }}</button>
                </form>
            </div>
        @endif
    </div>
</li>
