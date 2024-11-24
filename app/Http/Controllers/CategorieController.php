<?php

namespace App\Http\Controllers;

use App\Models\Product; // Assurez-vous d'avoir un modèle Product

class CategorieController extends Controller
{
    public function showCategorie()
    {
        $products = Product::with(['category', 'ressourcerie'])->get();
        // dd($products);
        return view('categorie', compact('products'));
    }
}
