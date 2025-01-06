<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Index', [
            'categories' => Category::withCount('products')
                ->orderBy('name')
                ->get(),
            'latestProducts' => Product::with(['categories', 'ressourcerie'])
                ->latest()
                ->take(4)
                ->get(),
            'popularProducts' => Product::with(['categories', 'ressourcerie'])
                ->withCount('favorites')
                ->orderByDesc('favorites_count')
                ->take(4)
                ->get()
        ]);
    }
} 