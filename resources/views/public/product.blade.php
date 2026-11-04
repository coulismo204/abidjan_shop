@php
    use App\Models\Setting;
    use App\Support\Money;
    $photos = $product->photos;
    $inStock = $product->sizes->where('stock', '>', 0);
    $soldOut = $inStock->isEmpty();
    $preselected = old('size_id') ?? ($inStock->count() === 1 ? $inStock->first()->id : null);
@endphp
<x-layouts.public
    :title="$product->fullName().' – '.Money::fcfa($product->price)"
    :description="\Illuminate\Support\Str::limit($product->description ?: $product->fullName(), 150).' '.__('Livraison à Abidjan, paiement à la livraison.')"
    :og-image="$photos->first()?->url()"
    :floating-wa="false"
>
    @push('head')
        {{-- Données structurées : Google peut afficher le prix et la disponibilité dans ses résultats. --}}
        <script type="application/ld+json">{!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->fullName(),
            'description' => $product->description,
            'image' => $photos->map->url()->all() ?: null,
            'brand' => $product->brand ? ['@type' => 'Brand', 'name' => $product->brand->name] : null,
            'color' => $product->color?->name,
            'offers' => [
                '@type' => 'Offer',
                'url' => route('product', $product),
                'priceCurrency' => 'XOF',
                'price' => $product->price,
                'availability' => $soldOut ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock',
            ],
        ]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @endpush

    <div class="container-page pt-6 pb-32 sm:pt-10 lg:pb-0">
        <nav class="text-sm text-gris" aria-label="{{ __('Fil d\'Ariane') }}">
            <a href="{{ route('shop') }}" class="hover:text-encre">{{ __('Chaussures') }}</a>
            <span aria-hidden="true" class="mx-1">/</span>
            <a href="{{ route('shop.category', $product->category) }}" class="hover:text-encre">{{ $product->category->name }}</a>
        </nav>

        <div class="mt-4 grid gap-8 lg:grid-cols-[1.2fr_1fr] lg:gap-14">
            {{-- Visuels --}}
            <div data-gallery class="lg:sticky lg:top-24 lg:self-start">
                <div class="relative aspect-square overflow-hidden rounded-3xl {{ $product->tintClass() }}">
                    @if ($photos->isNotEmpty())
                        <img data-gallery-main src="{{ $photos->first()->url() }}" alt="{{ $product->fullName() }}" class="size-full object-cover" fetchpriority="high">
                    @else
                        <x-product-visual :product="$product" />
                    @endif
                    <div class="absolute top-4 left-4 flex flex-col items-start gap-1.5">
                        @if ($product->isOnSale())<span class="badge bg-rouge px-3 py-1 text-sm text-white">−{{ $product->discountPercent() }} %</span>@endif
                        @if ($product->isNew())<span class="badge bg-encre px-3 py-1 text-sm text-white">{{ __('Nouveau') }}</span>@endif
                    </div>
                </div>
                @if ($photos->count() > 1)
                    <div class="mt-3 grid grid-cols-5 gap-2">
                        @foreach ($photos as $photo)
                            <button type="button" data-gallery-thumb data-src="{{ $photo->url() }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" class="aspect-square overflow-hidden rounded-xl bg-sable ring-encre aria-[current=true]:ring-2" aria-label="{{ __('Photo :n', ['n' => $loop->iteration]) }}">
                                <img src="{{ $photo->url() }}" alt="" class="size-full object-cover" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Informations et commande --}}
            <div>
                @if ($product->brand)
                    <a href="{{ route('shop', ['marque' => [$product->brand->slug]]) }}" class="text-sm font-bold tracking-[.16em] text-gris uppercase hover:text-encre">{{ $product->brand->name }}</a>
                @endif
                <h1 class="mt-1 text-5xl sm:text-6xl">{{ $product->name }}</h1>
                <x-price :product="$product" size="lg" class="mt-4" />

                @if ($product->color)
                    <div class="mt-6">
                        <p class="text-sm"><span class="font-semibold">{{ __('Couleur :') }}</span> {{ $product->color->name }}</p>
                        @if ($siblings->isNotEmpty())
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span class="flex size-11 items-center justify-center rounded-full ring-2 ring-encre ring-offset-2 ring-offset-craie" title="{{ $product->color->name }}"><x-color-dot :color="$product->color" class="size-9" /></span>
                                @foreach ($siblings as $sibling)
                                    @if ($sibling->color)
                                        <a href="{{ route('product', $sibling) }}" class="flex size-11 items-center justify-center rounded-full hover:ring-2 hover:ring-ligne" title="{{ $sibling->color->name }}" aria-label="{{ __('Voir en :c', ['c' => $sibling->color->name]) }}"><x-color-dot :color="$sibling->color" class="size-9" /></a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <form id="add-form" method="post" action="{{ route('cart.add') }}" class="mt-7" data-once>
                    @csrf
                    <fieldset data-size-picker>
                        <legend class="flex w-full items-center justify-between text-sm">
                            <span class="font-semibold">{{ __('Pointure (EU)') }}</span>
                            <a href="{{ route('how') }}#pointures" class="inline-flex items-center gap-1 font-semibold text-orange-fonce underline underline-offset-2"><x-icon name="ruler" class="size-4" />{{ __('Guide des pointures') }}</a>
                        </legend>
                        <div class="mt-3 grid grid-cols-4 gap-2 sm:grid-cols-6">
                            @foreach ($product->sizes as $size)
                                @php($out = $size->stock < 1)
                                <label @class([
                                    'relative flex h-14 flex-col items-center justify-center rounded-xl border text-lg font-bold transition select-none',
                                    'cursor-pointer border-ligne bg-white hover:border-encre has-checked:border-encre has-checked:bg-encre has-checked:text-white has-focus-visible:outline-3 has-focus-visible:outline-offset-2 has-focus-visible:outline-encre' => ! $out,
                                    'cursor-not-allowed border-dashed border-ligne bg-transparent text-gris/60 line-through' => $out,
                                ])>
                                    <input type="radio" name="size_id" value="{{ $size->id }}" data-size="{{ $size->size }}" class="sr-only" @disabled($out) @checked((string) $preselected === (string) $size->id) required>
                                    {{ $size->size }}
                                    @if (! $out && $size->stock === 1)
                                        <span class="absolute -top-2 rounded-full bg-orange px-1.5 text-[10px] leading-4 font-extrabold text-encre no-underline">{{ __('Dernière') }}</span>
                                    @endif
                                    @if ($out)<span class="sr-only">{{ __('épuisée') }}</span>@endif
                                </label>
                            @endforeach
                        </div>
                        @error('size_id')<p class="field-error" role="alert">{{ $message }}</p>@enderror
                    </fieldset>

                    @if ($soldOut)
                        <p class="mt-6 rounded-2xl bg-sable px-4 py-3 text-sm font-semibold">{{ __('Ce modèle est épuisé pour le moment. Écrivez-nous : on vous prévient dès son retour.') }}</p>
                        <a href="{{ route('wa.product', $product) }}" rel="nofollow" class="btn-wa mt-4 w-full py-4"><x-icon name="whatsapp" />{{ __('Me prévenir sur WhatsApp') }}</a>
                    @else
                        <div class="mt-6 hidden gap-3 sm:flex">
                            <button type="submit" name="action" value="buy" class="btn-primary flex-1 py-4 text-base">{{ __('Commander maintenant') }}<x-icon name="arrow" class="size-5" /></button>
                            <button type="submit" name="action" value="cart" class="btn-outline flex-1 py-4 text-base"><x-icon name="bag" />{{ __('Ajouter au panier') }}</button>
                        </div>
                    @endif
                </form>

                <a href="{{ route('wa.product', $product) }}" rel="nofollow" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-vert-fonce underline-offset-4 hover:underline">
                    <x-icon name="whatsapp" class="size-5" />{{ __('Une question sur ce modèle ? Écrivez-nous') }}
                </a>

                <ul class="mt-8 divide-y divide-ligne rounded-2xl border border-ligne bg-white text-sm">
                    <li class="flex gap-3 p-4"><x-icon name="truck" class="size-5 shrink-0" /><span><strong>{{ Setting::get('delivery_delay') }}</strong> · {{ __('Livraison : :f', ['f' => \App\Support\WhatsApp::deliveryLabel(Setting::deliveryFee())]) }}</span></li>
                    <li class="flex gap-3 p-4"><x-icon name="wallet" class="size-5 shrink-0" /><span>{{ __('Paiement à la livraison en espèces, ou par Mobile Money (Orange, MTN, Moov, Wave).') }}</span></li>
                    <li class="flex gap-3 p-4"><x-icon name="swap" class="size-5 shrink-0" /><span>{{ Setting::get('exchange_policy') }}</span></li>
                </ul>

                @if ($product->description)
                    <div class="mt-8">
                        <h2 class="text-2xl">{{ __('Description') }}</h2>
                        <div class="mt-3 space-y-3 leading-relaxed text-encre/85">
                            @foreach (preg_split("/\R{2,}/", trim($product->description)) as $para)
                                <p>{!! nl2br(e($para)) !!}</p>
                            @endforeach
                        </div>
                    </div>
                @endif
                <dl class="mt-6 grid grid-cols-[auto_1fr] gap-x-6 gap-y-2 text-sm">
                    <dt class="text-gris">{{ __('Catégorie') }}</dt><dd><a href="{{ route('shop.category', $product->category) }}" class="underline underline-offset-2">{{ $product->category->name }}</a></dd>
                    @if ($product->brand)<dt class="text-gris">{{ __('Marque') }}</dt><dd>{{ $product->brand->name }}</dd>@endif
                    @if ($product->color)<dt class="text-gris">{{ __('Couleur') }}</dt><dd>{{ $product->color->name }}</dd>@endif
                    <dt class="text-gris">{{ __('Pointures') }}</dt><dd>{{ $product->sizes->first()?->size }}–{{ $product->sizes->last()?->size }} (EU)</dd>
                </dl>
            </div>
        </div>
    </div>

    @if ($related->isNotEmpty())
        <section class="container-page pt-16 sm:pt-24">
            <h2 class="text-4xl sm:text-5xl">{{ __('Vous aimerez aussi') }}</h2>
            <div class="mt-6 grid grid-cols-2 gap-x-3 gap-y-8 sm:gap-x-5 lg:grid-cols-4">
                @foreach ($related as $item)
                    <x-product-card :product="$item" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Barre d'achat fixe sur mobile : le bouton reste sous le pouce --}}
    @unless ($soldOut)
        <div class="fixed inset-x-0 bottom-0 z-30 border-t border-ligne bg-white/95 px-4 pt-3 pb-[max(.75rem,env(safe-area-inset-bottom))] backdrop-blur sm:hidden">
            <div class="flex items-center gap-3">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs text-gris">{{ __('Pointure') }} <strong data-size-label class="text-encre">{{ $preselected ? $product->sizes->firstWhere('id', $preselected)?->size : '—' }}</strong></p>
                    <p class="font-display text-xl font-extrabold [font-stretch:85%]">{{ Money::fcfa($product->price) }}</p>
                </div>
                <button type="submit" form="add-form" name="action" value="cart" class="btn-outline size-13 shrink-0 p-0" aria-label="{{ __('Ajouter au panier') }}"><x-icon name="bag" class="size-6" /></button>
                <button type="submit" form="add-form" name="action" value="buy" class="btn-primary shrink-0 px-5 py-3.5">{{ __('Commander') }}<x-icon name="arrow" class="size-5" /></button>
            </div>
        </div>
    @endunless
</x-layouts.public>
