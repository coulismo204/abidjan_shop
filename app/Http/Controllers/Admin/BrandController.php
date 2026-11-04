<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;

class BrandController extends TaxonomyController
{
    protected function model(): string
    {
        return Brand::class;
    }

    protected function routeName(): string
    {
        return 'admin.brands';
    }

    protected function labels(): array
    {
        return [
            'plural' => __('Marques'),
            'singular' => __('marque'),
            'new' => __('Nouvelle marque'),
            'created' => __('Marque créée.'),
            'saved' => __('Marque enregistrée.'),
            'deleted' => __('Marque supprimée (ses produits restent en ligne, sans marque).'),
            'used' => '',
        ];
    }
}
