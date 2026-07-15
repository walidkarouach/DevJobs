<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Offre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Candidature>
 */
class CandidatureFactory extends Factory
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
                'role' => 'candidate'
            ]),

            'offre_id' => Offre::factory(),

            'statut' => fake()->randomElement([
                'en_attente',
                'acceptee',
                'refusee'
            ])
        ];
    }
}
