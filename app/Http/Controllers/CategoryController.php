<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Ressourcerie;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['categories', 'ressourcerie'])
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->categories, function ($query, $categories) {
                return $query->whereHas('categories', function ($q) use ($categories) {
                    $q->whereIn('categories.id', $categories);
                });
            })
            ->when($request->min_price, function ($query, $min_price) {
                return $query->where('price', '>=', $min_price);
            })
            ->when($request->max_price, function ($query, $max_price) {
                return $query->where('price', '<=', $max_price);
            })
            ->when($request->city, function ($query, $city) {
                return $query->whereHas('ressourcerie', function ($q) use ($city) {
                    $q->where('city', $city);
                });
            })
            ->when($request->quantity, function ($query, $quantity) {
                if ($quantity === 'available') {
                    return $query->where('quantity', '>', 0);
                } elseif ($quantity === 'out_of_stock') {
                    return $query->where('quantity', '=', 0);
                }

                return $query; // 'all' case, no filtering
            })
            ->when($request->sort, function ($query, $sort) {
                return match ($sort) {
                    'price_asc' => $query->orderBy('price', 'asc'),
                    'price_desc' => $query->orderBy('price', 'desc'),
                    'recent' => $query->orderBy('created_at', 'desc'),
                    default => $query->orderBy('created_at', 'desc')
                };
            });

        $products = $query->paginate(12)->appends($request->query());

        /** @var ?User $user */
        $user = Auth::user();

        // Ajouter l'état des favoris pour chaque produit
        foreach ($products as $product) {
            $images = is_string($product->images) ? json_decode($product->images) : [];
            $product->setAttribute('images', $images);
            $product->setAttribute('main_image', !empty($images) ? '/storage/products/'.$images[0] : null);
            $product->setAttribute('isFavorite', $user !== null && $product->isFavoritedBy($user));
        }

        return Inertia::render('Categories/Index', [
            'categories' => Category::all(),
            'products' => $products,
            'ressourceries' => Ressourcerie::select('id', 'name', 'city')->get(),
            'filters' => $request->only(['search', 'categories', 'min_price', 'max_price', 'city', 'quantity', 'sort']),
        ]);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = Product::with(['categories', 'ressourcerie'])
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        /** @var ?User $user */
        $user = Auth::user();

        // Ajouter l'état des favoris pour chaque produit
        foreach ($products as $product) {
            $images = is_string($product->images) ? json_decode($product->images) : [];
            $product->setAttribute('images', $images);
            $product->setAttribute('main_image', !empty($images) ? '/storage/products/'.$images[0] : null);
            $product->setAttribute('isFavorite', $user !== null && $product->isFavoritedBy($user));
        }

        return Inertia::render('Categories/Show', [
            'products' => $products,
            'categories' => Category::withCount('products')->get(),
            'ressourceries' => Ressourcerie::all(),
            'category' => $category,
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/categories'), $imageName);
            $validatedData['image'] = 'images/categories/' . $imageName;
        }

        $category = Category::create($validatedData);

        return response()->json($category, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        if ($request->hasFile('image') && $category->getAttribute('image')) {
            // Supprimer l'ancienne image si elle existe
            if (file_exists(public_path($category->getAttribute('image')))) {
                unlink(public_path($category->getAttribute('image')));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/categories'), $imageName);
            $validatedData['image'] = 'images/categories/' . $imageName;
        }

        $category->update($validatedData);

        return response()->json($category);
    }
}
