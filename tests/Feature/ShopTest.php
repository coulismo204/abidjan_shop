<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(CatalogSeeder::class);
    }

    public function test_public_pages_render(): void
    {
        $product = Product::first();
        foreach (['/', '/chaussures', '/chaussures/'.$product->category->slug, '/chaussure/'.$product->slug, '/panier', '/livraison-et-paiement', '/contact', '/sitemap.xml', '/robots.txt'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_hidden_product_category_or_brand_is_404(): void
    {
        $product = Product::first();
        $product->update(['is_visible' => false]);
        $this->get('/chaussure/'.$product->slug)->assertNotFound();

        $other = Product::where('category_id', '!=', $product->category_id)->first();
        $other->category->update(['is_visible' => false]);
        $this->get('/chaussure/'.$other->slug)->assertNotFound();
        $this->get('/chaussures/'.$other->category->slug)->assertNotFound();

        $branded = Product::published()->whereNotNull('brand_id')->first();
        $branded->brand->update(['is_visible' => false]);
        $this->get('/chaussure/'.$branded->slug)->assertNotFound();
    }

    public function test_filters_by_size_brand_color_and_price(): void
    {
        $category = Category::first();
        $nike = Brand::create(['name' => 'Marque A', 'slug' => 'marque-a']);
        $red = Color::create(['name' => 'Rouge test', 'slug' => 'rouge-test', 'hex' => '#aa0000']);

        $match = $this->product($category, ['name' => 'Modèle trouvé', 'brand_id' => $nike->id, 'color_id' => $red->id, 'price' => 30000], [42 => 2, 43 => 0]);
        $wrongSize = $this->product($category, ['name' => 'Pointure épuisée', 'brand_id' => $nike->id, 'color_id' => $red->id, 'price' => 30000], [42 => 0, 44 => 3]);
        $wrongPrice = $this->product($category, ['name' => 'Trop cher', 'brand_id' => $nike->id, 'color_id' => $red->id, 'price' => 90000], [42 => 1]);

        $response = $this->get('/chaussures?pointure[]=42&marque[]=marque-a&couleur[]=rouge-test&prix=20000-40000');

        $response->assertOk()->assertSee($match->name)->assertDontSee($wrongSize->name)->assertDontSee($wrongPrice->name);
    }

    public function test_sold_out_products_are_not_listed(): void
    {
        $product = $this->product(Category::first(), ['name' => 'Tout épuisé'], [41 => 0, 42 => 0]);

        $this->get('/chaussures')->assertDontSee($product->name);
        $this->get('/chaussure/'.$product->slug)->assertOk()->assertSee('épuisé');
    }

    public function test_search_matches_name_and_brand(): void
    {
        $this->get('/chaussures?q=force')->assertSee('Air Force 1')->assertDontSee('Desert Boot');
        $this->get('/chaussures?q=clarks')->assertSee('Desert Boot')->assertDontSee('Air Force 1');
    }

    public function test_product_view_counted_once_per_session(): void
    {
        $product = Product::first();
        $ua = ['User-Agent' => 'Mozilla/5.0 (Linux; Android 14) Mobile Safari'];
        $this->withHeaders($ua)->get('/chaussure/'.$product->slug);
        $this->withHeaders($ua)->get('/chaussure/'.$product->slug);
        $this->withHeaders(['User-Agent' => 'Googlebot/2.1'])->get('/chaussure/'.$product->slug);

        $this->assertSame($product->views_count + 1, $product->fresh()->views_count);
    }

    /** @param array<int, int> $stock */
    private function product(Category $category, array $attributes, array $stock): Product
    {
        $product = Product::create([
            'category_id' => $category->id,
            'slug' => Str::slug($attributes['name']).'-'.uniqid(),
            'price' => 25000,
            ...$attributes,
        ]);
        foreach ($stock as $size => $qty) {
            $product->sizes()->create(['size' => $size, 'stock' => $qty]);
        }

        return $product;
    }
}
