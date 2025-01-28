<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get();

        $latestProducts = Product::with(['categories', 'ressourcerie'])
            ->latest()
            ->take(4)
            ->get();

        $popularProducts = Product::with(['categories', 'ressourcerie'])
            ->withCount('favorites')
            ->orderByDesc('favorites_count')
            ->take(4)
            ->get();

        Log::info('Dashboard data', [
            'categories_count' => $categories->count(),
            'latest_products_count' => $latestProducts->count(),
            'popular_products_count' => $popularProducts->count()
        ]);

        return Inertia::render('Dashboard/Index', [
            'categories' => $categories,
            'latestProducts' => $latestProducts,
            'popularProducts' => $popularProducts
        ]);
    }
} 