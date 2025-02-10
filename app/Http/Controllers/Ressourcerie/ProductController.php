<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ressourcerie;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'ressourcerie', 'verified']);
    }

    public function index(Request $request): Response
    {
        $products = $request->user()->ressourcerie->products()
            ->with(['categories'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Ressourcerie/Products/Index', [
            'products' => $products,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Ressourcerie/Products/Create', [
            'categories' => Category::all(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:market__categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = new Product($validated);
        $product->slug = Str::slug($validated['name']);
        $product->ressourcerie_id = $request->user()->ressourcerie->id;
        $product->save();

        if (isset($validated['categories'])) {
            $product->categories()->sync($validated['categories']);
        }

        // Gérer le téléchargement des images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('ressourcerie.products.index')
            ->with('success', 'Produit créé avec succès.');
    }

    public function edit(Product $product): Response
    {
        $this->authorize('update', $product);

        return Inertia::render('Ressourcerie/Products/Edit', [
            'product' => $product->load('categories'),
            'categories' => Category::all(),
        ]);
    }

    public function update(Request $request, Product $product): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:market__categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product->update($validated);

        if (isset($validated['categories'])) {
            $product->categories()->sync($validated['categories']);
        }

        // Gérer le téléchargement des nouvelles images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('ressourcerie.products.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Product $product): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('delete', $product);

        $product->delete();

        return redirect()->route('ressourcerie.products.index')
            ->with('success', 'Produit supprimé avec succès.');
    }
} 