@php
    use App\Http\Controllers\ShopController;
    use App\Support\PriceRange;

    $routeFor = fn (array $query) => $category ? route('shop.category', [$category, ...$query]) : route('shop', $query);
    $query = request()->except('page');

    /** Lien qui retire une seule valeur d'un filtre (pastilles « filtres actifs »). */
    $without = function (string $key, $value = null) use ($query, $routeFor) {
        $q = $query;
        if ($value === null) {
            unset($q[$key]);
        } else {
            $q[$key] = array_values(array_diff((array) ($q[$key] ?? []), [(string) $value]));
        }
        return $routeFor($q);
    };

    $active = collect();
    if ($filters['q'] !== '') $active->push(['« '.$filters['q'].' »', $without('q')]);
    foreach ($filters['pointure'] as $s) $active->push([__('Pointure :s', ['s' => $s]), $without('pointure', $s)]);
    foreach ($facets['brands']->whereIn('slug', $filters['marque']) as $b) $active->push([$b->name, $without('marque', $b->slug)]);
    foreach ($facets['colors']->whereIn('slug', $filters['couleur']) as $c) $active->push([$c->name, $without('couleur', $c->slug)]);
    if ($filters['prix']) $active->push([PriceRange::find($filters['prix'])['label'], $without('prix')]);
    if ($filters['promo']) $active->push([__('En promo'), $without('promo')]);

    $heading = $category?->name ?? ($filters['promo'] ? __('Promos') : __('Toutes les chaussures'));
@endphp
<x-layouts.public
    :title="$category ? $category->name.' homme à Abidjan' : __('Chaussures homme')"
    :description="$category?->description ? $category->description.' '.__('Livraison à Abidjan, paiement à la livraison.') : null"
>
    <div class="container-page pt-8 sm:pt-12">
        <nav class="text-sm text-gris" aria-label="{{ __('Fil d\'Ariane') }}">
            <a href="{{ route('home') }}" class="hover:text-encre">{{ __('Accueil') }}</a>
            <span aria-hidden="true" class="mx-1">/</span>
            @if ($category)
                <a href="{{ route('shop') }}" class="hover:text-encre">{{ __('Chaussures') }}</a>
                <span aria-hidden="true" class="mx-1">/</span>
                <span class="text-encre" aria-current="page">{{ $category->name }}</span>
            @else
                <span class="text-encre" aria-current="page">{{ __('Chaussures') }}</span>
            @endif
        </nav>
        <div class="mt-3 flex flex-wrap items-end justify-between gap-x-6 gap-y-2">
            <h1 class="text-5xl sm:text-7xl">{{ $heading }}</h1>
            <p class="pb-1 text-sm font-semibold text-gris" aria-live="polite">{{ trans_choice(':count modèle|:count modèles', $products->total()) }}</p>
        </div>
        @if ($category?->description)
            <p class="mt-3 max-w-2xl text-gris">{{ $category->description }}</p>
        @endif

        {{-- Catégories : on garde les autres filtres en changeant de catégorie --}}
        <div class="-mx-4 mt-6 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] sm:mx-0 sm:flex-wrap sm:px-0">
            <a href="{{ route('shop', $query) }}" @class(['shrink-0 rounded-full border px-4 py-2.5 text-sm font-semibold transition', 'border-encre bg-encre text-white' => ! $category, 'border-ligne bg-white hover:border-encre' => $category])>{{ __('Tout') }}</a>
            @foreach ($categories as $cat)
                <a href="{{ route('shop.category', [$cat, ...$query]) }}" @class(['shrink-0 rounded-full border px-4 py-2.5 text-sm font-semibold transition', 'border-encre bg-encre text-white' => $category?->is($cat), 'border-ligne bg-white hover:border-encre' => ! $category?->is($cat)])>{{ $cat->name }}</a>
            @endforeach
        </div>
    </div>

    <div class="container-page mt-6 grid gap-8 lg:grid-cols-[17rem_1fr] lg:gap-10">
        {{-- Filtres. Mobile : panneau plein écran ouvert par une case à cocher (aucun JavaScript nécessaire). --}}
        <input type="checkbox" id="filters-toggle" class="peer sr-only" aria-hidden="true" tabindex="-1">
        <form id="filters" data-filters method="get" action="{{ $category ? route('shop.category', $category) : route('shop') }}"
              class="fixed inset-0 z-50 hidden flex-col bg-white peer-checked:flex lg:static lg:z-auto lg:flex lg:bg-transparent">
            <div class="flex items-center justify-between border-b border-ligne px-4 py-3 lg:hidden">
                <p class="font-display text-2xl uppercase">{{ __('Filtres') }}</p>
                <label for="filters-toggle" class="flex size-11 cursor-pointer items-center justify-center rounded-full hover:bg-sable" aria-label="{{ __('Fermer les filtres') }}"><x-icon name="close" class="size-6" /></label>
            </div>

            <div class="flex-1 space-y-7 overflow-y-auto px-4 py-5 lg:overflow-visible lg:p-0">
                <input type="hidden" name="tri" value="{{ $sort }}">
                <div>
                    <label for="recherche-boutique" class="field-label">{{ __('Recherche') }}</label>
                    <div class="relative">
                        <input id="recherche-boutique" name="q" type="search" value="{{ $filters['q'] }}" placeholder="{{ __('Modèle ou marque') }}" class="field pl-11">
                        <x-icon name="search" class="pointer-events-none absolute top-3.5 left-4 size-5 text-gris" />
                    </div>
                </div>

                @if ($facets['sizes']->isNotEmpty())
                    <fieldset>
                        <legend class="field-label flex w-full items-center justify-between">{{ __('Pointure') }} <a href="{{ route('how') }}#pointures" class="text-xs font-semibold text-orange-fonce underline underline-offset-2">{{ __('Guide') }}</a></legend>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach ($facets['sizes'] as $size)
                                <label class="chip rounded-xl px-0">
                                    <input type="checkbox" name="pointure[]" value="{{ $size }}" @checked(in_array($size, $filters['pointure'], true))>
                                    {{ $size }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endif

                @if ($facets['brands']->isNotEmpty())
                    <fieldset>
                        <legend class="field-label">{{ __('Marque') }}</legend>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($facets['brands'] as $brand)
                                <label class="chip">
                                    <input type="checkbox" name="marque[]" value="{{ $brand->slug }}" @checked(in_array($brand->slug, $filters['marque'], true))>
                                    {{ $brand->name }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endif

                @if ($facets['colors']->isNotEmpty())
                    <fieldset>
                        <legend class="field-label">{{ __('Couleur') }}</legend>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($facets['colors'] as $color)
                                <label class="chip pl-2.5">
                                    <input type="checkbox" name="couleur[]" value="{{ $color->slug }}" @checked(in_array($color->slug, $filters['couleur'], true))>
                                    <x-color-dot :color="$color" class="size-5" />
                                    {{ $color->name }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endif

                <fieldset>
                    <legend class="field-label">{{ __('Prix') }}</legend>
                    <div class="flex flex-wrap gap-2 lg:flex-col lg:items-start">
                        <label class="chip"><input type="radio" name="prix" value="" @checked(! $filters['prix'])>{{ __('Tous les prix') }}</label>
                        @foreach (PriceRange::all() as $key => $range)
                            <label class="chip"><input type="radio" name="prix" value="{{ $key }}" @checked($filters['prix'] === $key)>{{ $range['label'] }}</label>
                        @endforeach
                    </div>
                </fieldset>

                <label class="chip">
                    <input type="checkbox" name="promo" value="1" @checked($filters['promo'])>
                    {{ __('Uniquement les promos') }}
                </label>

                <noscript><button class="btn-dark hidden w-full lg:flex">{{ __('Appliquer les filtres') }}</button></noscript>
            </div>

            <div class="flex gap-3 border-t border-ligne bg-white px-4 pt-3 pb-[max(.75rem,env(safe-area-inset-bottom))] lg:hidden">
                <a href="{{ $routeFor(['tri' => $sort]) }}" class="btn-outline flex-1 px-4">{{ __('Effacer') }}</a>
                <button class="btn-dark flex-[2]">{{ __('Voir les résultats') }}</button>
            </div>
        </form>

        <div class="min-w-0">
            <div class="flex items-center justify-between gap-3">
                <label for="filters-toggle" class="btn-outline cursor-pointer px-5 py-2.5 text-sm lg:hidden">
                    <x-icon name="filter" class="size-5" />{{ __('Filtrer') }}
                    @if ($activeCount)<span class="flex size-5 items-center justify-center rounded-full bg-orange text-[11px] font-extrabold text-encre">{{ $activeCount }}</span>@endif
                </label>
                <div class="ml-auto flex items-center gap-2">
                    <label for="tri" class="hidden text-sm text-gris sm:block">{{ __('Trier par') }}</label>
                    <select id="tri" name="tri" form="sort-form" data-autosubmit class="field w-auto py-2.5 pr-10 text-sm font-semibold">
                        @foreach (ShopController::SORTS as $key => $label)
                            <option value="{{ $key }}" @selected($sort === $key)>{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            {{-- Le tri garde les filtres en cours --}}
            <form id="sort-form" method="get" action="{{ $category ? route('shop.category', $category) : route('shop') }}" class="hidden">
                @foreach (collect($query)->except('tri') as $key => $value)
                    @foreach ((array) $value as $v)
                        <input type="hidden" name="{{ is_array($value) ? $key.'[]' : $key }}" value="{{ $v }}">
                    @endforeach
                @endforeach
                <noscript><button>{{ __('Trier') }}</button></noscript>
            </form>

            @if ($active->isNotEmpty())
                <div class="mt-4 flex flex-wrap items-center gap-2">
                    @foreach ($active as [$label, $url])
                        <a href="{{ $url }}" class="inline-flex items-center gap-1.5 rounded-full bg-encre py-1.5 pr-2.5 pl-3.5 text-sm font-semibold text-white hover:bg-charbon" aria-label="{{ __('Retirer le filtre :f', ['f' => $label]) }}">{{ $label }}<x-icon name="close" class="size-4" /></a>
                    @endforeach
                    <a href="{{ $routeFor(['tri' => $sort]) }}" class="px-2 text-sm font-semibold underline underline-offset-4">{{ __('Tout effacer') }}</a>
                </div>
            @endif

            @if ($products->isEmpty())
                <div class="mt-8 rounded-3xl border border-dashed border-ligne bg-white px-6 py-14 text-center">
                    <x-shoe silhouette="basket" hex="#ebe5da" class="mx-auto w-40" />
                    <h2 class="mt-4 text-3xl">{{ __('Aucune paire ne correspond') }}</h2>
                    <p class="mx-auto mt-2 max-w-md text-gris">{{ __('Retirez un filtre, ou dites-nous ce que vous cherchez : on peut souvent le trouver pour vous.') }}</p>
                    <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                        <a href="{{ $routeFor([]) }}" class="btn-dark">{{ __('Voir tous les modèles') }}</a>
                        <a href="{{ route('wa.contact') }}" rel="nofollow" class="btn-wa"><x-icon name="whatsapp" />{{ __('Demander un modèle') }}</a>
                    </div>
                </div>
            @else
                <div class="mt-6 grid grid-cols-2 gap-x-3 gap-y-8 sm:gap-x-5 md:grid-cols-3">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" :loading="$loop->index < 6 ? 'eager' : 'lazy'" />
                    @endforeach
                </div>
                <div class="mt-12">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.public>
