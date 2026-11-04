<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductSize;
use App\Support\Bots;
use App\Support\PriceRange;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public const SORTS = [
        'nouveautes' => 'Nouveautés',
        'prix-croissant' => 'Prix croissant',
        'prix-decroissant' => 'Prix décroissant',
        'populaires' => 'Les plus consultés',
    ];

    /**
     * Catalogue filtrable. Tous les filtres passent par l'URL (formulaire GET) :
     * ils marchent sans JavaScript, se partagent sur WhatsApp et sont indexables.
     */
    public function index(Request $request, ?Category $category = null): View
    {
        abort_if($category && ! $category->is_visible, 404);

        $filters = [
            'q' => trim((string) $request->query('q')),
            'marque' => $this->list($request, 'marque'),
            'couleur' => $this->list($request, 'couleur'),
            'pointure' => array_map('intval', $this->list($request, 'pointure')),
            'prix' => PriceRange::find($request->query('prix')) ? $request->query('prix') : null,
            'promo' => $request->boolean('promo'),
        ];
        $sort = array_key_exists($request->query('tri'), self::SORTS) ? $request->query('tri') : 'nouveautes';

        // Base commune : produits publiés et disponibles, dans la catégorie courante.
        $base = Product::published()->inStock()
            ->when($category, fn (Builder $q) => $q->where('category_id', $category->id));

        $products = (clone $base)
            ->when($filters['q'] !== '', function (Builder $q) use ($filters) {
                $term = '%'.mb_strtolower($filters['q']).'%';
                $q->where(fn (Builder $w) => $w->whereRaw('lower(products.name) like ?', [$term])
                    ->orWhereHas('brand', fn (Builder $b) => $b->whereRaw('lower(name) like ?', [$term])));
            })
            ->when($filters['marque'], fn (Builder $q, array $slugs) => $q->whereHas('brand', fn (Builder $b) => $b->whereIn('slug', $slugs)))
            ->when($filters['couleur'], fn (Builder $q, array $slugs) => $q->whereHas('color', fn (Builder $c) => $c->whereIn('slug', $slugs)))
            ->when($filters['pointure'], fn (Builder $q, array $sizes) => $q->whereHas('sizes', fn (Builder $s) => $s->whereIn('size', $sizes)->where('stock', '>', 0)))
            ->when(PriceRange::find($filters['prix']), function (Builder $q, array $range) {
                $q->when($range['min'], fn ($w) => $w->where('price', '>=', $range['min']))
                    ->when($range['max'], fn ($w) => $w->where('price', '<', $range['max']));
            })
            ->when($filters['promo'], fn (Builder $q) => $q->whereColumn('compare_at_price', '>', 'price'))
            ->with(['brand', 'color', 'category', 'coverPhoto', 'sizes'])
            ->tap(fn (Builder $q) => match ($sort) {
                'prix-croissant' => $q->orderBy('price'),
                'prix-decroissant' => $q->orderByDesc('price'),
                'populaires' => $q->orderByDesc('views_count'),
                default => $q->orderByDesc('products.created_at'),
            })
            ->orderBy('products.id')
            ->paginate(24)
            ->withQueryString();

        // Options des filtres : seulement celles qui existent dans la catégorie (pas de filtre qui mène à zéro résultat).
        $productIds = (clone $base)->select('products.id');
        $facets = [
            'brands' => Brand::visible()->ordered()->whereHas('products', fn ($q) => $q->whereIn('products.id', $productIds))->get(),
            'colors' => Color::ordered()->whereHas('products', fn ($q) => $q->whereIn('products.id', $productIds))->get(),
            'sizes' => ProductSize::whereIn('product_id', $productIds)->where('stock', '>', 0)
                ->distinct()->orderBy('size')->pluck('size'),
        ];

        $activeCount = count($filters['marque']) + count($filters['couleur']) + count($filters['pointure'])
            + ($filters['prix'] ? 1 : 0) + ($filters['promo'] ? 1 : 0) + ($filters['q'] !== '' ? 1 : 0);

        return view('public.shop', [
            'category' => $category,
            'categories' => Category::visible()->ordered()->get(),
            'products' => $products,
            'filters' => $filters,
            'sort' => $sort,
            'facets' => $facets,
            'activeCount' => $activeCount,
        ]);
    }

    public function show(Request $request, Product $product): View
    {
        $product->load(['brand', 'color', 'category', 'photos', 'sizes']);
        abort_unless($product->isPublished(), 404);

        $this->recordView($request, $product);

        // Autres coloris du même modèle (même nom, même marque) puis produits de la même catégorie.
        $siblings = Product::published()->whereKeyNot($product->id)
            ->where('name', $product->name)->where('brand_id', $product->brand_id)
            ->with('color')->get();

        $related = Product::published()->inStock()->whereKeyNot($product->id)
            ->where('category_id', $product->category_id)
            ->whereNotIn('id', $siblings->pluck('id'))
            ->with(['brand', 'color', 'category', 'coverPhoto', 'sizes'])
            ->inRandomOrder()->take(4)->get();

        return view('public.product', compact('product', 'siblings', 'related'));
    }

    /** @return list<string> */
    private function list(Request $request, string $key): array
    {
        $value = $request->query($key, []);

        return array_values(array_filter(array_map('strval', is_array($value) ? $value : [$value]), 'strlen'));
    }

    /** Une vue par visiteur (session) et par produit, robots exclus. */
    private function recordView(Request $request, Product $product): void
    {
        if (Bots::is($request->userAgent())) {
            return;
        }
        $seen = $request->session()->get('viewed_products', []);
        if (in_array($product->id, $seen, true)) {
            return;
        }
        $request->session()->push('viewed_products', $product->id);
        Product::whereKey($product->id)->increment('views_count');
    }
}
