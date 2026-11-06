@props(['title' => null, 'description' => null, 'ogImage' => null, 'floatingWa' => true, 'noindex' => false])
@php
    use App\Models\Setting;
    $fullTitle = $title ? $title.' · Abidjan Shop' : 'Abidjan Shop · Chaussures homme à Abidjan, livrées chez vous';
    $metaDescription = $description ?? __('Baskets, mocassins, chaussures de ville et sandales pour homme. Commande en ligne 24 h/24, livraison partout à Abidjan, paiement à la livraison ou Mobile Money.');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    @if ($noindex)<meta name="robots" content="noindex, nofollow">@endif
    <meta name="theme-color" content="#00873f">
    <script>document.documentElement.classList.add('js')</script>
    <meta property="og:site_name" content="Abidjan Shop">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="flex min-h-dvh flex-col">
    <a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:rounded-xl focus:bg-white focus:px-4 focus:py-2 focus:font-semibold">{{ __('Aller au contenu') }}</a>

    {{-- Bandeau d'arguments : ce qui rassure avant d'acheter en ligne --}}
    <div class="bg-vert text-white">
        <div class="container-page flex h-9 items-center justify-center gap-8 overflow-hidden text-xs font-semibold whitespace-nowrap">
            <p class="flex items-center gap-1.5"><x-icon name="wallet" class="size-4" />{{ __('Paiement à la livraison') }}</p>
            <p class="hidden items-center gap-1.5 sm:flex"><x-icon name="truck" class="size-4" />{{ Setting::get('delivery_delay') }}</p>
            <p class="hidden items-center gap-1.5 lg:flex"><x-icon name="swap" class="size-4" />{{ __('Échange de pointure sous 48 h') }}</p>
        </div>
    </div>

    <header class="sticky top-0 z-40 border-b border-ligne bg-white">
        <div class="container-page flex h-16 items-center gap-3 lg:gap-6">
            {{-- Menu mobile sans JavaScript (<details>) --}}
            <details class="group lg:hidden">
                <summary class="-ml-2 flex size-11 cursor-pointer list-none items-center justify-center rounded-lg hover:bg-craie" aria-label="{{ __('Menu') }}">
                    <x-icon name="menu" class="size-6 group-open:hidden" />
                    <x-icon name="close" class="hidden size-6 group-open:block" />
                </summary>
                <div class="menu-panel absolute inset-x-0 top-16 max-h-[calc(100dvh-6.25rem)] overflow-y-auto border-b border-ligne bg-white shadow-xl">
                    <nav class="container-page flex flex-col py-3 font-semibold" aria-label="{{ __('Menu mobile') }}">
                        <a href="{{ route('shop') }}" class="py-3">{{ __('Toutes les chaussures') }}</a>
                        @foreach ($navCategories as $cat)
                            <a href="{{ route('shop.category', $cat) }}" class="py-3">{{ $cat->name }}</a>
                        @endforeach
                        <a href="{{ route('shop', ['promo' => 1]) }}" class="py-3 text-rouge">{{ __('Promos') }}</a>
                        <div class="mt-2 border-t border-ligne pt-2 font-medium text-gris">
                            <a href="{{ route('how') }}" class="block py-3">{{ __('Livraison et paiement') }}</a>
                            <a href="{{ route('contact') }}" class="block py-3">{{ __('Contact') }}</a>
                        </div>
                        <a href="{{ route('wa.contact') }}" rel="nofollow" class="btn-wa my-3"><x-icon name="whatsapp" class="size-5" />{{ __('Commander sur WhatsApp') }}</a>
                    </nav>
                </div>
            </details>

            <a href="{{ route('home') }}" class="shrink-0 text-[15px] sm:text-base" aria-label="{{ __('Abidjan Shop, accueil') }}"><x-logo /></a>

            <form action="{{ route('shop') }}" method="get" role="search" class="relative hidden flex-1 md:block">
                <label for="recherche" class="sr-only">{{ __('Rechercher une chaussure ou une marque') }}</label>
                <input id="recherche" name="q" type="search" value="{{ request('q') }}" placeholder="{{ __('Rechercher : Nike, mocassin, sandale…') }}" class="h-11 w-full rounded-lg border-2 border-ligne bg-white pr-28 pl-4 text-sm transition focus:border-vert focus:outline-none">
                <button class="absolute top-1 right-1 bottom-1 flex items-center gap-1.5 rounded-md bg-vert px-4 text-sm font-bold text-white hover:bg-vert-fonce"><x-icon name="search" class="size-4" />{{ __('Chercher') }}</button>
            </form>

            <div class="ml-auto flex items-center gap-1 md:ml-0">
                <a href="{{ route('wa.contact') }}" rel="nofollow" class="hidden items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold hover:bg-craie xl:flex"><x-icon name="whatsapp" class="size-5 text-vert" /><span>{{ __('Aide') }}<span class="block text-xs font-normal text-gris">{{ __('sur WhatsApp') }}</span></span></a>
                <a href="{{ route('cart') }}" class="relative flex h-11 items-center gap-2 rounded-lg px-2.5 font-semibold hover:bg-craie" aria-label="{{ trans_choice('Panier, :count article|Panier, :count articles', $cartCount) }}">
                    <x-icon name="bag" class="size-6" />
                    <span class="hidden text-sm sm:inline">{{ __('Panier') }}</span>
                    @if ($cartCount > 0)
                        <span @class(['absolute top-0.5 left-6 flex min-w-5 items-center justify-center rounded-full bg-rouge px-1 text-[11px] leading-5 font-bold text-white', 'animate-pop' => session('cart_added')])>{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>

        {{-- Recherche sur mobile : toujours visible, c'est le premier réflexe --}}
        <form action="{{ route('shop') }}" method="get" role="search" class="container-page pb-3 md:hidden">
            <label for="recherche-mobile" class="sr-only">{{ __('Rechercher une chaussure ou une marque') }}</label>
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-5 -translate-y-1/2 text-gris" />
                <input id="recherche-mobile" name="q" type="search" value="{{ request('q') }}" placeholder="{{ __('Nike, mocassin, pointure 42…') }}" class="h-11 w-full rounded-lg border-2 border-ligne bg-craie pr-4 pl-11 text-[15px] focus:border-vert focus:bg-white focus:outline-none">
            </div>
        </form>

        {{-- Catégories en pastilles (défilent au doigt sur mobile) --}}
        <nav class="border-t border-ligne" aria-label="{{ __('Catégories') }}">
            <div class="container-page flex gap-2 overflow-x-auto py-2 text-sm font-semibold whitespace-nowrap [scrollbar-width:none]">
                @php($onShop = request()->routeIs('shop') && ! request('promo'))
                <a href="{{ route('shop') }}" @class(['rounded-full px-4 py-2 transition', 'bg-encre text-white' => $onShop, 'bg-craie hover:bg-ligne' => ! $onShop])>{{ __('Tout') }}</a>
                @foreach ($navCategories as $cat)
                    @php($current = request()->route('category')?->is($cat))
                    <a href="{{ route('shop.category', $cat) }}" @class(['rounded-full px-4 py-2 transition', 'bg-encre text-white' => $current, 'bg-craie hover:bg-ligne' => ! $current])>{{ $cat->name }}</a>
                @endforeach
                <a href="{{ route('shop', ['promo' => 1]) }}" @class(['rounded-full px-4 py-2 transition', 'bg-rouge text-white' => request('promo'), 'bg-rouge-pale text-rouge hover:bg-rouge hover:text-white' => ! request('promo')])>{{ __('Promos') }}</a>
            </div>
        </nav>
    </header>

    @if (session('cart_added'))
        <div class="container-page relative z-30 mt-3" role="status">
            <div class="flex flex-col gap-3 rounded-xl bg-vert-pale px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="flex items-center gap-3 text-sm font-semibold"><span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-vert text-white"><x-icon name="check" class="size-4" /></span>{{ session('cart_added') }}</p>
                <div class="flex gap-2">
                    <a href="{{ route('cart') }}" class="btn-outline px-4 py-2 text-sm">{{ __('Voir le panier') }}</a>
                    <a href="{{ route('checkout') }}" class="btn-primary px-4 py-2 text-sm">{{ __('Commander') }}<x-icon name="arrow" class="size-4" /></a>
                </div>
            </div>
        </div>
    @endif
    @foreach (['status' => 'bg-white border-ligne', 'error' => 'bg-rouge-pale border-rouge/30 text-rouge'] as $key => $classes)
        @if (session($key))
            <div class="container-page mt-3" role="{{ $key === 'error' ? 'alert' : 'status' }}">
                <p class="rounded-xl border px-4 py-3 text-sm font-semibold {{ $classes }}">{{ session($key) }}</p>
            </div>
        @endif
    @endforeach

    <main id="contenu" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="mt-16 bg-charbon text-white">
        <div class="container-page grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]">
            <div>
                <a href="{{ route('home') }}" class="text-2xl"><x-logo tagline /></a>
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-gris-clair">{{ __('Chaussures homme sélectionnées pour le style et le confort. Commandez à toute heure, on livre partout à Abidjan.') }}</p>
            </div>
            <div>
                <p class="mb-4 text-sm font-bold text-white">{{ __('Boutique') }}</p>
                <ul class="space-y-2.5 text-sm text-gris-clair">
                    <li><a href="{{ route('shop') }}" class="hover:text-white">{{ __('Toutes les chaussures') }}</a></li>
                    @foreach ($navCategories as $cat)
                        <li><a href="{{ route('shop.category', $cat) }}" class="hover:text-white">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <p class="mb-4 text-sm font-bold text-white">{{ __('Aide') }}</p>
                <ul class="space-y-2.5 text-sm text-gris-clair">
                    <li><a href="{{ route('how') }}" class="hover:text-white">{{ __('Livraison et paiement') }}</a></li>
                    <li><a href="{{ route('how') }}#pointures" class="hover:text-white">{{ __('Guide des pointures') }}</a></li>
                    <li><a href="{{ route('how') }}#echanges" class="hover:text-white">{{ __('Échanges') }}</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white">{{ __('Contact') }}</a></li>
                    @foreach (['instagram_url' => 'Instagram', 'facebook_url' => 'Facebook', 'tiktok_url' => 'TikTok'] as $key => $label)
                        @if ($url = Setting::get($key))
                            <li><a href="{{ $url }}" target="_blank" rel="noopener" class="hover:text-white">{{ $label }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <div>
                <p class="mb-4 text-sm font-bold text-white">{{ __('On vous répond') }}</p>
                <p class="text-sm leading-relaxed text-gris-clair">{{ Setting::get('contact_hours') }}</p>
                <a href="{{ route('wa.contact') }}" rel="nofollow" class="btn-wa mt-5"><x-icon name="whatsapp" class="size-5" />{{ __('Écrire sur WhatsApp') }}</a>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="container-page flex flex-col gap-1 py-5 text-xs text-gris-clair sm:flex-row sm:justify-between">
                <p>© {{ date('Y') }} Abidjan Shop · {{ Setting::get('contact_address') }}</p>
                <p>{{ __('Prix en francs CFA.') }}</p>
            </div>
        </div>
    </footer>

    @if ($floatingWa)
        <a href="{{ route('wa.contact') }}" rel="nofollow" class="fixed right-4 bottom-[max(1rem,env(safe-area-inset-bottom))] z-30 flex size-14 items-center justify-center rounded-full bg-vert text-white shadow-xl shadow-encre/25 transition hover:scale-105 hover:bg-vert-fonce" aria-label="{{ __('Nous écrire sur WhatsApp') }}">
            <x-icon name="whatsapp" class="size-6" />
        </a>
    @endif
    @stack('scripts')
</body>
</html>
