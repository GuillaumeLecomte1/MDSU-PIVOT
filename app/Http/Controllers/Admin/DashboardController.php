<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'title' => 'Dashboard Admin',
            'stats' => [
                'users' => User::count(),
                'products' => Product::count(),
                'orders' => 0, // Temporairement mis à 0 en attendant la création de la table orders
            ],
        ]);
    }
}
