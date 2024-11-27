<?php

namespace App\Http\Controllers;

use App\Models\Product; // Assurez-vous d'avoir un modèle Product

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $product = Product::with(['categories', 'ressourcerie'])->find($product->id);
        return view('products.show', compact('product'));
    }
}
