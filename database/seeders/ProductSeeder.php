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
        $conditions = ['neuf', 'très bon état', 'bon état', 'état moyen'];
        $colors = ['rouge', 'bleu', 'vert', 'noir', 'blanc', 'gris', 'marron'];
        $brands = ['IKEA', 'Conforama', 'BUT', 'Maisons du Monde', 'Emmaüs', 'Vintage'];

        $categoryIds = Category::pluck('id')->toArray();
        $ressourcerieIds = Ressourcerie::pluck('id')->toArray();

        for ($i = 1; $i <= 30; $i++) {
            $name = "Produit $i";
            
            Product::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "Description détaillée du produit $i",
                'price' => rand(5, 500) + (rand(0, 99) / 100),
                'condition' => $conditions[array_rand($conditions)],
                'dimensions' => rand(20, 200) . 'x' . rand(20, 200) . 'x' . rand(20, 200) . ' cm',
                'color' => $colors[array_rand($colors)],
                'brand' => $brands[array_rand($brands)],
                'stock' => rand(1, 10),
                'is_available' => true,
                'images' => json_encode([
                    "product-$i-1.jpg",
                    "product-$i-2.jpg"
                ]),
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'ressourcerie_id' => $ressourcerieIds[array_rand($ressourcerieIds)]
            ]);
        }
    }
}
