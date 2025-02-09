<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected static $categories = [
        'Meubles' => 'furniture',
        'Électroménager' => 'appliances',
        'Décoration' => 'decoration',
        'Vêtements' => 'clothing',
        'Livres' => 'books',
        'Jouets' => 'toys',
        'Sport & Loisirs' => 'sports',
        'Bricolage' => 'diy',
        'Jardin' => 'garden',
        'High-Tech' => 'electronics',
    ];

    protected static $usedCategories = [];

    /**
     * Define the model's default state.
     *
     * @return array<model-property<\App\Models\Category>, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'icon' => null,
            'image_url' => null,
            'is_active' => true,
            'display_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
