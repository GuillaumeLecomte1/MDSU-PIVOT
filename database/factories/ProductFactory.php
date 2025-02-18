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
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraphs(2, true),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'condition' => $this->faker->randomElement(['Neuf', 'Très bon état', 'Bon état', 'État moyen', 'À rénover']),
            'dimensions' => $this->faker->numberBetween(10, 100).'x'.$this->faker->numberBetween(10, 100).'x'.$this->faker->numberBetween(10, 100).' cm',
            'color' => $this->faker->colorName(),
            'brand' => $this->faker->company(),
            'stock' => $this->faker->numberBetween(0, 10),
            'is_available' => $this->faker->boolean(80),
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
