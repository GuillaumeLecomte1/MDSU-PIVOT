<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory;
    protected $table = 'market__products';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'condition',
        'dimensions',
        'color',
        'brand',
        'stock',
        'is_available',
        'images',
        'ressourcerie_id',
        'category_id',
        'main_image',
        'path',
        'user_id',
    ];

    protected $casts = [
        'price' => 'float',
        'images' => 'array',
        'stock' => 'integer',
        'is_available' => 'boolean',
        'is_favorite' => 'boolean',
        'path' => 'string',
    ];

    protected $appends = [
        'main_image',
        'is_favorite'
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'market__category_product');
    }

    public function ressourcerie(): BelongsTo
    {
        return $this->belongsTo(Ressourcerie::class);
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites', 'product_id', 'user_id')
            ->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function getMainImageAttribute(): ?string
    {
        $firstImage = $this->images()->first();
        return $firstImage ? $firstImage->getAttribute('path') : null;
    }

    public function getIsFavoriteAttribute(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        return $this->favorites()->where('user_id', Auth::id())->exists();
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
