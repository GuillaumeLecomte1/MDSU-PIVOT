<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $availableCategories = array_diff_key(static::$categories, static::$usedCategories);
        
        if (empty($availableCategories)) {
            // Si toutes les catégories ont été utilisées, réinitialiser
            static::$usedCategories = [];
            $availableCategories = static::$categories;
        }

        $name = array_rand($availableCategories);
        $icon = static::$categories[$name];
        static::$usedCategories[$name] = true;

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'icon' => "icon-{$icon}",
            'image_url' => fake()->imageUrl(640, 480, $name),
            'is_active' => true,
            'display_order' => fake()->numberBetween(0, 10),
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
