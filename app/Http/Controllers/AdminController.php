<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Entreprise;
use App\Models\Offre;
use App\Models\Competence;
use App\Models\Candidature;

class AdminController extends Controller
{
    // Statistiques globales de la plateforme (admin uniquement)
    public function statistiques()
    {
        return response()->json([
            'utilisateurs' => User::count(),
            'entreprises' => Entreprise::count(),
            'offres' => Offre::count(),
            'competences' => Competence::count(),
            'candidatures' => [
                'total' => Candidature::count(),
                'en_attente' => Candidature::where('statut', 'en_attente')->count(),
                'acceptees' => Candidature::where('statut', 'acceptee')->count(),
                'refusees' => Candidature::where('statut', 'refusee')->count(),
            ],
        ]);
    }
}
