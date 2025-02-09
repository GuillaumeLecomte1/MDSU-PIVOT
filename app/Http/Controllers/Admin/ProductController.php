<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\Ressourcerie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['categories', 'ressourcerie'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        $ressourceries = Ressourcerie::all();

        return Inertia::render('Admin/Products/Create', [
            'categories' => $categories,
            'ressourceries' => $ressourceries,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|string',
            'dimensions' => 'nullable|string',
            'color' => 'nullable|string',
            'brand' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'ressourcerie_id' => 'required|exists:market__ressourceries,id',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:market__categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = Str::slug($request->name).'-'.uniqid().'.'.$image->extension();
                $image->storeAs('public/products', $filename);
                $images[] = $filename;
            }
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'condition' => $request->condition,
            'dimensions' => $request->dimensions,
            'color' => $request->color,
            'brand' => $request->brand,
            'stock' => $request->stock,
            'ressourcerie_id' => $request->ressourcerie_id,
            'images' => json_encode($images),
        ]);

        $product->categories()->attach($request->category_ids);

        return redirect()->route('categories.index')
            ->with('success', 'Produit créé avec succès.');
    }
}
