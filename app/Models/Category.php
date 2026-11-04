<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** Dessins disponibles pour les produits sans photo. */
    public const SILHOUETTES = [
        'basket' => 'Basket',
        'ville' => 'Chaussure de ville',
        'mocassin' => 'Mocassin',
        'sandale' => 'Sandale / mule',
        'bottine' => 'Bottine',
    ];

    protected $fillable = ['name', 'slug', 'description', 'silhouette', 'position', 'is_visible'];

    protected function casts(): array
    {
        return ['is_visible' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('name');
    }
}
