<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(): Response
    {
        /** @var User $user */
        $user = Auth::user();
        
        $favorites = $user->favorites()
            ->with(['categories', 'ressourcerie', 'images'])
            ->get();

        return Inertia::render('Favorites/Index', [
            'favorites' => ProductResource::collection($favorites)->resolve(),
        ]);
    }

    public function toggle(Product $product)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if ($user->favorites()->where('product_id', $product->id)->exists()) {
                $user->favorites()->detach($product->id);
                $isFavorite = false;
            } else {
                $user->favorites()->attach($product->id);
                $isFavorite = true;
            }

            return redirect()->back()->with([
                'success' => true,
                'isFavorite' => $isFavorite
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling favorite:', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la modification des favoris.');
        }
    }
}
