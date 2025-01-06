<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::with(['categories', 'ressourcerie']);

        if (request('category')) {
            $query->whereHas('categories', function ($q) {
                $q->where('slug', request('category'));
            });
        }

        if (request('price_min')) {
            $query->where('price', '>=', request('price_min'));
        }

        if (request('price_max')) {
            $query->where('price', '<=', request('price_max'));
        }

        if (request('city')) {
            $query->whereHas('ressourcerie', function ($q) {
                $q->where('city', request('city'));
            });
        }

        $products = $query->paginate(12);

        foreach ($products as $product) {
            $images = json_decode($product->images) ?? [];
            $product->images = $images;
            $product->main_image = !empty($images) ? '/storage/products/' . $images[0] : null;
            $product->isFavorite = Auth::check() ? $product->favorites()->where('user_id', Auth::id())->exists() : false;
        }

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => request()->all(['category', 'price_min', 'price_max', 'city']),
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['categories', 'ressourcerie']);
        
        $images = json_decode($product->images) ?? [];
        $product->images = array_map(function($image) {
            return '/storage/products/' . $image;
        }, $images);
        $product->main_image = !empty($images) ? '/storage/products/' . $images[0] : null;
        $product->isFavorite = Auth::check() ? $product->favorites()->where('user_id', Auth::id())->exists() : false;

        $similarProducts = Product::with(['categories', 'ressourcerie'])
            ->where('ressourcerie_id', $product->ressourcerie_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        foreach ($similarProducts as $similarProduct) {
            $images = json_decode($similarProduct->images) ?? [];
            $similarProduct->images = $images;
            $similarProduct->main_image = !empty($images) ? '/storage/products/' . $images[0] : null;
        }

        return Inertia::render('Products/Show', [
            'product' => $product,
            'similarProducts' => $similarProducts,
        ]);
    }
}
