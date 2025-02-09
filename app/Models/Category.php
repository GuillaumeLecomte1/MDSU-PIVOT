<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'market__categories';

    public function products()
    {
        return $this->belongsToMany(Product::class, 'market__category_product', 'category_id', 'product_id');
    }
}
