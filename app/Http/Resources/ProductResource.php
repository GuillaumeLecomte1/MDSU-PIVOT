<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'description' => $this->description,
            'images' => $this->images->map(fn($image) => [
                'path' => $image->path,
                'url' => $image->url,
            ]),
            'categories' => $this->categories->map(fn($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ]),
            'ressourcerie' => [
                'id' => $this->ressourcerie->id,
                'name' => $this->ressourcerie->name,
            ],
            'is_available' => $this->is_available,
            'stock' => $this->stock,
            'isFavorite' => Auth::check() ? $this->isFavoritedBy(Auth::user()) : false,
        ];
    }
} 