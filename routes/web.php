<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RessourcerieController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

// Route racine nécessitant une authentification
Route::middleware(['auth'])->group(function () {
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
        $processProducts = function($products) {
            foreach ($products as $product) {
                $images = json_decode($product->images) ?? [];
                $product->images = $images;
                $product->main_image = !empty($images) ? '/storage/products/' . $images[0] : null;
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
    })->name('home');
});

// Redirection par défaut pour les utilisateurs non connectés
Route::get('/', function () {
    return redirect()->route('login');
})->name('root');

// Dashboard route
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard/Index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Categories routes (public)
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Products routes (public)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::middleware('auth')->group(function () {
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
});

Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Admin/Index');
    })->name('dashboard');
    
    // Routes pour les produits
    Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('products.store');
    
    // Routes pour les catégories
    Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    
    // Routes pour les ressourceries
    Route::get('/ressourceries', [App\Http\Controllers\Admin\RessourcerieController::class, 'index'])->name('ressourceries.index');
    Route::get('/ressourceries/create', [App\Http\Controllers\Admin\RessourcerieController::class, 'create'])->name('ressourceries.create');
    Route::post('/ressourceries', [App\Http\Controllers\Admin\RessourcerieController::class, 'store'])->name('ressourceries.store');
    
    // Routes pour les utilisateurs
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    
    // Routes pour les commandes
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
});

require __DIR__.'/auth.php';
