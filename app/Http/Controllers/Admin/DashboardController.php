<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $since = now()->subDays(29)->startOfDay();
        $valid = fn ($q) => $q->where('status', '!=', OrderStatus::Cancelled);

        $recent = Order::where('created_at', '>=', $since)->tap($valid);

        $topProducts = OrderItem::query()
            ->whereHas('order', fn ($q) => $q->where('created_at', '>=', $since)->tap($valid))
            ->select('product_name', 'brand_name', 'color_name', DB::raw('sum(quantity) as sold'), DB::raw('sum(quantity * unit_price) as revenue'))
            ->groupBy('product_name', 'brand_name', 'color_name')
            ->orderByDesc('sold')
            ->take(5)
            ->get();

        // Pointures presque épuisées des produits en ligne : à réassortir.
        $lowStock = ProductSize::with('product.brand', 'product.color')
            ->where('stock', '<=', 1)
            ->whereHas('product', fn ($q) => $q->where('is_visible', true))
            ->orderBy('stock')->orderBy('product_id')->orderBy('size')
            ->take(12)
            ->get();

        return view('admin.dashboard', [
            'pending' => Order::where('status', OrderStatus::New)->latest()->take(8)->get(),
            'pendingCount' => Order::where('status', OrderStatus::New)->count(),
            'ordersCount' => (clone $recent)->count(),
            'revenue' => (int) (clone $recent)->sum('subtotal'),
            'delivered' => (int) Order::where('created_at', '>=', $since)->where('status', OrderStatus::Delivered)->sum('subtotal'),
            'topProducts' => $topProducts,
            'lowStock' => $lowStock,
            'mostViewed' => Product::with('brand', 'color')->where('views_count', '>', 0)->orderByDesc('views_count')->take(5)->get(),
            'counts' => [
                'products' => Product::count(),
                'online' => Product::published()->inStock()->count(),
            ],
        ]);
    }
}
