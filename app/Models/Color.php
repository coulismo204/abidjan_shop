<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    protected $fillable = ['name', 'slug', 'hex', 'position'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('name');
    }

    /** Vrai pour les teintes claires : on leur ajoute un contour pour qu'elles restent visibles sur fond clair. */
    public function isLight(): bool
    {
        $hex = ltrim($this->hex, '#');
        [$r, $g, $b] = array_map('hexdec', str_split(str_pad($hex, 6, '0'), 2));

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) > 200;
    }
}
