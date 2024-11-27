<?php

use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/categorie', function () {
//     return view('categorie');
// })->middleware(['auth', 'verified'])->name('categorie');
// Route::get('/categorie', [CategorieController::class, 'showCategorie'])->name('categorie');
Route::get('/categorie', [CategoryController::class, 'index'])->name('categorie');
Route::get('/categorie/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/history', function () {
    return view('history');
})->middleware(['auth', 'verified'])->name('history');

Route::get('/ressourcerie', function () {
    return view('ressourcerie');
})->middleware(['auth', 'verified'])->name('ressourcerie');

// Route pour afficher la fiche du produit
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Remplacer les routes Stripe existantes par celles-ci
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/payment/create-checkout-session', [PaymentController::class, 'createCheckoutSession'])
        ->name('payment.create-checkout-session');
        
    // Route pour afficher la page de paiement
    Route::get('/stripe', [PaymentController::class, 'showPaymentPage'])
    ->name('stripe');

    Route::get('/payment/success', [PaymentController::class, 'success'])
        ->name('payment.success');

    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])
        ->name('payment.cancel');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';

