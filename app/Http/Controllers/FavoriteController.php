<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Auth::user()
            ->favorites()
            ->with(['categories', 'ressourcerie'])
            ->get()
            ->map(function ($product) {
                $product->is_favorite = true;
                return $product;
            });

        return Inertia::render('Favorites/Index', [
            'favorites' => $favorites
        ]);
    }

    public function toggle(Request $request, Product $product)
    {
        $user = Auth::user();
        $isFavorite = $user->favorites()->toggle($product->id);

        return response()->json([
            'success' => true,
            'isFavorite' => count($isFavorite['attached']) > 0
        ]);
    }
} 