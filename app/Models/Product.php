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
        'id',
        'name',
        'description',
        'price',
        'created_at',
        'updated_at'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'market__category_product', 'product_id', 'category_id');
    }
    public function ressourcerie()
    {
        return $this->belongsTo(Ressourcerie::class);
    }
}
