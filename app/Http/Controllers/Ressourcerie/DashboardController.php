<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ressourcerie;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Ressourcerie;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'ressourcerie', 'verified']);
    }

    public function index(Request $request): Response
    {
        $ressourcerie = $request->user()->ressourcerie;
        $productsCount = $ressourcerie->products()->count();
        $activeProductsCount = $ressourcerie->products()->where('is_available', true)->count();
        $totalSales = 0; // À implémenter avec le système de commandes

        return Inertia::render('Ressourcerie/Dashboard', [
            'stats' => [
                'total_products' => $productsCount,
                'active_products' => $activeProductsCount,
                'total_sales' => $totalSales,
            ],
            'ressourcerie' => $ressourcerie,
        ]);
    }
} 