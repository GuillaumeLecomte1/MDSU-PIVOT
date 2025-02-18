<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Ressourcerie;
use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure storage directory exists
        if (!Storage::disk('public')->exists('products')) {
            Storage::disk('public')->makeDirectory('products');
        }

        // Create admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create client user
        User::factory()->create([
            'name' => 'Client',
            'email' => 'client@example.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);

        // Create ressourcerie user
        $ressourcerie = User::factory()->create([
            'name' => 'Ressourcerie',
            'email' => 'ressourcerie@example.com',
            'password' => bcrypt('password'),
            'role' => 'ressourcerie',
        ]);

        // Create ressourcerie profile
        $ressourcerieProfile = Ressourcerie::create([
            'user_id' => $ressourcerie->id,
            'name' => 'Ressourcerie Test',
            'slug' => 'ressourcerie-test',
            'email' => 'ressourcerie@example.com',
            'siret' => '12345678901234',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'postal_code' => '12345',
            'phone' => '0123456789',
            'description' => 'A test ressourcerie',
        ]);

        // Create 5 random clients
        User::factory(5)->create([
            'role' => 'client',
        ]);

        // Create 10 random categories
        Category::factory(10)->create();

        // Create image service
        $imageService = new ImageService();

        // Create 10 products for each ressourcerie
        $categories = Category::all();
        $ressourceries = Ressourcerie::all();

        foreach ($ressourceries as $ressourcerie) {
            Product::factory(10)->create([
                'ressourcerie_id' => $ressourcerie->id,
                'category_id' => $categories->random()->id,
            ])->each(function ($product) use ($imageService) {
                // Create 3 images for each product
                for ($i = 1; $i <= 3; $i++) {
                    $imagePath = $imageService->createTestImage(800, 600, "Product {$product->id} - Image {$i}");
                    
                    // Generate thumbnails
                    $thumbnails = $imageService->generateThumbnails($imagePath);
                    
                    // Create product image record with JSON encoded thumbnails
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $imagePath,
                        'thumbnails' => json_encode($thumbnails, JSON_FORCE_OBJECT),
                        'order' => $i,
                    ]);
                }
            });
        }
    }
}
