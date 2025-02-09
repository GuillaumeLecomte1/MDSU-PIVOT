<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Ressourcerie;

class RessourcerieController extends Controller
{
    public function index()
    {
        return Inertia::render('Ressourceries/Index', [
            'ressourceries' => Ressourcerie::withCount('products')->get(),
        ]);
    }

    public function show(Ressourcerie $ressourcerie)
    {
        $ressourcerie->load('products.categories');

        return Inertia::render('Ressourceries/Show', [
            'ressourcerie' => $ressourcerie,
            'products' => $ressourcerie->products()
                ->with(['categories', 'ressourcerie'])
                ->paginate(12),
        ]);
    }
}
