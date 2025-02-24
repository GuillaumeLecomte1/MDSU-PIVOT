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
        $query = $request->user()->ressourcerie->products()
            ->with(['categories', 'images']);

        // Filtre de recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre de disponibilité
        if ($request->filled('availability')) {
            $availability = $request->input('availability');
            if ($availability === 'available') {
                $query->where('is_available', true);
            } elseif ($availability === 'unavailable') {
                $query->where('is_available', false);
            }
        }

        // Tri
        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            switch ($sort) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(10)
            ->through(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'is_available' => $product->is_available,
                    'categories' => $product->categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                        ];
                    }),
                    'images' => $product->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'path' => $image->path,
                            'order' => $image->order,
                        ];
                    }),
                    'created_at' => $product->created_at,
                ];
            });

        return Inertia::render('Ressourcerie/Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search', 'availability', 'sort']),
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
