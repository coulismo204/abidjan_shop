<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $with = ['brand', 'color', 'category', 'coverPhoto', 'sizes'];

        $featured = Product::published()->inStock()->where('is_featured', true)
            ->with($with)->ordered()->take(8)->get();

        // Rangées complètes sur ordinateur (4 colonnes) : pas de trou en fin de grille.
        if ($featured->count() > 4) {
            $featured = $featured->take(intdiv($featured->count(), 4) * 4);
        }

        $latest = Product::published()->inStock()->with($with)
            ->latest()->take(8)->get();

        $categories = Category::visible()->ordered()
            ->withCount(['products' => fn ($q) => $q->published()->inStock()])
            ->get()
            ->where('products_count', '>', 0);

        return view('public.home', compact('featured', 'latest', 'categories'));
    }
}
