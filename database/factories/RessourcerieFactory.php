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
     * @return array<model-property<\App\Models\Ressourcerie>, mixed>
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
            'postal_code' => substr(fake()->postcode(), 0, 5),
            'phone' => fake()->numerify('##-##-##-##-##'),
            'email' => fake()->unique()->safeEmail(),
            'website_url' => fake()->url(),
            'logo_url' => null,
            'opening_hours' => json_encode([
                'monday' => '9:00-18:00',
                'tuesday' => '9:00-18:00',
                'wednesday' => '9:00-18:00',
                'thursday' => '9:00-18:00',
                'friday' => '9:00-18:00',
                'saturday' => '10:00-17:00',
                'sunday' => 'closed'
            ]),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'is_active' => true,
            'siret' => fake()->numerify('##############'),
        ];
    }
}
