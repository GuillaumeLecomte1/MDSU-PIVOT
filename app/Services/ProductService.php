<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function getProductsForCategory(string $slug): LengthAwarePaginator
    {
        return Product::with(['categories', 'ressourcerie'])
            ->whereHas('categories', function ($query) use ($slug)
            {
                $query->where('categories.slug', $slug);
            })
            ->when(request('min_price'), function ($query)
            {
                $query->where('price', '>=', (float) request('min_price'));
            })
            ->when(request('max_price'), function ($query)
            {
                $query->where('price', '<=', (float) request('max_price'));
            })
            ->when(request('city'), function ($query)
            {
                $query->whereHas('ressourcerie', function ($q)
                {
                    $q->where('city', request('city'));
                });
            })
            ->when(request('sort'), function ($query)
            {
                return match (request('sort')) {
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
        $imagesArray = [];
        $images = $product->getAttribute('images');
        if ($images !== null) {
            $imagesArray = is_string($images) ? json_decode($images, true) : $images;
        }

        $mainImage = ! empty($imagesArray) ?
            Storage::url('products/'.reset($imagesArray)) :
            asset('images/no-image.jpg');

        $price = (float) $product->getAttribute('price');
        $category = $product->categories->first();
        $ressourcerie = $product->ressourcerie;

        return [
            'id' => $product->id,
            'name' => $product->getAttribute('name'),
            'description' => $product->getAttribute('description'),
            'price' => number_format($price, 2, ',', ' ').' €',
            'mainImage' => $mainImage,
            'categoryName' => $category ? $category->getAttribute('name') : 'Non catégorisé',
            'ressourcerieInfo' => $ressourcerie ? sprintf(
                '%s (%s)',
                $ressourcerie->getAttribute('name'),
                $ressourcerie->getAttribute('city')
            ) : 'Non assigné',
            'isAvailable' => (bool) ($product->getAttribute('is_available') && $product->getAttribute('stock') > 0),
            'stockLabel' => $product->getAttribute('is_available') && $product->getAttribute('stock') > 0 ? 'En stock' : 'Indisponible',
            'stockClass' => $product->getAttribute('is_available') && $product->getAttribute('stock') > 0 ? 'text-green-600' : 'text-red-600',
        ];
    }
}
