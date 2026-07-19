<?php

namespace Database\Factories;

use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Entreprise>
 */
class EntrepriseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'role' => 'entreprise'
            ]),

            'nom' => fake()->company(),
            'secteur' => fake()->randomElement([
                'Informatique',
                'Finance',
                'Marketing',
                'Santé',
                'Industrie'
            ]),
            'description' => fake()->paragraph(),
            'logo' => 'logo.png',
        ];
    }
}
