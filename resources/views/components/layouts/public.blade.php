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
    <meta name="theme-color" content="#121212">
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
    <a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:font-semibold">{{ __('Aller au contenu') }}</a>

    {{-- Bandeau d'arguments : ce qui rassure avant d'acheter en ligne --}}
    <div class="bg-encre text-white">
        <div class="container-page flex h-9 items-center justify-center gap-6 overflow-hidden text-xs font-semibold whitespace-nowrap sm:justify-between">
            <p class="flex items-center gap-2"><x-icon name="truck" class="size-4 text-orange" />{{ Setting::get('delivery_delay') }}</p>
            <p class="hidden items-center gap-2 sm:flex"><x-icon name="wallet" class="size-4 text-orange" />{{ __('Paiement à la livraison ou Mobile Money') }}</p>
            <p class="hidden items-center gap-2 lg:flex"><x-icon name="swap" class="size-4 text-orange" />{{ __('Échange de pointure sous 48 h') }}</p>
        </div>
    </div>

    <header data-header class="sticky top-0 z-40 border-b border-transparent bg-white/95 backdrop-blur transition-[border-color] duration-300 [&.is-scrolled]:border-ligne">
        <div class="container-page flex h-16 items-center gap-3 lg:h-[4.5rem]">
            {{-- Menu mobile sans JavaScript (<details>) --}}
            <details class="group xl:hidden">
                <summary class="-ml-2 flex size-11 cursor-pointer list-none items-center justify-center rounded-full hover:bg-sable" aria-label="{{ __('Menu') }}">
                    <x-icon name="menu" class="size-6 group-open:hidden" />
                    <x-icon name="close" class="hidden size-6 group-open:block" />
                </summary>
                <div class="menu-panel absolute inset-x-0 top-16 lg:top-[4.5rem] max-h-[calc(100dvh-6.25rem)] overflow-y-auto border-b border-ligne bg-white shadow-2xl shadow-encre/10">
                    <nav class="container-page flex flex-col py-4" aria-label="{{ __('Menu mobile') }}">
                        <a href="{{ route('shop') }}" class="py-3 font-display text-2xl uppercase">{{ __('Toutes les chaussures') }}</a>
                        @foreach ($navCategories as $cat)
                            <a href="{{ route('shop.category', $cat) }}" class="py-2.5 font-display text-2xl uppercase">{{ $cat->name }}</a>
                        @endforeach
                        <a href="{{ route('shop', ['promo' => 1]) }}" class="py-2.5 font-display text-2xl text-rouge uppercase">{{ __('Promos') }}</a>
                        <div class="mt-3 border-t border-ligne pt-3">
                            <a href="{{ route('how') }}" class="block py-2.5 font-semibold">{{ __('Livraison et paiement') }}</a>
                            <a href="{{ route('contact') }}" class="block py-2.5 font-semibold">{{ __('Contact') }}</a>
                        </div>
                        <a href="{{ route('wa.contact') }}" rel="nofollow" class="btn-wa mt-4"><x-icon name="whatsapp" />{{ __('Une question ? WhatsApp') }}</a>
                    </nav>
                </div>
            </details>

            <a href="{{ route('home') }}" class="text-[17px] lg:text-xl" aria-label="{{ __('Abidjan Shop, accueil') }}"><x-logo /></a>

            <nav class="ml-6 hidden items-center gap-0.5 text-[15px] font-semibold whitespace-nowrap xl:flex" aria-label="{{ __('Menu principal') }}">
                @foreach ($navCategories as $cat)
                    <a href="{{ route('shop.category', $cat) }}" @class([
                        'rounded-full px-3.5 py-2 transition hover:bg-sable',
                        'bg-encre text-white hover:bg-encre' => request()->route('category')?->is($cat),
                    ])>{{ $cat->name }}</a>
                @endforeach
                <a href="{{ route('shop', ['promo' => 1]) }}" class="rounded-full px-3.5 py-2 text-rouge transition hover:bg-rouge-pale">{{ __('Promos') }}</a>
            </nav>

            <div class="ml-auto flex items-center gap-1">
                <form action="{{ route('shop') }}" method="get" role="search" class="relative hidden md:block">
                    <label for="recherche" class="sr-only">{{ __('Rechercher une chaussure ou une marque') }}</label>
                    <input id="recherche" name="q" type="search" value="{{ request('q') }}" placeholder="{{ __('Rechercher…') }}" class="h-11 w-44 rounded-full border border-ligne bg-white pr-4 pl-10 text-sm transition focus:w-60 focus:border-encre focus:outline-none xl:w-56">
                    <x-icon name="search" class="pointer-events-none absolute top-3 left-3.5 size-5 text-gris" />
                </form>
                <a href="{{ route('shop') }}#recherche-boutique" class="flex size-11 items-center justify-center rounded-full hover:bg-sable md:hidden" aria-label="{{ __('Rechercher') }}"><x-icon name="search" class="size-6" /></a>
                <a href="{{ route('cart') }}" class="relative flex h-11 items-center gap-2 rounded-full pr-2 pl-2.5 hover:bg-sable sm:pr-4" aria-label="{{ trans_choice('Panier, :count article|Panier, :count articles', $cartCount) }}">
                    <x-icon name="bag" class="size-6" />
                    <span class="hidden text-sm font-semibold sm:inline">{{ __('Panier') }}</span>
                    @if ($cartCount > 0)
                        <span @class(['absolute top-1 left-6 flex min-w-5 items-center justify-center rounded-full bg-orange px-1 text-[11px] leading-5 font-extrabold text-encre', 'animate-pop' => session('cart_added')])>{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    @if (session('cart_added'))
        <div class="container-page relative z-30 mt-3" role="status">
            <div class="flex flex-col gap-3 rounded-2xl bg-encre px-4 py-3 text-white sm:flex-row sm:items-center sm:justify-between">
                <p class="flex items-center gap-2 text-sm font-semibold"><span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-vert"><x-icon name="check" class="size-4" /></span>{{ session('cart_added') }}</p>
                <div class="flex gap-2">
                    <a href="{{ route('cart') }}" class="btn-outline-light px-4 py-2 text-sm">{{ __('Voir le panier') }}</a>
                    <a href="{{ route('checkout') }}" class="btn-primary px-4 py-2 text-sm">{{ __('Commander') }}<x-icon name="arrow" class="size-4" /></a>
                </div>
            </div>
        </div>
    @endif
    @foreach (['status' => 'bg-white border-ligne', 'error' => 'bg-rouge-pale border-rouge/30 text-rouge'] as $key => $classes)
        @if (session($key))
            <div class="container-page mt-3" role="{{ $key === 'error' ? 'alert' : 'status' }}">
                <p class="rounded-2xl border px-4 py-3 text-sm font-semibold {{ $classes }}">{{ session($key) }}</p>
            </div>
        @endif
    @endforeach

    <main id="contenu" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="mt-24 bg-encre text-white">
        <div class="container-page grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]">
            <div>
                <a href="{{ route('home') }}" class="text-2xl"><x-logo tagline /></a>
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-gris-clair">{{ __('Chaussures homme sélectionnées pour le style et le confort. Commandez à toute heure, on livre partout à Abidjan.') }}</p>
            </div>
            <div>
                <p class="mb-4 text-xs font-bold tracking-[.18em] text-orange uppercase">{{ __('Boutique') }}</p>
                <ul class="space-y-2.5 text-sm text-gris-clair">
                    <li><a href="{{ route('shop') }}" class="hover:text-white">{{ __('Toutes les chaussures') }}</a></li>
                    @foreach ($navCategories as $cat)
                        <li><a href="{{ route('shop.category', $cat) }}" class="hover:text-white">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <p class="mb-4 text-xs font-bold tracking-[.18em] text-orange uppercase">{{ __('Aide') }}</p>
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
                <p class="mb-4 text-xs font-bold tracking-[.18em] text-orange uppercase">{{ __('On vous répond') }}</p>
                <p class="text-sm leading-relaxed text-gris-clair">{{ Setting::get('contact_hours') }}</p>
                <a href="{{ route('wa.contact') }}" rel="nofollow" class="btn-wa mt-5 py-2.5 text-sm"><x-icon name="whatsapp" class="size-4" />{{ __('Écrire sur WhatsApp') }}</a>
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
            <x-icon name="whatsapp" class="size-7" />
        </a>
    @endif
    @stack('scripts')
</body>
</html>
