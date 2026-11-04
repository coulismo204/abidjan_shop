<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Support\Images;
use App\Support\Slug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with(['category', 'brand', 'color', 'coverPhoto', 'sizes'])
            ->when($request->query('categorie'), fn (Builder $q, $id) => $q->where('category_id', $id))
            ->when($request->query('marque'), fn (Builder $q, $id) => $q->where('brand_id', $id))
            ->when($request->query('q'), fn (Builder $q, $term) => $q->whereRaw('lower(name) like ?', ['%'.mb_strtolower($term).'%']))
            ->when($request->query('stock') === 'epuise', fn (Builder $q) => $q->whereDoesntHave('sizes', fn ($s) => $s->where('stock', '>', 0)))
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::ordered()->get(),
            'brands' => Brand::ordered()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $product = new Product(['is_visible' => true]);

        // « Dupliquer » : pré-remplit un nouveau coloris du même modèle.
        if ($source = Product::with('sizes')->find($request->query('copie'))) {
            $product->fill($source->only(['category_id', 'brand_id', 'name', 'description', 'price', 'compare_at_price']));
            $product->setRelation('sizes', $source->sizes->map(fn ($s) => $s->replicate()->fill(['stock' => 0])));
        }

        return view('admin.products.form', $this->formData($product));
    }

    public function store(Request $request): RedirectResponse
    {
        $product = new Product;
        $this->save($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('status', __('Produit créé.'));
    }

    public function edit(Product $product): View
    {
        $product->load(['photos', 'sizes']);

        return view('admin.products.form', $this->formData($product));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->save($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('status', __('Produit enregistré.'));
    }

    public function toggle(Product $product): RedirectResponse
    {
        $product->update(['is_visible' => ! $product->is_visible]);

        return back()->with('status', $product->is_visible ? __('Produit affiché sur le site.') : __('Produit masqué.'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        DB::transaction(function () use ($product) {
            $product->photos->each->delete();
            $product->delete();
        });

        return redirect()->route('admin.products.index')->with('status', __('Produit supprimé. Les anciennes commandes le gardent dans leur historique.'));
    }

    private function formData(Product $product): array
    {
        return [
            'product' => $product,
            'categories' => Category::ordered()->get(),
            'brands' => Brand::ordered()->get(),
            'colors' => Color::ordered()->get(),
            'stock' => $product->sizes->pluck('stock', 'size')->all(),
        ];
    }

    private function save(Request $request, Product $product): void
    {
        $data = $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'brand_id' => ['nullable', Rule::exists('brands', 'id')],
            'color_id' => ['nullable', Rule::exists('colors', 'id')],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'integer', 'min:100', 'max:10000000'],
            'compare_at_price' => ['nullable', 'integer', 'gt:price', 'max:10000000'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'stock' => ['nullable', 'array'],
            'stock.*' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'max:8192'],
            'delete_photos' => ['nullable', 'array'],
            'delete_photos.*' => ['integer'],
            'cover_photo' => ['nullable', 'integer'],
        ], [
            'compare_at_price.gt' => __('L\'ancien prix doit être plus élevé que le prix de vente.'),
        ]);

        DB::transaction(function () use ($request, $product, $data) {
            $product->fill([
                ...collect($data)->only(['category_id', 'brand_id', 'color_id', 'name', 'description', 'price', 'compare_at_price'])->all(),
                'position' => $data['position'] ?? 0,
                'is_featured' => $request->boolean('is_featured'),
                'is_visible' => $request->boolean('is_visible'),
            ]);
            if (! $product->exists || $product->isDirty(['name', 'brand_id', 'color_id'])) {
                $product->load('brand', 'color');
                $product->slug = Slug::unique(Product::class, $product->brand?->name.' '.$product->name.' '.$product->color?->name, $product->id);
            }
            $product->save();

            // Pointures : case vide = pointure non proposée ; 0 = proposée mais épuisée.
            foreach (Product::SIZES as $size) {
                $value = $data['stock'][$size] ?? null;
                if ($value === null || $value === '') {
                    $product->sizes()->where('size', $size)->delete();
                } else {
                    $product->sizes()->updateOrCreate(['size' => $size], ['stock' => (int) $value]);
                }
            }

            if (! empty($data['delete_photos'])) {
                $product->photos()->whereIn('id', $data['delete_photos'])->get()->each->delete();
            }
            $next = (int) $product->photos()->max('position') + 1;
            foreach ($request->file('photos', []) as $file) {
                $product->photos()->create(['path' => Images::store($file, 'products'), 'position' => $next++]);
            }
            if (! empty($data['cover_photo'])) {
                $ordered = $product->photos()->get()
                    ->sortBy(fn (ProductPhoto $p) => $p->id === (int) $data['cover_photo'] ? -1 : $p->position)
                    ->values();
                foreach ($ordered as $i => $photo) {
                    ProductPhoto::whereKey($photo->id)->update(['position' => $i]);
                }
            }
        });
    }
}
