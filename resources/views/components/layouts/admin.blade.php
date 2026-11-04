@props(['title' => null])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ? $title.' · ' : '' }}{{ __('Admin') }} Abidjan Shop</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-dvh bg-craie">
    @php
        $newOrders = \App\Models\Order::where('status', \App\Enums\OrderStatus::New)->count();
        $nav = [
            ['admin.dashboard', 'admin.dashboard', __('Tableau de bord'), null],
            ['admin.orders.index', 'admin.orders.*', __('Commandes'), $newOrders ?: null],
            ['admin.products.index', 'admin.products.*', __('Produits'), null],
            ['admin.categories.index', 'admin.categories.*', __('Catégories'), null],
            ['admin.brands.index', 'admin.brands.*', __('Marques'), null],
            ['admin.colors.index', 'admin.colors.*', __('Couleurs'), null],
            ['admin.settings', 'admin.settings*', __('Réglages'), null],
        ];
    @endphp
    <header class="sticky top-0 z-30 bg-encre text-white">
        <div class="mx-auto flex h-14 max-w-6xl items-center justify-between gap-4 px-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-[15px]"><x-logo /><span class="badge bg-orange text-encre">admin</span></a>
            <div class="flex items-center gap-1 text-sm">
                <a href="{{ route('home') }}" target="_blank" class="hidden rounded-lg px-3 py-2 font-medium text-gris-clair hover:bg-white/10 hover:text-white sm:block">{{ __('Voir le site') }} ↗</a>
                <a href="{{ route('admin.account') }}" class="rounded-lg px-3 py-2 font-medium text-gris-clair hover:bg-white/10 hover:text-white">{{ __('Mon compte') }}</a>
                <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="rounded-lg px-3 py-2 font-medium text-gris-clair hover:bg-white/10 hover:text-white">{{ __('Déconnexion') }}</button></form>
            </div>
        </div>
        <nav class="mx-auto flex max-w-6xl gap-1 overflow-x-auto px-3 pb-2.5 text-sm font-semibold [scrollbar-width:none]">
            @foreach ($nav as [$route, $pattern, $label, $count])
                <a href="{{ route($route) }}" @class(['flex shrink-0 items-center gap-1.5 rounded-full px-4 py-2 transition', 'bg-orange text-encre' => request()->routeIs($pattern), 'text-gris-clair hover:bg-white/10 hover:text-white' => ! request()->routeIs($pattern)])>
                    {{ $label }}
                    @if ($count)<span @class(['rounded-full px-1.5 text-[11px] leading-5 font-extrabold', 'bg-encre text-orange' => request()->routeIs($pattern), 'bg-orange text-encre' => ! request()->routeIs($pattern)])>{{ $count }}</span>@endif
                </a>
            @endforeach
        </nav>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6 sm:py-8">
        @if (session('status'))
            <div class="mb-5 flex items-center gap-2 rounded-xl bg-vert px-4 py-3 text-sm font-semibold text-white" role="status"><x-icon name="check" class="size-4" />{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-5 rounded-xl border border-rouge/30 bg-rouge-pale px-4 py-3 text-sm font-semibold text-rouge" role="alert">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-rouge/30 bg-rouge-pale px-4 py-3 text-sm text-rouge" role="alert">
                <p class="font-semibold">{{ __('Merci de corriger les points suivants :') }}</p>
                <ul class="mt-1 list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        {{ $slot }}
    </main>
</body>
</html>
