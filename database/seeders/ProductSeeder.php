<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Ressourcerie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $ressourceries = Ressourcerie::all();

        for ($i = 1; $i <= 50; $i++) {
            $product = Product::create([
                'name' => 'Product ' . $i,
                'slug' => Str::slug('Product ' . $i),
                'description' => 'Description for product ' . $i,
                'price' => rand(10, 1000),
                'dimensions' => rand(10, 100) . 'x' . rand(10, 100) . 'x' . rand(10, 100),
                'images' => json_encode([
                    "product-$i-1.jpg",
                    "product-$i-2.jpg",
                    "product-$i-3.jpg"
                ]),
                'ressourcerie_id' => $ressourceries->random()->id,
            ]);

            $product->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}
