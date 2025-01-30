<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Meubles',
            'Électroménager',
            'Décoration',
            'Vaisselle',
            'Vêtements',
            'Livres',
            'Jouets',
            'Électronique',
            'Sports & Loisirs',
            'Bricolage',
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
                'description' => "Collection de $categoryName de seconde main",
            ]);
        }
    }
}
