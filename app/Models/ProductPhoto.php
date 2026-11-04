<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductPhoto extends Model
{
    protected $fillable = ['product_id', 'path', 'position'];

    protected static function booted(): void
    {
        // Le fichier disparaît avec la ligne : pas de photos orphelines sur l'hébergement.
        static::deleted(fn (ProductPhoto $photo) => Storage::disk('public')->delete($photo->path));
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
