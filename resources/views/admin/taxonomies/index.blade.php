<x-layouts.admin :title="$labels['plural']">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-4xl">{{ $labels['plural'] }}</h1>
        <a href="{{ route($route.'.create') }}" class="a-btn-primary py-2.5">+ {{ $labels['new'] }}</a>
    </div>
    <div class="card mt-5 divide-y divide-ligne">
        @forelse ($items as $item)
            <div @class(['flex items-center gap-4 p-4', 'opacity-60' => $hasVisibility && ! $item->is_visible])>
                @if ($kind === 'Color')<x-color-dot :color="$item" class="size-8" />@endif
                @if ($kind === 'Category')<x-shoe :silhouette="$item->silhouette" class="w-14" />@endif
                <div class="min-w-0 flex-1">
                    <a href="{{ route($route.'.edit', $item->id) }}" class="font-semibold hover:underline">{{ $item->name }}</a>
                    <p class="text-sm text-gris">
                        {{ trans_choice(':count produit|:count produits', $item->products_count) }}
                        @if ($hasVisibility && ! $item->is_visible) · <span class="badge bg-sable text-gris">{{ __('masqué') }}</span>@endif
                    </p>
                </div>
                <a href="{{ route($route.'.edit', $item->id) }}" class="a-btn-ghost">{{ __('Modifier') }}</a>
            </div>
        @empty
            <p class="p-8 text-center text-gris">{{ __('Rien pour le moment.') }}</p>
        @endforelse
    </div>
</x-layouts.admin>
