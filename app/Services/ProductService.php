<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function getProductsForCategory(string $slug): LengthAwarePaginator
    {
        return Product::with(['categories', 'ressourcerie'])
            ->whereHas('categories', function ($query) use ($slug) {
                $query->where('categories.slug', $slug);
            })
            ->when(request('min_price'), function ($query) {
                $query->where('price', '>=', request('min_price'));
            })
            ->when(request('max_price'), function ($query) {
                $query->where('price', '<=', request('max_price'));
            })
            ->when(request('city'), function ($query) {
                $query->whereHas('ressourcerie', function ($q) {
                    $q->where('city', request('city'));
                });
            })
            ->when(request('sort'), function ($query) {
                match (request('sort')) {
                    'price_asc' => $query->orderBy('price', 'asc'),
                    'price_desc' => $query->orderBy('price', 'desc'),
                    'newest' => $query->orderBy('created_at', 'desc'),
                    default => $query->orderBy('created_at', 'desc')
                };
            })
            ->paginate(12);
    }

    public function getProductDetails(Product $product): array
    {
        $images = $product->images ?
            (is_string($product->images) ? json_decode($product->images) : $product->images) :
            null;

        $mainImage = $images && is_array($images) && ! empty($images) ?
            asset('storage/'.$images[0]) :
            asset('images/no-image.jpg');

        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => number_format($product->price, 2, ',', ' ').' €',
            'mainImage' => $mainImage,
            'categoryName' => $product->categories->first()?->name ?? 'Non catégorisé',
            'ressourcerieInfo' => $product->ressourcerie ?
                "{$product->ressourcerie->name} ({$product->ressourcerie->city})" :
                'Non assigné',
            'isAvailable' => $product->is_available && $product->stock > 0,
            'stockLabel' => $product->is_available && $product->stock > 0 ? 'En stock' : 'Indisponible',
            'stockClass' => $product->is_available && $product->stock > 0 ? 'text-green-600' : 'text-red-600',
        ];
    }
}
