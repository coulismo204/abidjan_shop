<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(CatalogSeeder::class);
        $this->admin = User::factory()->create();
    }

    public function test_admin_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/connexion');
        $this->get('/admin/commandes')->assertRedirect('/admin/connexion');
    }

    public function test_admin_pages_render(): void
    {
        $product = Product::first();
        $this->actingAs($this->admin);
        foreach (['/admin', '/admin/commandes', '/admin/produits', '/admin/produits/create', '/admin/produits/'.$product->slug.'/edit', '/admin/produits/create?copie='.$product->id, '/admin/categories', '/admin/marques', '/admin/couleurs', '/admin/couleurs/create', '/admin/categories/'.$product->category_id.'/edit', '/admin/reglages', '/admin/compte'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_product_creation_saves_sizes_and_stock(): void
    {
        $category = Category::first();

        $this->actingAs($this->admin)->post('/admin/produits', [
            'category_id' => $category->id,
            'name' => 'Nouveau modèle',
            'price' => 40000,
            'is_visible' => '1',
            'stock' => [40 => '3', 41 => '0', 42 => ''],
        ])->assertRedirect();

        $product = Product::where('name', 'Nouveau modèle')->sole();
        $this->assertSame([40 => 3, 41 => 0], $product->sizes->pluck('stock', 'size')->all());
    }

    public function test_sale_price_must_be_higher_than_price(): void
    {
        $this->actingAs($this->admin)->post('/admin/produits', [
            'category_id' => Category::first()->id, 'name' => 'Promo ratée', 'price' => 40000, 'compare_at_price' => 30000,
        ])->assertSessionHasErrors('compare_at_price');
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $category = Category::has('products')->first();
        $this->actingAs($this->admin)->delete('/admin/categories/'.$category->id)->assertSessionHas('error');
        $this->assertModelExists($category);
    }

    public function test_cancelling_an_order_restores_stock(): void
    {
        $size = Product::first()->sizes()->where('stock', '>', 0)->first();
        $before = $size->stock;
        $order = Order::create([
            'reference' => 'AS-TEST-0001', 'customer_name' => 'Test', 'phone' => '0708091011', 'commune' => 'Plateau',
            'address' => 'Rue 12', 'payment_method' => 'cash', 'subtotal' => 1000, 'total' => 1000,
        ]);
        $order->items()->create(['product_size_id' => $size->id, 'product_name' => 'X', 'size' => $size->size, 'unit_price' => 1000, 'quantity' => 2]);

        $this->actingAs($this->admin)->patch('/admin/commandes/'.$order->reference, ['status' => 'cancelled']);

        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
        $this->assertSame($before + 2, $size->fresh()->stock);

        // Une commande annulée ne change plus de statut (sinon le stock serait compté deux fois).
        $this->actingAs($this->admin)->patch('/admin/commandes/'.$order->reference, ['status' => 'delivered'])->assertSessionHas('error');
        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
    }
}
