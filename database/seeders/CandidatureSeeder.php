<?php

namespace Database\Seeders;

use App\Models\Candidature;
use App\Models\User;
use App\Models\Offre;
use Illuminate\Database\Seeder;

class CandidatureSeeder extends Seeder
{
    public function run(): void
    {
        $candidats = User::where('role', 'candidate')->get();
        $offres = Offre::all();

        // Génère des paires (candidat, offre) uniques pour respecter
        // la contrainte unique (user_id, offre_id) de la table.
        $paires = $candidats->crossJoin($offres)->shuffle()->take(50);

        foreach ($paires as [$candidat, $offre]) {
            Candidature::firstOrCreate(
                [
                    'user_id' => $candidat->id,
                    'offre_id' => $offre->id,
                ],
                [
                    'statut' => fake()->randomElement(['en_attente', 'acceptee', 'refusee']),
                ]
            );
        }
    }
}
