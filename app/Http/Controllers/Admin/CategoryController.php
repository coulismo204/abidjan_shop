<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Validation\Rule;

class CategoryController extends TaxonomyController
{
    protected function model(): string
    {
        return Category::class;
    }

    protected function routeName(): string
    {
        return 'admin.categories';
    }

    protected function labels(): array
    {
        return [
            'plural' => __('Catégories'),
            'singular' => __('catégorie'),
            'new' => __('Nouvelle catégorie'),
            'created' => __('Catégorie créée.'),
            'saved' => __('Catégorie enregistrée.'),
            'deleted' => __('Catégorie supprimée.'),
            'used' => __('Cette catégorie contient des produits : déplacez-les d\'abord, ou masquez simplement la catégorie.'),
        ];
    }

    protected function protectsUsedItems(): bool
    {
        return true;
    }

    protected function extraRules(): array
    {
        return [
            'description' => ['nullable', 'string', 'max:255'],
            'silhouette' => ['required', Rule::in(array_keys(Category::SILHOUETTES))],
        ];
    }
}
