<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\ProductSize;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private ProductSize $size;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Setting::put(['whatsapp_number' => '2250701020304', 'delivery_fee' => '1500']);

        $category = Category::create(['name' => 'Baskets', 'slug' => 'baskets']);
        $product = $category->products()->create(['name' => 'Runner', 'slug' => 'runner', 'price' => 30000]);
        $this->size = $product->sizes()->create(['size' => 42, 'stock' => 2]);
    }

    private function customer(array $overrides = []): array
    {
        return [
            'customer_name' => 'Koffi Yao',
            'phone' => '+225 07 08 09 10 11',
            'commune' => 'Cocody',
            'address' => 'Riviera 2, près de la pharmacie',
            'payment_method' => 'cash',
            ...$overrides,
        ];
    }

    public function test_buy_now_adds_to_cart_and_goes_to_checkout(): void
    {
        $this->post('/panier', ['size_id' => $this->size->id, 'action' => 'buy'])->assertRedirect('/commande');
        $this->get('/commande')->assertOk()->assertSee('Runner');
    }

    public function test_sold_out_size_cannot_be_added(): void
    {
        $this->size->update(['stock' => 0]);
        $this->post('/panier', ['size_id' => $this->size->id])->assertSessionHasErrors('size_id');
        $this->assertSame([], session('cart', []));
    }

    public function test_cart_quantity_is_capped_by_stock(): void
    {
        $this->post('/panier', ['size_id' => $this->size->id, 'quantity' => 5]);
        $this->assertSame([$this->size->id => 2], session('cart'));
    }

    public function test_empty_cart_redirects_away_from_checkout(): void
    {
        $this->get('/commande')->assertRedirect('/chaussures');
    }

    public function test_checkout_creates_order_reserves_stock_and_opens_whatsapp(): void
    {
        $this->post('/panier', ['size_id' => $this->size->id]);

        $response = $this->post('/commande', $this->customer());

        $order = Order::with('items')->sole();
        $response->assertRedirect(route('order.done', $order));
        $this->assertSame('0708091011', $order->phone);
        $this->assertSame(OrderStatus::New, $order->status);
        $this->assertSame(30000, $order->subtotal);
        $this->assertSame(1500, $order->delivery_fee);
        $this->assertSame(31500, $order->total);
        $this->assertSame('Runner', $order->items->first()->product_name);
        $this->assertSame(1, $this->size->fresh()->stock);
        $this->assertSame([], session('cart', []));

        $this->get(route('order.done', $order))->assertOk()->assertSee($order->reference);

        $location = $this->get(route('wa.order', $order))->headers->get('Location');
        $this->assertStringStartsWith('https://wa.me/2250701020304?text=', $location);
        $message = rawurldecode(substr($location, strpos($location, '=') + 1));
        $this->assertStringContainsString($order->reference, $message);
        $this->assertStringContainsString('1 × Runner – pointure 42', $message);
        $this->assertStringContainsString('Cocody, Riviera 2', $message);
        $this->assertNotNull($order->fresh()->whatsapp_opened_at);
    }

    public function test_delivery_fee_is_left_open_outside_abidjan(): void
    {
        $this->post('/panier', ['size_id' => $this->size->id]);
        $this->post('/commande', $this->customer(['commune' => 'Hors Abidjan', 'address' => 'Bouaké, quartier Commerce']));

        $order = Order::sole();
        $this->assertNull($order->delivery_fee);
        $this->assertSame(30000, $order->total);
    }

    public function test_invalid_phone_is_rejected(): void
    {
        $this->post('/panier', ['size_id' => $this->size->id]);
        $this->post('/commande', $this->customer(['phone' => '123']))->assertSessionHasErrors('phone');
        $this->assertSame(0, Order::count());
    }

    public function test_order_fails_when_stock_ran_out_meanwhile(): void
    {
        $this->post('/panier', ['size_id' => $this->size->id, 'quantity' => 2]);
        $this->size->update(['stock' => 1]); // un autre client a acheté une paire entre-temps

        $this->post('/commande', $this->customer())->assertRedirect('/panier')->assertSessionHas('error');
        $this->assertSame(0, Order::count());
        $this->assertSame(1, $this->size->fresh()->stock);
    }

    public function test_honeypot_blocks_bots(): void
    {
        $this->post('/panier', ['size_id' => $this->size->id]);
        $this->post('/commande', $this->customer(['website' => 'http://spam.example']))->assertRedirect('/');
        $this->assertSame(0, Order::count());
    }

    public function test_confirmation_page_is_private_to_the_buyer(): void
    {
        $this->post('/panier', ['size_id' => $this->size->id]);
        $this->post('/commande', $this->customer());
        $order = Order::sole();

        $this->flushSession();
        $this->get(route('order.done', $order))->assertNotFound();
        $this->get(route('wa.order', $order))->assertNotFound();
    }
}
