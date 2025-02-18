<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    /**
     * Ajoute une ou plusieurs images à un produit
     */
    public function addImages(Product $product, array $images, int $startOrder = 0): void
    {
        foreach ($images as $index => $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'path' => $path,
                    'order' => $startOrder + $index,
                ]);
            }
        }
    }

    /**
     * Supprime une ou plusieurs images
     */
    public function deleteImages(array $imageIds): void
    {
        $images = ProductImage::whereIn('id', $imageIds)->get();
        
        foreach ($images as $image) {
            // Supprimer le fichier physique
            Storage::disk('public')->delete($image->path);
            // Supprimer l'enregistrement
            $image->delete();
        }
    }

    /**
     * Réorganise l'ordre des images
     */
    public function reorderImages(Product $product, array $imageIds): void
    {
        foreach ($imageIds as $order => $imageId) {
            $product->images()->where('id', $imageId)->update(['order' => $order]);
        }
    }

    /**
     * Supprime toutes les images d'un produit
     */
    public function deleteAllImages(Product $product): void
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $product->images()->delete();
    }
} 