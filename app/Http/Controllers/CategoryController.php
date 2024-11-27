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

    public function show(string $slug)
    {
        // Récupérer la catégorie courante
        $category = Category::where('slug', $slug)->firstOrFail();
        
        // Récupérer toutes les catégories pour le menu de navigation
        $categories = Category::withCount('products')->get();
        
        // Récupérer les produits de la catégorie
        $products = $category->products()
            ->with(['ressourcerie', 'categories'])
            ->paginate(12);
        
        // Récupérer les ressourceries pour le filtre
        $ressourceries = Ressourcerie::all();

        return view('categorie', compact(
            'category',
            'categories',
            'products',
            'ressourceries'
        ));
    }
} 