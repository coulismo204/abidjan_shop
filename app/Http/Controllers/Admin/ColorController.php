<?php

namespace App\Http\Controllers\Admin;

use App\Models\Color;

class ColorController extends TaxonomyController
{
    protected function model(): string
    {
        return Color::class;
    }

    protected function routeName(): string
    {
        return 'admin.colors';
    }

    protected function hasVisibility(): bool
    {
        return false;
    }

    protected function labels(): array
    {
        return [
            'plural' => __('Couleurs'),
            'singular' => __('couleur'),
            'new' => __('Nouvelle couleur'),
            'created' => __('Couleur créée.'),
            'saved' => __('Couleur enregistrée.'),
            'deleted' => __('Couleur supprimée.'),
            'used' => '',
        ];
    }

    protected function extraRules(): array
    {
        return ['hex' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/']];
    }
}
