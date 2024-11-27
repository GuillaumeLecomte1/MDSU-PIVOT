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
        return $this->belongsTo(Category::class);
    }
    public function ressourcerie()
    {
        return $this->belongsTo(Ressourcerie::class);
    }
}
