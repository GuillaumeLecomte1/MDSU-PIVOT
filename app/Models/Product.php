<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use App\Traits\Favoritable;

class Product extends Model
{
    use HasFactory, Favoritable;

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
        'ressourcerie_id',
        'category_id',
        'user_id',
    ];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'is_available' => 'boolean',
        'is_favorite' => 'boolean',
    ];

    protected $appends = [
        'is_favorite',
    ];

    protected $with = ['images'];

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
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function getIsFavoriteAttribute(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return $this->favorites()->where('user_id', Auth::id())->exists();
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
