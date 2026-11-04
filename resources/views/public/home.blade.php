@php
    use App\Models\Setting;
    $hero = $featured->first() ?? $latest->first();
@endphp
<x-layouts.public>
    {{--
        Accueil : fond blanc et grand titre noir (sobre, lisible), la couleur est concentrée
        dans le panneau produit à droite, comme une vitrine.
    --}}
    <section class="bg-white">
        <div class="container-page grid items-center gap-10 pt-8 pb-12 lg:grid-cols-[1fr_1.05fr] lg:gap-14 lg:pt-14 lg:pb-16">
            <div>
                <p class="eyebrow text-gris">{{ __('Chaussures homme · Abidjan') }}</p>
                <h1 class="mt-5 text-[clamp(2.8rem,7.5vw,5.6rem)] leading-[.92]">{{ Setting::get('hero_title') }}</h1>
                <p class="mt-6 max-w-lg text-lg leading-relaxed text-gris">{{ Setting::get('hero_subtitle') }}</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('shop') }}" class="btn-primary px-8 py-4 text-base">{{ __('Voir les chaussures') }}<x-icon name="arrow" class="size-5" /></a>
                    <a href="{{ route('how') }}" class="btn-outline px-8 py-4 text-base">{{ __('Comment commander') }}</a>
                </div>
                <ul class="mt-10 grid gap-4 border-t border-ligne pt-6 text-sm sm:grid-cols-3">
                    @foreach ([
                        ['truck', 'Livré en 24 h', 'partout à Abidjan', 'bg-peche'],
                        ['wallet', 'Payé à la livraison', 'ou Mobile Money', 'bg-menthe'],
                        ['swap', 'Échange 48 h', 'si la pointure ne va pas', 'bg-azur'],
                    ] as [$icon, $strong, $rest, $bg])
                        <li class="flex items-center gap-3">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $bg }}"><x-icon :name="$icon" class="size-5" /></span>
                            <span><strong class="block font-semibold">{{ __($strong) }}</strong><span class="text-gris">{{ __($rest) }}</span></span>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if ($hero)
                <a href="{{ route('product', $hero) }}" class="group relative block overflow-hidden rounded-[2rem] bg-orange" aria-label="{{ $hero->fullName() }}">
                    <div class="relative aspect-[4/3.4] sm:aspect-[5/4]">
                        <div aria-hidden="true" class="absolute -top-[18%] -right-[12%] size-[75%] rounded-full bg-jaune transition duration-700 ease-out-soft group-hover:scale-105"></div>
                        <div aria-hidden="true" class="absolute -bottom-[30%] -left-[15%] size-[55%] rounded-full bg-white/15"></div>
                        <div class="absolute inset-0 flex items-center justify-center pb-[18%]">
                            @if ($hero->coverPhoto)
                                <img src="{{ $hero->coverPhoto->url() }}" alt="" class="size-full object-cover" fetchpriority="high">
                            @else
                                <x-shoe :silhouette="$hero->category->silhouette" :hex="$hero->color?->hex" class="w-[66%] -rotate-12 drop-shadow-[0_28px_24px_rgb(90_30_0/.35)] transition duration-700 ease-out-soft group-hover:-rotate-6" />
                            @endif
                        </div>
                        <div class="absolute bottom-4 left-4 flex items-end gap-3 rounded-2xl bg-white px-4 py-3 shadow-xl shadow-encre/15 sm:bottom-6 sm:left-6">
                            <div>
                                <p class="text-[11px] font-bold tracking-[.16em] text-gris uppercase">{{ $hero->brand?->name ?? __('À la une') }}</p>
                                <p class="font-semibold">{{ $hero->name }}</p>
                                <x-price :product="$hero" />
                            </div>
                            <span class="mb-1 flex size-9 items-center justify-center rounded-full bg-encre text-white transition group-hover:bg-orange group-hover:text-encre"><x-icon name="arrow" class="size-4" /></span>
                        </div>
                        <span class="absolute top-4 left-4 badge bg-white px-3 py-1 text-encre sm:top-6 sm:left-6">{{ __('À la une') }}</span>
                    </div>
                </a>
            @endif
        </div>
    </section>

    {{-- Accès direct par pointure : le premier réflexe d'un acheteur de chaussures --}}
    <section class="container-page pt-4 sm:pt-8">
        <div class="flex flex-col gap-5 rounded-3xl bg-ciel p-6 sm:p-8 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="eyebrow before:bg-encre">{{ __('Achat express') }}</p>
                <h2 class="mt-2 text-3xl sm:text-4xl">{{ __('Quelle est votre pointure ?') }}</h2>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach (range(39, 46) as $size)
                    <a href="{{ route('shop', ['pointure' => [$size]]) }}" class="flex size-14 items-center justify-center rounded-2xl bg-white font-display text-2xl transition [font-stretch:85%] hover:-translate-y-0.5 hover:bg-encre hover:text-white">{{ $size }}</a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Catégories --}}
    @if ($categories->isNotEmpty())
        <section class="container-page pt-14 sm:pt-20">
            <h2 class="text-4xl sm:text-5xl">{{ __('Trouver son style') }}</h2>
            @php($catCols = [1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4', 5 => 'lg:grid-cols-5'][min($categories->count(), 5)])
            <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4 {{ $catCols }}">
                @foreach ($categories as $cat)
                    @php($tile = ['bg-orange', 'bg-vert-vif', 'bg-jaune', 'bg-ciel', 'bg-peche'][$loop->index % 5])
                    <a href="{{ route('shop.category', $cat) }}" data-reveal style="--i: {{ $loop->index }}" @class([
                        'group relative flex flex-col justify-between overflow-hidden rounded-3xl p-5 text-encre transition duration-500 ease-out-soft hover:-translate-y-1',
                        $tile,
                        'col-span-2 lg:col-span-1' => $loop->first,
                    ])>
                        <div>
                            <h3 class="text-xl uppercase [overflow-wrap:normal] sm:text-3xl">{{ $cat->name }}</h3>
                            <p class="mt-1 text-sm font-medium">{{ trans_choice(':count modèle|:count modèles', $cat->products_count) }}</p>
                        </div>
                        <x-shoe :silhouette="$cat->silhouette" hex="#ffffff" class="mt-4 w-[85%] self-end transition duration-500 ease-out-soft group-hover:-rotate-6" />
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Sélection --}}
    @if ($featured->isNotEmpty())
        <section class="container-page pt-14 sm:pt-20">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="eyebrow text-gris">{{ __('Sélection') }}</p>
                    <h2 class="mt-2 text-4xl sm:text-5xl">{{ __('Les incontournables') }}</h2>
                </div>
                <a href="{{ route('shop') }}" class="hidden shrink-0 items-center gap-2 font-semibold underline-offset-4 hover:underline sm:flex">{{ __('Tout voir') }}<x-icon name="arrow" class="size-4" /></a>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-x-3 gap-y-8 sm:gap-x-5 lg:grid-cols-4">
                @foreach ($featured as $product)
                    <div data-reveal style="--i: {{ $loop->index % 4 }}"><x-product-card :product="$product" /></div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Comment ça marche --}}
    <section class="container-page pt-14 sm:pt-20">
        <div class="overflow-hidden rounded-3xl bg-vert-vif p-6 text-encre sm:p-10 lg:p-14">
            <p class="eyebrow before:bg-encre">{{ __('Simple comme un message') }}</p>
            <h2 class="mt-3 max-w-2xl text-4xl sm:text-5xl">{{ __('Commandé en 1 minute, livré chez vous') }}</h2>
            <ol class="mt-10 grid gap-8 sm:grid-cols-3">
                @foreach ([
                    ['Choisissez', 'Votre modèle et votre pointure. Les pointures affichées sont en stock, vraiment.'],
                    ['Validez', 'Nom, téléphone, commune : c\'est tout. Pas de compte à créer, pas de carte bancaire.'],
                    ['Recevez', 'On vous confirme sur WhatsApp, le livreur vous appelle. Vous payez à la réception ou par Mobile Money.'],
                ] as $i => [$step, $text])
                    <li data-reveal style="--i: {{ $i }}">
                        <span class="flex size-16 items-center justify-center rounded-full bg-white font-display text-4xl [font-stretch:75%]">0{{ $i + 1 }}</span>
                        <h3 class="mt-4 text-2xl uppercase">{{ __($step) }}</h3>
                        <p class="mt-2 leading-relaxed font-medium">{{ __($text) }}</p>
                    </li>
                @endforeach
            </ol>
            <a href="{{ route('shop') }}" class="btn-dark mt-10 px-8 py-4 hover:bg-white hover:text-encre">{{ __('Je choisis mes chaussures') }}<x-icon name="arrow" class="size-5" /></a>
        </div>
    </section>

    {{-- Nouveautés --}}
    @if ($latest->isNotEmpty())
        <section class="container-page pt-14 sm:pt-20">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="eyebrow text-gris">{{ __('Fraîchement arrivées') }}</p>
                    <h2 class="mt-2 text-4xl sm:text-5xl">{{ __('Nouveautés') }}</h2>
                </div>
                <a href="{{ route('shop', ['tri' => 'nouveautes']) }}" class="hidden shrink-0 items-center gap-2 font-semibold underline-offset-4 hover:underline sm:flex">{{ __('Tout voir') }}<x-icon name="arrow" class="size-4" /></a>
            </div>
            <div class="-mx-4 mt-6 flex snap-x snap-mandatory gap-3 overflow-x-auto px-4 pb-2 [scrollbar-width:thin] sm:gap-5 lg:mx-0 lg:grid lg:grid-cols-4 lg:overflow-visible lg:px-0">
                @foreach ($latest->take(8) as $product)
                    <div class="w-[46%] shrink-0 snap-start sm:w-[31%] lg:w-auto">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Garanties --}}
    <section class="container-page pt-14 sm:pt-20">
        <ul class="grid gap-3 sm:grid-cols-3">
            @foreach ([
                ['truck', Setting::get('delivery_delay'), Setting::get('delivery_outside'), 'bg-peche', 'bg-orange'],
                ['wallet', __('Payez à la réception'), __('En espèces au livreur, ou par Orange Money, MTN MoMo, Moov Money et Wave.'), 'bg-menthe', 'bg-vert-vif'],
                ['swap', __('Pointure échangée'), Setting::get('exchange_policy'), 'bg-azur', 'bg-ciel'],
            ] as [$icon, $titleText, $text, $bg, $iconBg])
                <li class="flex gap-4 rounded-3xl {{ $bg }} p-5">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl {{ $iconBg }} text-encre"><x-icon :name="$icon" class="size-6" /></span>
                    <div>
                        <p class="font-display text-xl uppercase">{{ $titleText }}</p>
                        <p class="mt-1 text-sm leading-relaxed">{{ $text }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>
</x-layouts.public>
