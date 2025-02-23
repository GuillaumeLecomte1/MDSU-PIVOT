<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get()
            ->map(function ($product) use ($cart) {
                $product->quantity = $cart[$product->id];
                return $product;
            });

        return Inertia::render('Cart/Index', [
            'products' => $products,
        ]);
    }

    public function add(Product $product)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$product->id])) {
            $cart[$product->id]++;
        } else {
            $cart[$product->id] = 1;
        }

        session()->put('cart', $cart);

        return back()->with([
            'success' => 'Produit ajouté au panier',
            'cartCount' => array_sum($cart),
        ]);
    }

    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        return back()->with([
            'success' => 'Produit retiré du panier',
            'cartCount' => array_sum($cart),
        ]);
    }

    public function updateQuantity(Product $product, Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        
        if (isset($cart[$product->id])) {
            $cart[$product->id] = $request->quantity;
            session()->put('cart', $cart);
        }

        return back()->with([
            'success' => 'Quantité mise à jour',
            'cartCount' => array_sum($cart),
        ]);
    }
} 