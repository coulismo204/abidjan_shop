<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

// ---- Boutique ----
Route::get('/', HomeController::class)->name('home');
Route::get('/chaussures', [ShopController::class, 'index'])->name('shop');
Route::get('/chaussures/{category}', [ShopController::class, 'index'])->name('shop.category');
Route::get('/chaussure/{product}', [ShopController::class, 'show'])->name('product');
Route::get('/livraison-et-paiement', [PageController::class, 'how'])->name('how');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// ---- Panier et commande (sans compte client) ----
Route::get('/panier', [CartController::class, 'index'])->name('cart');
Route::post('/panier', [CartController::class, 'add'])->name('cart.add')->middleware('throttle:60,1');
Route::patch('/panier/{sizeId}', [CartController::class, 'update'])->name('cart.update')->whereNumber('sizeId');
Route::delete('/panier/{sizeId}', [CartController::class, 'remove'])->name('cart.remove')->whereNumber('sizeId');

Route::get('/commande', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/commande', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:10,1');
Route::get('/commande/{order}/merci', [CheckoutController::class, 'done'])->name('order.done');

// ---- Liens WhatsApp (message pré-rempli puis redirection vers wa.me) ----
Route::prefix('wa')->name('wa.')->middleware('throttle:60,1')->group(function () {
    Route::get('/commande/{order}', [CheckoutController::class, 'whatsapp'])->name('order');
    Route::get('/question/{product}', [PageController::class, 'whatsappProduct'])->name('product');
    Route::get('/contact', [PageController::class, 'whatsappContact'])->name('contact');
});

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots']);

// ---- Administration ----
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/connexion', [Admin\AuthController::class, 'show'])->name('login');
        Route::post('/connexion', [Admin\AuthController::class, 'login'])->name('login.attempt');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/deconnexion', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::get('commandes', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('commandes/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('commandes/{order}', [Admin\OrderController::class, 'update'])->name('orders.update');

        Route::resource('produits', Admin\ProductController::class)->except('show')
            ->names('products')->parameters(['produits' => 'product']);
        Route::patch('produits/{product}/visibilite', [Admin\ProductController::class, 'toggle'])->name('products.toggle');

        Route::resource('categories', Admin\CategoryController::class)->except('show')->parameters(['categories' => 'id']);
        Route::resource('marques', Admin\BrandController::class)->except('show')->names('brands')->parameters(['marques' => 'id']);
        Route::resource('couleurs', Admin\ColorController::class)->except('show')->names('colors')->parameters(['couleurs' => 'id']);

        Route::get('/reglages', [Admin\SettingsController::class, 'edit'])->name('settings');
        Route::put('/reglages', [Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::get('/compte', [Admin\SettingsController::class, 'editPassword'])->name('account');
        Route::put('/compte', [Admin\SettingsController::class, 'updatePassword'])->name('account.update');
    });
});
