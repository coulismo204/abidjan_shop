@php use App\Models\Product; @endphp
<x-layouts.admin :title="$product->exists ? $product->name : __('Nouveau produit')">
    <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-gris hover:text-encre">← {{ __('Produits') }}</a>
    <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-4xl">{{ $product->exists ? $product->name : __('Nouveau produit') }}</h1>
        @if ($product->exists)
            <div class="flex gap-2">
                <a href="{{ route('product', $product) }}" target="_blank" class="a-btn-ghost">{{ __('Voir sur le site') }} ↗</a>
                <a href="{{ route('admin.products.create', ['copie' => $product->id]) }}" class="a-btn-ghost">{{ __('Ajouter un coloris') }}</a>
            </div>
        @endif
    </div>

    <form method="post" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="mt-5 grid gap-5 lg:grid-cols-[1.5fr_1fr]">
        @csrf
        @if ($product->exists) @method('put') @endif

        <div class="space-y-5">
            <section class="card space-y-4 p-5">
                <div>
                    <label for="name" class="field-label">{{ __('Nom du modèle') }}</label>
                    <input id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="150" class="field" placeholder="Air Force 1 '07">
                    <p class="field-help">{{ __('Sans la marque ni la couleur : elles s\'ajoutent automatiquement.') }}</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label for="category_id" class="field-label">{{ __('Catégorie') }}</label>
                        <select id="category_id" name="category_id" required class="field">
                            @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label for="brand_id" class="field-label">{{ __('Marque') }}</label>
                        <select id="brand_id" name="brand_id" class="field">
                            <option value="">—</option>
                            @foreach ($brands as $b)<option value="{{ $b->id }}" @selected(old('brand_id', $product->brand_id) == $b->id)>{{ $b->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label for="color_id" class="field-label">{{ __('Couleur') }}</label>
                        <select id="color_id" name="color_id" class="field">
                            <option value="">—</option>
                            @foreach ($colors as $c)<option value="{{ $c->id }}" @selected(old('color_id', $product->color_id) == $c->id)>{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label for="description" class="field-label">{{ __('Description') }}</label>
                    <textarea id="description" name="description" rows="5" maxlength="5000" class="field">{{ old('description', $product->description) }}</textarea>
                    <p class="field-help">{{ __('Matière, confort, conseils de pointure. Une ligne vide sépare les paragraphes.') }}</p>
                </div>
            </section>

            <section class="card p-5">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-2xl">{{ __('Pointures et stock') }}</h2>
                        <p class="text-sm text-gris">{{ __('Nombre de paires par pointure. Case vide = pointure non proposée ; 0 = affichée comme épuisée.') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="fill" class="sr-only">{{ __('Quantité pour toutes les pointures') }}</label>
                        <input id="fill" data-fill-value type="number" min="0" class="field w-20 py-2" placeholder="2">
                        <button type="button" data-fill-stock class="a-btn-ghost">{{ __('Remplir tout') }}</button>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-4 gap-2 sm:grid-cols-6">
                    @foreach (Product::SIZES as $size)
                        <div>
                            <label for="stock-{{ $size }}" class="mb-1 block text-center font-display text-xl [font-stretch:85%]">{{ $size }}</label>
                            <input id="stock-{{ $size }}" data-stock-input type="number" name="stock[{{ $size }}]" min="0" max="9999" value="{{ old('stock.'.$size, $stock[$size] ?? '') }}" class="field px-2 py-2 text-center">
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="card p-5">
                <h2 class="text-2xl">{{ __('Photos') }}</h2>
                <p class="text-sm text-gris">{{ __('Fond clair et uni de préférence, chaussure de profil. Redimensionnées et converties automatiquement (WebP). Sans photo, un dessin à la couleur du produit est affiché.') }}</p>
                @if ($product->photos?->isNotEmpty())
                    <div class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-4">
                        @foreach ($product->photos as $photo)
                            <div class="rounded-xl border border-ligne p-2">
                                <img src="{{ $photo->url() }}" alt="" class="aspect-square w-full rounded-lg object-cover">
                                <label class="mt-2 flex items-center gap-1.5 text-xs"><input type="radio" name="cover_photo" value="{{ $photo->id }}" @checked($loop->first) class="accent-encre">{{ __('Principale') }}</label>
                                <label class="mt-1 flex items-center gap-1.5 text-xs text-rouge"><input type="checkbox" name="delete_photos[]" value="{{ $photo->id }}" class="accent-rouge">{{ __('Supprimer') }}</label>
                            </div>
                        @endforeach
                    </div>
                @endif
                <label for="photos" class="field-label mt-4">{{ __('Ajouter des photos') }}</label>
                <input id="photos" type="file" name="photos[]" accept="image/*" multiple class="block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-encre file:px-4 file:py-2 file:font-semibold file:text-white">
            </section>
        </div>

        <div class="space-y-5">
            <section class="card space-y-4 p-5 lg:sticky lg:top-32">
                <div>
                    <label for="price" class="field-label">{{ __('Prix de vente (FCFA)') }}</label>
                    <input id="price" name="price" type="number" min="100" step="100" value="{{ old('price', $product->price) }}" required class="field text-lg font-bold">
                </div>
                <div>
                    <label for="compare_at_price" class="field-label">{{ __('Ancien prix (promo, facultatif)') }}</label>
                    <input id="compare_at_price" name="compare_at_price" type="number" min="100" step="100" value="{{ old('compare_at_price', $product->compare_at_price) }}" class="field">
                    <p class="field-help">{{ __('Affiché barré, avec le pourcentage de réduction.') }}</p>
                </div>
                <div>
                    <label for="position" class="field-label">{{ __('Ordre d\'affichage') }}</label>
                    <input id="position" name="position" type="number" min="0" value="{{ old('position', $product->position ?? 0) }}" class="field">
                    <p class="field-help">{{ __('0 par défaut. Plus petit = plus haut dans « À la une ».') }}</p>
                </div>
                <label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="is_visible" value="1" @checked(old('is_visible', $product->is_visible)) class="size-4 accent-encre">{{ __('En ligne sur le site') }}</label>
                <label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) class="size-4 accent-encre">{{ __('À la une (accueil)') }}</label>
                <button class="a-btn-primary w-full py-3">{{ __('Enregistrer') }}</button>
            </section>
        </div>
    </form>

    @if ($product->exists)
        <form method="post" action="{{ route('admin.products.destroy', $product) }}" class="mt-8" data-confirm="{{ __('Supprimer définitivement ce produit et ses photos ?') }}">
            @csrf @method('delete')
            <button class="a-btn-danger">{{ __('Supprimer ce produit') }}</button>
            <span class="ml-2 text-xs text-gris">{{ __('Pour le retirer temporairement, décochez plutôt « En ligne ».') }}</span>
        </form>
    @endif
</x-layouts.admin>
