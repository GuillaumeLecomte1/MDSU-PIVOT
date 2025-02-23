<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::with(['categories', 'ressourcerie', 'images']);

        if (request('category')) {
            $query->whereHas('categories', function ($q) {
                $q->where('slug', request('category'));
            });
        }

        if (request('price_min')) {
            $query->where('price', '>=', request('price_min'));
        }

        if (request('price_max')) {
            $query->where('price', '<=', request('price_max'));
        }

        if (request('city')) {
            $query->whereHas('ressourcerie', function ($q) {
                $q->where('city', request('city'));
            });
        }

        $products = $query->paginate(12);

        return Inertia::render('Products/Index', [
            'products' => ProductResource::collection($products),
            'filters' => request()->all(['category', 'price_min', 'price_max', 'city']),
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): Response
    {
        $product->load(['images', 'categories', 'ressourcerie']);
        
        $similarProducts = Product::query()
            ->where('ressourcerie_id', $product->ressourcerie_id)
            ->where('id', '!=', $product->id)
            ->with(['images', 'categories'])
            ->take(4)
            ->get();

        // Créer un tableau personnalisé pour le produit principal
        $productData = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'description' => $product->description,
            'dimensions' => $product->dimensions,
            'condition' => $product->condition,
            'images' => $product->images->map(fn($image) => [
                'path' => $image->path,
                'url' => $image->url,
            ]),
            'categories' => $product->categories->map(fn($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ]),
            'ressourcerie' => [
                'id' => $product->ressourcerie->id,
                'name' => $product->ressourcerie->name,
            ],
            'is_available' => $product->is_available,
            'stock' => $product->stock,
            'isFavorite' => Auth::check() ? $product->isFavoritedBy(Auth::user()) : false,
        ];

        return Inertia::render('Products/Show', [
            'product' => $productData,
            'similarProducts' => ProductResource::collection($similarProducts)->resolve(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = Product::create($validatedData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('images/products'), $imageName);
                $product->images()->create([
                    'path' => 'images/products/'.$imageName,
                ]);
            }
        }

        return response()->json($product->load('images'), 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product->update($validatedData);

        if ($request->hasFile('images')) {
            // Supprimer les anciennes images
            foreach ($product->images as $image) {
                if (file_exists(public_path($image->path))) {
                    unlink(public_path($image->path));
                }
                $image->delete();
            }

            // Ajouter les nouvelles images
            foreach ($request->file('images') as $image) {
                $imageName = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('images/products'), $imageName);
                $product->images()->create([
                    'path' => 'images/products/'.$imageName,
                ]);
            }
        }

        return response()->json($product->load('images'));
    }
}
