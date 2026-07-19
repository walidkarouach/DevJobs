<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Crée un pool de candidats utilisé ensuite par CandidatureSeeder.
     */
    public function run(): void
    {
        User::factory(15)->create([
            'role' => 'candidate',
        ]);
    }
}
