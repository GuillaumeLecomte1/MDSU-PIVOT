<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Ressourcerie;

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
        'category_id',
        'ressourcerie_id'
    ];

    protected $casts = [
        'price' => 'float',
        'images' => 'array',
        'stock' => 'integer',
        'is_available' => 'boolean'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'market__category_product', 'product_id', 'category_id');
    }
    public function ressourcerie()
    {
        return $this->belongsTo(Ressourcerie::class, 'ressourcerie_id');
    }
}
