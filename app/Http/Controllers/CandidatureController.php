<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\Offre;
use App\Http\Requests\CandidatureRequest;
use App\Http\Requests\StatutRequest;

class CandidatureController extends Controller
{
    // Le candidat postule à une offre
    public function store(CandidatureRequest $request, Offre $offre)
    {
        $dejaPostule = Candidature::where('user_id', auth()->id())
            ->where('offre_id', $offre->id)
            ->exists();

        if ($dejaPostule) {
            return response()->json([
                'message' => 'Vous avez déjà postulé à cette offre.'
            ], 409);
        }

        $candidature = Candidature::create([
            'user_id' => auth()->id(),
            'offre_id' => $offre->id,
            'statut' => 'en_attente',
        ]);

        return response()->json([
            'message' => 'Candidature envoyée avec succès.',
            'candidature' => $candidature,
        ], 201);
    }

    // Liste des candidatures visibles selon le rôle connecté
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'candidate') {
            // Le candidat ne voit que ses propres candidatures
            $candidatures = Candidature::with('offre.entreprise')
                ->where('user_id', $user->id)
                ->get();
        } elseif ($user->role === 'entreprise') {
            // L'entreprise ne voit que les candidatures reçues sur ses offres
            $candidatures = Candidature::with('user', 'offre')
                ->whereHas('offre.entreprise', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->get();
        } else {
            // L'admin voit tout
            $candidatures = Candidature::with('user', 'offre.entreprise')->get();
        }

        return response()->json($candidatures);
    }

    // L'entreprise (propriétaire de l'offre) ou l'admin change le statut
    public function updateStatut(StatutRequest $request, Candidature $candidature)
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && $candidature->offre->entreprise->user_id !== $user->id) {
            return response()->json([
                'message' => 'Accès refusé.'
            ], 403);
        }

        $candidature->update([
            'statut' => $request->statut,
        ]);

        return response()->json([
            'message' => 'Statut de la candidature mis à jour.',
            'candidature' => $candidature,
        ]);
    }
}
