<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $favorites = $user->favorites()
            ->with(['categories', 'ressourcerie'])
            ->get()
            ->map(function ($product) {
                $imagesData = is_string($product->getAttribute('images'))
                    ? json_decode($product->getAttribute('images'))
                    : [];
                $images = is_array($imagesData) ? $imagesData : [];

                $product->setAttribute('images', $images);
                $product->setAttribute('main_image', ! empty($images) ? '/storage/products/'.$images[0] : null);
                $product->setAttribute('isFavorite', true);

                return $product;
            });

        return Inertia::render('Favorites/Index', [
            'favorites' => $favorites,
        ]);
    }

    public function toggle(Product $product)
    {
        /** @var User $user */
        $user = Auth::user();

        $isFavorite = ! $user->hasFavorited($product);

        if ($isFavorite) {
            $user->favorites()->attach($product->id);
        } else {
            $user->favorites()->detach($product->id);
        }

        return response()->json([
            'isFavorite' => $isFavorite,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'description' => $product->description,
                'main_image' => $product->getAttribute('main_image'),
                'isFavorite' => $isFavorite,
            ],
        ]);
    }
}
