<?php

namespace App\Providers;

use App\Models\Category;
use App\Support\Cart;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(Cart::class);

        // PostgreSQL plante (erreur 500) sur un id non numérique : on renvoie un 404 avant.
        Route::pattern('id', '[0-9]+');
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
        Schema::defaultStringLength(191); // anciennes versions MySQL / MariaDB en mutualisé
        Paginator::defaultView('components.pagination');

        // Menu et compteur du panier, partagés par toutes les pages publiques.
        View::composer('components.layouts.public', function ($view) {
            $view->with([
                'navCategories' => Category::visible()->ordered()->get(['id', 'name', 'slug']),
                'cartCount' => app(Cart::class)->count(),
            ]);
        });
    }
}
