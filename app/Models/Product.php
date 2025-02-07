<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'images' => 'array',
        'stock' => 'integer',
        'is_available' => 'boolean',
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
        return $this->belongsToMany(User::class, 'favorites')
            ->withTimestamps();
    }

    public function getMainImageAttribute(): ?string
    {
        $images = $this->images;

        return ! empty($images) ? '/storage/products/'.$images[0] : null;
    }

    public function getIsFavoriteAttribute(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return $this->favorites()->where('user_id', auth()->id())->exists();
    }

    public function isFavoritedBy(User $user): bool
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
