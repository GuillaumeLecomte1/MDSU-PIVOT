<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Ressourcerie\DashboardController as RessourcerieDashboardController;
use App\Http\Controllers\RessourcerieController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

// Routes publiques (login, register)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::get('register', function () {
        return Inertia::render('Auth/Register');
    })->name('register');
});

// Routes publiques sans authentification
Route::get('/notre-histoire', function () {
    return Inertia::render('About/Index');
})->name('about');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/ressourceries', [RessourcerieController::class, 'index'])->name('ressourceries.index');
Route::get('/ressourceries/{ressourcerie}', [RessourcerieController::class, 'show'])->name('ressourceries.show');

// Route racine avec redirection vers login si non connecté
Route::get('/', function () {
    $latestProducts = Product::with(['categories', 'ressourcerie', 'favorites', 'images'])
        ->latest()
        ->take(4)
        ->get();

    $popularProducts = Product::with(['categories', 'ressourcerie', 'favorites', 'images'])
        ->withCount('favorites')
        ->orderByDesc('favorites_count')
        ->take(4)
        ->get();

    // Ajouter l'état des favoris pour chaque produit
    $processProducts = function ($products) {
        foreach ($products as $product) {
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

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Client routes
    Route::middleware('can:access-client')->prefix('client')->name('client.')->group(function () {
        Route::get('/orders', [App\Http\Controllers\Client\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\Client\OrderController::class, 'show'])->name('orders.show');
    });

    // Favorites routes
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/products/{product}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    });

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/cart/{product}', [CartController::class, 'updateQuantity'])->name('cart.update');

    // Routes pour les commandes
    Route::post('/orders/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::get('/orders/success', [OrderController::class, 'success'])->name('orders.success');
    Route::get('/orders/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// Routes spécifiques aux rôles
Route::middleware(['auth', 'verified'])->group(function () {
    // Routes pour les ressourceries (gestion des produits)
    Route::prefix('ressourcerie')
        ->middleware(['auth', 'verified', 'can:access-ressourcerie'])
        ->name('ressourcerie.')
        ->group(function () {
            Route::get('/dashboard', [RessourcerieDashboardController::class, 'index'])->name('dashboard');
            Route::resource('products', App\Http\Controllers\Ressourcerie\ProductController::class)->names('products');
            Route::get('/profile', [App\Http\Controllers\Ressourcerie\ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [App\Http\Controllers\Ressourcerie\ProfileController::class, 'update'])->name('profile.update');
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

// Admin Documentation Routes
Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/documentation', [App\Http\Controllers\Admin\DocumentationController::class, 'index'])->name('documentation.index');
    Route::get('/documentation/{filename}', [App\Http\Controllers\Admin\DocumentationController::class, 'show'])->name('documentation.show');
});

require __DIR__.'/auth.php';
