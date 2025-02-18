<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ressourcerie;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductImageService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly ProductImageService $imageService
    ) {}

    public function index(Request $request): Response
    {
        $products = $request->user()->ressourcerie->products()
            ->with(['categories', 'images'])
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
        $product->is_available = true;
        $product->save();

        if (isset($validated['categories'])) {
            $product->categories()->sync($validated['categories']);
        }

        if ($request->hasFile('images')) {
            $this->imageService->addImages($product, $request->file('images'));
        }

        return redirect()->route('ressourcerie.products.index')
            ->with('success', 'Produit créé avec succès.');
    }

    public function edit(Product $product): Response
    {
        $this->authorize('update', $product);

        return Inertia::render('Ressourcerie/Products/Edit', [
            'product' => $product->load(['categories', 'images']),
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
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id',
            'reorder_images' => 'nullable|array',
            'reorder_images.*' => 'exists:product_images,id',
        ]);

        $product->update($validated);

        if (isset($validated['categories'])) {
            $product->categories()->sync($validated['categories']);
        }

        // Gérer la suppression des images
        if (!empty($validated['delete_images'])) {
            $this->imageService->deleteImages($validated['delete_images']);
        }

        // Gérer l'ajout de nouvelles images
        if ($request->hasFile('images')) {
            $startOrder = $product->images()->max('order') + 1;
            $this->imageService->addImages($product, $request->file('images'), $startOrder);
        }

        // Gérer la réorganisation des images
        if (!empty($validated['reorder_images'])) {
            $this->imageService->reorderImages($product, $validated['reorder_images']);
        }

        return redirect()->route('ressourcerie.products.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Product $product): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('delete', $product);

        // Supprimer toutes les images avant de supprimer le produit
        $this->imageService->deleteAllImages($product);
        $product->delete();

        return redirect()->route('ressourcerie.products.index')
            ->with('success', 'Produit supprimé avec succès.');
    }
}
