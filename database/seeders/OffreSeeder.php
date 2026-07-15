<?php

namespace Database\Seeders;

use App\Models\Offre;
use Illuminate\Database\Seeder;

class OffreSeeder extends Seeder
{
    public function run(): void
    {
        Offre::factory(30)->create();
    }
}