@php use App\Models\Category; @endphp
<x-layouts.admin :title="$item->exists ? $item->name : $labels['new']">
    <a href="{{ route($route.'.index') }}" class="text-sm font-semibold text-gris hover:text-encre">← {{ $labels['plural'] }}</a>
    <h1 class="mt-2 text-4xl">{{ $item->exists ? $item->name : $labels['new'] }}</h1>

    <form method="post" action="{{ $item->exists ? route($route.'.update', $item->id) : route($route.'.store') }}" class="card mt-5 max-w-xl space-y-4 p-5">
        @csrf
        @if ($item->exists) @method('put') @endif
        <div>
            <label for="name" class="field-label">{{ __('Nom') }}</label>
            <input id="name" name="name" value="{{ old('name', $item->name) }}" required maxlength="100" class="field">
        </div>

        @if ($kind === 'Category')
            <div>
                <label for="description" class="field-label">{{ __('Description courte') }}</label>
                <input id="description" name="description" value="{{ old('description', $item->description) }}" maxlength="255" class="field">
                <p class="field-help">{{ __('Affichée en haut de la page de la catégorie et utilisée par Google.') }}</p>
            </div>
            <fieldset>
                <legend class="field-label">{{ __('Dessin utilisé quand un produit n\'a pas de photo') }}</legend>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @foreach (Category::SILHOUETTES as $key => $label)
                        <label class="cursor-pointer rounded-xl border border-ligne p-3 text-center text-xs font-semibold has-checked:border-encre has-checked:ring-1 has-checked:ring-encre">
                            <input type="radio" name="silhouette" value="{{ $key }}" @checked(old('silhouette', $item->silhouette ?? 'basket') === $key) class="sr-only">
                            <x-shoe :silhouette="$key" class="mb-1" />{{ $label }}
                        </label>
                    @endforeach
                </div>
            </fieldset>
        @endif

        @if ($kind === 'Color')
            <div>
                <label for="hex" class="field-label">{{ __('Teinte') }}</label>
                <div class="flex gap-2">
                    <input type="color" data-hex-picker value="{{ old('hex', $item->hex ?? '#141414') }}" class="h-12 w-16 cursor-pointer rounded-xl border border-ligne bg-white p-1" aria-label="{{ __('Choisir la teinte') }}">
                    <input id="hex" name="hex" data-hex value="{{ old('hex', $item->hex ?? '#141414') }}" required pattern="#[0-9a-fA-F]{6}" class="field font-mono">
                </div>
                <p class="field-help">{{ __('Sert à la pastille du filtre et au dessin des produits sans photo.') }}</p>
            </div>
        @endif

        <div>
            <label for="position" class="field-label">{{ __('Ordre d\'affichage') }}</label>
            <input id="position" name="position" type="number" min="0" value="{{ old('position', $item->position ?? 0) }}" class="field w-32">
        </div>
        @if ($hasVisibility)
            <label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="is_visible" value="1" @checked(old('is_visible', $item->is_visible ?? true)) class="size-4 accent-encre">{{ __('Visible sur le site') }}</label>
        @endif
        <button class="a-btn-primary py-3">{{ __('Enregistrer') }}</button>
    </form>

    @if ($item->exists)
        <form method="post" action="{{ route($route.'.destroy', $item->id) }}" class="mt-6" data-confirm="{{ __('Supprimer définitivement ?') }}">
            @csrf @method('delete')
            <button class="a-btn-danger">{{ __('Supprimer cette :s', ['s' => $labels['singular']]) }}</button>
        </form>
    @endif
</x-layouts.admin>
