<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Ressourcerie;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['categories', 'ressourcerie'])
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
            })
            ->when($request->sort, function ($query, $sort) {
                return match($sort) {
                    'price_asc' => $query->orderBy('price', 'asc'),
                    'price_desc' => $query->orderBy('price', 'desc'), 
                    'newest' => $query->orderBy('created_at', 'desc'),
                    default => $query->orderBy('created_at', 'desc')
                };
            });

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->get();
        $ressourceries = Ressourcerie::all();

        return Inertia::render('Categories/Index', [
            'products' => $products,
            'categories' => $categories,
            'ressourceries' => $ressourceries,
            'filters' => $request->all()
        ]);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $products = Product::with(['categories', 'ressourcerie'])
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return Inertia::render('Categories/Show', [
            'products' => $products,
            'categories' => Category::withCount('products')->get(),
            'ressourceries' => Ressourcerie::all(),
            'category' => $category
        ]);
    }
} 
