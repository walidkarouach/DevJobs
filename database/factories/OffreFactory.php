<?php

namespace Database\Factories;

use App\Models\Entreprise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offre>
 */
class OffreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'entreprise_id' => Entreprise::factory(),
            'titre' => fake()->jobTitle(),
            'description' => fake()->paragraph(4),
            'type_contrat' => fake()->randomElement([
                'CDI',
                'CDD',
                'Stage',
                'Freelance'
            ])
        ];
    }
}
