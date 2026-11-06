@php
    use App\Models\Setting;
    $hero = $featured->first() ?? $latest->first();
@endphp
<x-layouts.public>
    <div class="container-page space-y-10 pt-4 sm:space-y-14 sm:pt-6">
        {{-- Bannière : message court, un bouton, une paire en vedette. Compacte pour laisser voir les produits dessous. --}}
        <section class="relative isolate overflow-hidden rounded-2xl bg-vert text-white">
            <div aria-hidden="true" class="absolute -right-16 -bottom-24 -z-10 size-72 rounded-full bg-white/10 sm:size-96"></div>
            <div class="grid items-center gap-4 p-5 sm:grid-cols-[1.2fr_1fr] sm:p-8 lg:p-10">
                <div>
                    <p class="inline-flex rounded-md bg-jaune px-2.5 py-1 text-xs font-bold text-encre">{{ __('Paiement à la livraison') }}</p>
                    <h1 class="mt-3 text-3xl sm:text-4xl lg:text-5xl">{{ Setting::get('hero_title') }}</h1>
                    <p class="mt-2 max-w-md text-[15px] text-white/90 sm:text-base">{{ Setting::get('hero_subtitle') }}</p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <a href="{{ route('shop') }}" class="btn-light">{{ __('Voir les chaussures') }}<x-icon name="arrow" class="size-4" /></a>
                        <a href="{{ route('shop', ['promo' => 1]) }}" class="btn-outline-light">{{ __('Les promos') }}</a>
                    </div>
                </div>
                @if ($hero)
                    <a href="{{ route('product', $hero) }}" class="relative mx-auto hidden aspect-square w-full max-w-xs items-center justify-center sm:flex lg:max-w-sm" aria-label="{{ $hero->fullName() }}">
                        <span aria-hidden="true" class="absolute inset-[6%] rounded-full bg-white"></span>
                        <x-shoe :silhouette="$hero->category->silhouette" :hex="$hero->color?->hex" class="relative w-[92%] -translate-y-[6%] -rotate-12 drop-shadow-xl" />
                        <span class="absolute right-0 bottom-0 rounded-lg bg-white px-3 py-2 text-encre shadow-lg">
                            <span class="block text-xs font-semibold text-gris">{{ $hero->brand?->name }} {{ $hero->name }}</span>
                            <x-price :product="$hero" />
                        </span>
                    </a>
                @endif
            </div>
        </section>

        {{-- Garanties --}}
        <ul class="grid grid-cols-2 gap-2 sm:gap-3 lg:grid-cols-4">
            @foreach ([
                ['wallet', __('Paiement à la livraison'), __('Espèces ou Mobile Money')],
                ['truck', Setting::get('delivery_delay'), __('Partout à Abidjan')],
                ['swap', __('Échange sous 48 h'), __('Si la pointure ne va pas')],
                ['whatsapp', __('Commande sur WhatsApp'), __('On vous répond vite')],
            ] as [$icon, $strong, $rest])
                <li class="flex items-center gap-3 rounded-xl bg-craie p-3 sm:p-4">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-white text-vert"><x-icon :name="$icon" class="size-5" /></span>
                    <span class="min-w-0 text-xs sm:text-sm"><strong class="block leading-tight">{{ $strong }}</strong><span class="text-gris">{{ $rest }}</span></span>
                </li>
            @endforeach
        </ul>

        {{-- Catégories --}}
        @if ($categories->isNotEmpty())
            <section>
                <h2 class="text-xl sm:text-2xl">{{ __('Catégories') }}</h2>
                <div class="-mx-4 mt-3 flex gap-3 overflow-x-auto px-4 pb-1 [scrollbar-width:none] sm:mx-0 sm:grid sm:grid-cols-5 sm:px-0">
                    @foreach ($categories as $cat)
                        <a href="{{ route('shop.category', $cat) }}" class="group w-28 shrink-0 text-center sm:w-auto">
                            <span class="flex aspect-square items-center justify-center rounded-2xl bg-craie p-3 transition group-hover:bg-vert-pale sm:aspect-[4/3] sm:px-8">
                                <x-shoe :silhouette="$cat->silhouette" hex="#151816" class="w-full" />
                            </span>
                            <span class="mt-2 block text-sm font-semibold leading-tight">{{ $cat->name }}</span>
                            <span class="text-xs text-gris">{{ trans_choice(':count modèle|:count modèles', $cat->products_count) }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Promos --}}
        @if ($deals->isNotEmpty())
            <section class="rounded-2xl bg-rouge-pale p-4 sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="flex items-center gap-2 text-xl text-rouge sm:text-2xl"><span class="rounded-md bg-rouge px-2 py-0.5 text-base text-white">%</span>{{ __('Promos du moment') }}</h2>
                    <a href="{{ route('shop', ['promo' => 1]) }}" class="text-sm font-bold text-rouge hover:underline">{{ __('Tout voir') }} →</a>
                </div>
                <div class="-mx-4 mt-4 flex gap-3 overflow-x-auto px-4 pb-1 sm:mx-0 sm:grid sm:grid-cols-3 sm:px-0 lg:grid-cols-4">
                    @foreach ($deals as $product)
                        <div class="w-[44%] shrink-0 sm:w-auto"><x-product-card :product="$product" /></div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Les plus demandés --}}
        @if ($featured->isNotEmpty())
            <section>
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl sm:text-2xl">{{ __('Les plus demandés') }}</h2>
                    <a href="{{ route('shop') }}" class="link-arrow">{{ __('Tout voir') }} →</a>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($featured as $product)
                        <x-product-card :product="$product" :loading="$loop->index < 4 ? 'eager' : 'lazy'" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Achat par pointure --}}
        <section class="rounded-2xl border border-ligne p-4 sm:p-6">
            <h2 class="text-xl sm:text-2xl">{{ __('Achetez par pointure') }}</h2>
            <p class="mt-1 text-sm text-gris">{{ __('Touchez votre pointure : on vous montre seulement les modèles disponibles.') }}</p>
            <div class="mt-4 grid grid-cols-4 gap-2 sm:grid-cols-8">
                @foreach (range(39, 46) as $size)
                    <a href="{{ route('shop', ['pointure' => [$size]]) }}" class="flex h-12 items-center justify-center rounded-lg border-2 border-ligne text-lg font-bold transition hover:border-vert hover:bg-vert hover:text-white">{{ $size }}</a>
                @endforeach
            </div>
        </section>

        {{-- Nouveautés --}}
        @if ($latest->isNotEmpty())
            <section>
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl sm:text-2xl">{{ __('Nouveautés') }}</h2>
                    <a href="{{ route('shop', ['tri' => 'nouveautes']) }}" class="link-arrow">{{ __('Tout voir') }} →</a>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($latest as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Comment commander + WhatsApp --}}
        <section class="grid gap-4 lg:grid-cols-[1.4fr_1fr]">
            <div class="rounded-2xl bg-craie p-5 sm:p-8">
                <h2 class="text-xl sm:text-2xl">{{ __('Comment commander ?') }}</h2>
                <ol class="mt-5 grid gap-5 sm:grid-cols-3">
                    @foreach ([
                        ['Choisissez', 'Votre modèle et votre pointure.'],
                        ['Validez', 'Nom, téléphone, commune. Pas de compte à créer.'],
                        ['Recevez', 'Le livreur vous appelle. Vous payez à la réception.'],
                    ] as $i => [$step, $text])
                        <li class="flex gap-3 sm:block">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-vert text-sm font-bold text-white">{{ $i + 1 }}</span>
                            <div class="sm:mt-3">
                                <h3 class="text-base">{{ __($step) }}</h3>
                                <p class="text-sm text-gris">{{ __($text) }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
            <div class="flex flex-col justify-center rounded-2xl bg-vert-pale p-5 sm:p-8">
                <h2 class="text-xl sm:text-2xl">{{ __('Vous ne trouvez pas votre modèle ?') }}</h2>
                <p class="mt-2 text-sm text-gris">{{ __('Envoyez-nous une photo sur WhatsApp : on le cherche pour vous.') }}</p>
                <a href="{{ route('wa.contact') }}" rel="nofollow" class="btn-wa mt-5 self-start"><x-icon name="whatsapp" class="size-5" />{{ __('Écrire sur WhatsApp') }}</a>
            </div>
        </section>
    </div>
</x-layouts.public>
