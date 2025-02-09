<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Ressourcerie;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer un utilisateur admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Créer quelques utilisateurs avec différents rôles
        User::factory(5)->client()->create();
        User::factory(3)->ressourcerie()->create();

        // Créer les catégories
        Category::factory(10)->create();

        // Créer des ressourceries avec leurs produits
        Ressourcerie::factory(5)
            ->has(Product::factory()->count(10))
            ->create();

        // Créer des produits supplémentaires
        Product::factory(20)->create();
    }
}
