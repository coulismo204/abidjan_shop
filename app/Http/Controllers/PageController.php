<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function how(): View
    {
        return view('public.how');
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function whatsappContact(): RedirectResponse
    {
        return $this->away(WhatsApp::url(WhatsApp::contactMessage()));
    }

    public function whatsappProduct(Product $product): RedirectResponse
    {
        $product->load('brand', 'color', 'category');
        abort_unless($product->isPublished(), 404);

        return $this->away(WhatsApp::url(WhatsApp::productQuestion($product)));
    }

    private function away(string $url): RedirectResponse
    {
        return redirect()->away($url)->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
