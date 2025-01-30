<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Ressourcerie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['categories', 'ressourcerie'])
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
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
            ->when($request->quantity, function ($query, $quantity) {
                if ($quantity === 'available') {
                    return $query->where('quantity', '>', 0);
                } elseif ($quantity === 'out_of_stock') {
                    return $query->where('quantity', '=', 0);
                }

                return $query; // 'all' case, no filtering
            })
            ->when($request->sort, function ($query, $sort) {
                return match ($sort) {
                    'price_asc' => $query->orderBy('price', 'asc'),
                    'price_desc' => $query->orderBy('price', 'desc'),
                    'recent' => $query->orderBy('created_at', 'desc'),
                    default => $query->orderBy('created_at', 'desc')
                };
            });

        $products = $query->paginate(12)->withQueryString();

        // Ajouter l'état des favoris pour chaque produit
        foreach ($products as $product) {
            $images = json_decode($product->images) ?? [];
            $product->images = $images;
            $product->main_image = ! empty($images) ? '/storage/products/'.$images[0] : null;
            $product->isFavorite = Auth::check() ? $product->isFavoritedBy(Auth::user()) : false;
        }

        return Inertia::render('Categories/Index', [
            'categories' => Category::all(),
            'products' => $products,
            'ressourceries' => Ressourcerie::select('id', 'name', 'city')->get(),
            'filters' => $request->only(['search', 'categories', 'min_price', 'max_price', 'city', 'quantity', 'sort']),
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

        // Ajouter l'état des favoris pour chaque produit
        foreach ($products as $product) {
            $images = json_decode($product->images) ?? [];
            $product->images = $images;
            $product->main_image = ! empty($images) ? '/storage/products/'.$images[0] : null;
            $product->isFavorite = Auth::check() ? $product->isFavoritedBy(Auth::user()) : false;
        }

        return Inertia::render('Categories/Show', [
            'products' => $products,
            'categories' => Category::withCount('products')->get(),
            'ressourceries' => Ressourcerie::all(),
            'category' => $category,
        ]);
    }
}
