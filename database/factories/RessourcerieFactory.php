<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ressourcerie>
 */
class RessourcerieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->numerify('#####'),
            'phone' => fake()->numerify('0#########'),
            'email' => fake()->unique()->companyEmail(),
            'website_url' => fake()->url(),
            'logo_url' => null,
            'opening_hours' => json_encode([
                'monday' => ['09:00-12:00', '14:00-18:00'],
                'tuesday' => ['09:00-12:00', '14:00-18:00'],
                'wednesday' => ['09:00-12:00', '14:00-18:00'],
                'thursday' => ['09:00-12:00', '14:00-18:00'],
                'friday' => ['09:00-12:00', '14:00-18:00'],
                'saturday' => ['09:00-12:00'],
                'sunday' => [],
            ]),
            'latitude' => fake()->latitude(41.0, 51.0),
            'longitude' => fake()->longitude(-5.0, 9.0),
            'is_active' => true,
            'siret' => fake()->numerify('##############'),
        ];
    }
}
