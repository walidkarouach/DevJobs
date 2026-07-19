<?php

namespace Database\Seeders;

use App\Models\Offre;
use App\Models\Entreprise;
use App\Models\Competence;
use Illuminate\Database\Seeder;

class OffreSeeder extends Seeder
{
    public function run(): void
    {
        $entreprises = Entreprise::all();
        $competences = Competence::pluck('id');

        // recycle() réutilise les entreprises déjà créées au lieu d'en générer de nouvelles
        Offre::factory(30)
            ->recycle($entreprises)
            ->create()
            ->each(function (Offre $offre) use ($competences) {
                $offre->competences()->attach(
                    $competences->random(rand(2, 4))
                );
            });
    }
}
