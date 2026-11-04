<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'lastmod' => null],
            ['loc' => route('shop'), 'lastmod' => null],
            ['loc' => route('how'), 'lastmod' => null],
            ['loc' => route('contact'), 'lastmod' => null],
        ])
            ->merge(Category::visible()->ordered()->get()->map(fn ($c) => ['loc' => route('shop.category', $c), 'lastmod' => $c->updated_at]))
            ->merge(Product::published()->get()->map(fn ($p) => ['loc' => route('product', $p), 'lastmod' => $p->updated_at]));

        return response()->view('seo.sitemap', compact('urls'))->header('Content-Type', 'application/xml');
    }

    /** Hors production (préproduction, local) : on interdit l'indexation. */
    public function robots(): Response
    {
        $body = app()->isProduction()
            ? "User-agent: *\nDisallow: /admin\nDisallow: /panier\nDisallow: /commande\nDisallow: /wa/\n\nSitemap: ".route('sitemap')."\n"
            : "User-agent: *\nDisallow: /\n";

        return response($body)->header('Content-Type', 'text/plain');
    }
}
