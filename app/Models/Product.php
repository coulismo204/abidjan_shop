<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    /** Pointures proposées dans l'administration (européennes, homme). */
    public const SIZES = [38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48];

    /** Nombre de jours pendant lesquels un produit porte le badge « Nouveau ». */
    public const NEW_DAYS = 14;

    protected $fillable = [
        'category_id', 'brand_id', 'color_id', 'name', 'slug', 'description',
        'price', 'compare_at_price', 'is_featured', 'is_visible', 'position',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'compare_at_price' => 'integer',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class)->orderBy('size');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class)->orderBy('position')->orderBy('id');
    }

    public function coverPhoto(): HasOne
    {
        return $this->hasOne(ProductPhoto::class)->ofMany(['position' => 'min', 'id' => 'min']);
    }

    /** Visible, dans une catégorie visible, et sans marque masquée. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('products.is_visible', true)
            ->whereHas('category', fn (Builder $q) => $q->where('is_visible', true))
            ->where(fn (Builder $q) => $q->whereNull('brand_id')
                ->orWhereHas('brand', fn (Builder $b) => $b->where('is_visible', true)));
    }

    /** Au moins une pointure en stock. */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->whereHas('sizes', fn (Builder $q) => $q->where('stock', '>', 0));
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderByDesc('created_at');
    }

    public function isPublished(): bool
    {
        return $this->is_visible
            && $this->category?->is_visible
            && ($this->brand_id === null || $this->brand?->is_visible);
    }

    public function isOnSale(): bool
    {
        return $this->compare_at_price !== null && $this->compare_at_price > $this->price;
    }

    public function discountPercent(): ?int
    {
        return $this->isOnSale() ? (int) round(100 - $this->price * 100 / $this->compare_at_price) : null;
    }

    public function isNew(): bool
    {
        return $this->created_at !== null && $this->created_at->gt(now()->subDays(self::NEW_DAYS));
    }

    public function isSoldOut(): bool
    {
        return $this->sizes->every(fn (ProductSize $s) => $s->stock < 1);
    }

    /** Nom complet pour les messages et le SEO : « Nike Air Force 1 – Blanc ». */
    public function fullName(): string
    {
        return trim(($this->brand ? $this->brand->name.' ' : '').$this->name)
            .($this->color ? ' – '.$this->color->name : '');
    }
}
