<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Ressourcerie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<model-property<\App\Models\Product>, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);
        $conditions = ['Neuf', 'Très bon état', 'Bon état', 'État moyen', 'À rénover'];
        $colors = ['Rouge', 'Bleu', 'Vert', 'Jaune', 'Noir', 'Blanc', 'Gris', 'Marron'];

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraphs(2, true),
            'price' => fake()->randomFloat(2, 5, 1000),
            'condition' => fake()->randomElement($conditions),
            'dimensions' => fake()->numerify('##x##x## cm'),
            'color' => fake()->randomElement($colors),
            'brand' => fake()->company(),
            'stock' => fake()->numberBetween(0, 10),
            'is_available' => fake()->boolean(80),
            'images' => json_encode([
                fake()->imageUrl(640, 480, 'product'),
                fake()->imageUrl(640, 480, 'product'),
            ]),
            'ressourcerie_id' => Ressourcerie::factory(),
        ];
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
            'is_available' => false,
        ]);
    }

    /**
     * Indicate that the product is in stock.
     */
    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(1, 10),
            'is_available' => true,
        ]);
    }
}
