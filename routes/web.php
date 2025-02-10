<?php

use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RessourcerieController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Ressourcerie\DashboardController as RessourcerieDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Routes publiques (login, register)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::get('register', function () {
        return Inertia::render('Auth/Register');
    })->name('register');
});

// Route racine avec redirection vers login si non connecté
Route::get('/', function () {
    $latestProducts = Product::with(['categories', 'ressourcerie', 'favorites'])
        ->latest()
        ->take(4)
        ->get();

    $popularProducts = Product::with(['categories', 'ressourcerie', 'favorites'])
        ->withCount('favorites')
        ->orderByDesc('favorites_count')
        ->take(4)
        ->get();

    // Ajouter l'état des favoris pour chaque produit
    $processProducts = function ($products) {
        foreach ($products as $product) {
            $images = json_decode($product->images) ?? [];
            $product->images = $images;
            $product->main_image = ! empty($images) ? '/storage/products/'.$images[0] : null;
            $product->isFavorite = Auth::check() ? $product->isFavoritedBy(Auth::user()) : false;
        }

        return $products;
    };

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'latestProducts' => $processProducts($latestProducts),
        'popularProducts' => $processProducts($popularProducts),
        'categories' => Category::withCount('products')->get(),
    ]);
})->middleware(['auth', 'verified'])->name('home');

// Toutes les autres routes nécessitent une authentification
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/Index');
    })->name('dashboard');

    // Categories routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

    // Products routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ressourceries routes
    Route::get('/ressourceries', [RessourcerieController::class, 'index'])->name('ressourceries.index');
    Route::get('/ressourceries/{ressourcerie}', [RessourcerieController::class, 'show'])->name('ressourceries.show');

    // Favorites routes
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/products/{product}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Cart routes
    Route::get('/cart', function () {
        return Inertia::render('Cart/Index');
    })->name('cart.index');
});

// Routes spécifiques aux rôles
Route::middleware(['auth', 'verified'])->group(function () {
    // Routes pour les ressourceries
    Route::prefix('ressourcerie')->middleware('can:access-ressourcerie')->group(function () {
        Route::get('/dashboard', [RessourcerieDashboardController::class, 'index'])->name('ressourcerie.dashboard');
        Route::resource('products', App\Http\Controllers\Ressourcerie\ProductController::class);
        Route::get('/profile', [App\Http\Controllers\Ressourcerie\ProfileController::class, 'edit'])->name('ressourcerie.profile.edit');
        Route::patch('/profile', [App\Http\Controllers\Ressourcerie\ProfileController::class, 'update'])->name('ressourcerie.profile.update');
    });

    // Routes pour les admins
    Route::prefix('admin')->middleware('can:access-admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', App\Http\Controllers\Admin\UserController::class)->middleware('can:manage-users');
        Route::resource('products', App\Http\Controllers\Admin\ProductController::class)->middleware('can:manage-products');
        Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->middleware('can:manage-categories');
        Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->middleware('can:manage-orders');
        Route::resource('ressourceries', App\Http\Controllers\Admin\RessourcerieController::class)->middleware('can:manage-users');
    });

    // Routes de gestion des rôles (accessibles uniquement par l'admin)
    Route::middleware('can:access-admin')->group(function () {
        Route::post('/role/client', [RoleController::class, 'switchToClient'])->name('role.client');
        Route::post('/role/ressourcerie', [RoleController::class, 'switchToRessourcerie'])->name('role.ressourcerie');
        Route::post('/role/admin', [RoleController::class, 'switchToAdmin'])->name('role.admin');
    });
});

// Route pour la page d'erreur 403 (Forbidden)
Route::get('/forbidden', function () {
    return Inertia::render('Error/Forbidden');
})->name('forbidden');

require __DIR__.'/auth.php';
