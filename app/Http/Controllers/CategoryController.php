<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Ressourcerie;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['ressourcerie', 'categories'])
            ->when($request->categories, function ($query, $categories) {
                return $query->whereHas('categories', function ($q) use ($categories) {
                    $q->whereIn('categories.id', $categories);
                });
            })
            ->when($request->min_price, function ($query, $min_price) {
                return $query->where('price', '>=', $min_price);
            })
            ->when($request->max_price, function ($query, $max_price) {
                return $query->where('price', '<=', $max_price);
            })
            ->when($request->city, function ($query, $city) {
                return $query->whereHas('ressourcerie', function ($q) use ($city) {
                    $q->where('city', $city);
                });
            });

        // Tri
        $query = match ($request->sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'newest' => $query->latest(),
            default => $query->orderBy('created_at', 'desc')
        };

        return view('categorie', [
            'products' => $query->paginate(12),
            'categories' => Category::withCount('products')->get(),
            'ressourceries' => Ressourcerie::all(),
        ]);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $products = $category->products()
            ->with(['categories', 'ressourcerie', ])
            ->paginate(12);
        
        return view('categorie', [
            'products' => $products,
            'categories' => Category::withCount('products')->get(),
            'ressourceries' => Ressourcerie::all(),
        ]);
    }
} 