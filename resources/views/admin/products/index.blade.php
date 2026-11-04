@php use App\Support\Money; @endphp
<x-layouts.admin :title="__('Produits')">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-4xl">{{ __('Produits') }}</h1>
        <a href="{{ route('admin.products.create') }}" class="a-btn-primary py-2.5">+ {{ __('Nouveau produit') }}</a>
    </div>

    <form method="get" class="mt-4 flex flex-wrap gap-2">
        <label for="q" class="sr-only">{{ __('Rechercher') }}</label>
        <input id="q" name="q" value="{{ request('q') }}" placeholder="{{ __('Nom du modèle') }}" class="field w-auto flex-1 py-2.5">
        <label for="categorie" class="sr-only">{{ __('Catégorie') }}</label>
        <select id="categorie" name="categorie" class="field w-auto py-2.5">
            <option value="">{{ __('Toutes catégories') }}</option>
            @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('categorie') == $c->id)>{{ $c->name }}</option>@endforeach
        </select>
        <label for="marque" class="sr-only">{{ __('Marque') }}</label>
        <select id="marque" name="marque" class="field w-auto py-2.5">
            <option value="">{{ __('Toutes marques') }}</option>
            @foreach ($brands as $b)<option value="{{ $b->id }}" @selected(request('marque') == $b->id)>{{ $b->name }}</option>@endforeach
        </select>
        <label class="flex items-center gap-2 px-2 text-sm"><input type="checkbox" name="stock" value="epuise" @checked(request('stock') === 'epuise') class="size-4 accent-encre">{{ __('Épuisés') }}</label>
        <button class="a-btn-ghost">{{ __('Filtrer') }}</button>
    </form>

    <div class="mt-5 grid gap-3">
        @forelse ($products as $product)
            @php($stock = $product->sizes->sum('stock'))
            <div @class(['card flex items-center gap-4 p-3', 'opacity-60' => ! $product->is_visible])>
                <div class="size-16 shrink-0 overflow-hidden rounded-xl {{ $product->tintClass() }}"><x-product-visual :product="$product" /></div>
                <div class="min-w-0 flex-1">
                    <a href="{{ route('admin.products.edit', $product) }}" class="font-semibold hover:underline">{{ $product->fullName() }}</a>
                    <p class="text-sm text-gris">
                        {{ $product->category->name }} · {{ Money::fcfa($product->price) }}
                        @if ($product->isOnSale())<span class="badge bg-rouge text-white">promo</span>@endif
                        @if ($product->is_featured)<span class="badge bg-orange-pale text-orange-fonce">{{ __('à la une') }}</span>@endif
                        @unless ($product->is_visible)<span class="badge bg-sable text-gris">{{ __('masqué') }}</span>@endunless
                    </p>
                    <p class="mt-1 flex flex-wrap gap-1 text-xs">
                        @foreach ($product->sizes as $size)
                            <span @class(['rounded px-1.5 py-0.5', 'bg-sable' => $size->stock > 1, 'bg-orange-pale text-orange-fonce' => $size->stock === 1, 'bg-rouge-pale text-rouge line-through' => $size->stock === 0])>{{ $size->size }}:{{ $size->stock }}</span>
                        @endforeach
                        <span class="px-1 font-semibold">= {{ trans_choice(':count paire|:count paires', $stock) }}</span>
                    </p>
                </div>
                <div class="hidden shrink-0 gap-2 sm:flex">
                    <form method="post" action="{{ route('admin.products.toggle', $product) }}">@csrf @method('patch')<button class="a-btn-ghost">{{ $product->is_visible ? __('Masquer') : __('Afficher') }}</button></form>
                    <a href="{{ route('admin.products.edit', $product) }}" class="a-btn-primary">{{ __('Modifier') }}</a>
                </div>
            </div>
        @empty
            <p class="card p-8 text-center text-gris">{{ __('Aucun produit.') }}</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $products->links() }}</div>
</x-layouts.admin>
