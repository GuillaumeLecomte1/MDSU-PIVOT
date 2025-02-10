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
            'password' => Hash::make('admin'),
            'role' => 'admin',
        ]);

        // Créer un utilisateur client
        User::factory()->create([
            'name' => 'client',
            'email' => 'client@example.com',
            'password' => Hash::make('client'),
            'role' => 'client',
        ]);

        // Créer un utilisateur ressourcerie
        User::factory()->create([
            'name' => 'Ressourcerie',
            'email' => 'ressourcerie@example.com',
            'password' => Hash::make('ressourcerie'),
            'role' => 'ressourcerie',
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
