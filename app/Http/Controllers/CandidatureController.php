<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\Offre;
use App\Http\Requests\CandidatureRequest;
use Illuminate\Http\Request;

class CandidatureController extends Controller
{
    // Candidate postule à une offre
    public function store(CandidatureRequest $request)
    {
        $offre = Offre::findOrFail($request->offre_id);

        // Vérifier si le candidat a déjà postulé
        $exists = Candidature::where('candidat_id', auth()->id())
            ->where('offre_id', $offre->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Vous avez déjà postulé à cette offre.'
            ], 409);
        }

        $candidature = Candidature::create([
            'candidat_id' => auth()->id(),
            'offre_id' => $offre->id,
            'statut' => 'en_attente'
        ]);

        return response()->json([
            'message' => 'Candidature envoyée avec succès.',
            'candidature' => $candidature
        ], 201);
    }

    // Candidate : voir ses candidatures
    public function mesCandidatures()
    {
        return response()->json(
            Candidature::with('offre')
                ->where('candidat_id', auth()->id())
                ->get()
        );
    }

    // Entreprise : voir les candidatures reçues
    public function candidaturesRecues()
    {
        return response()->json(
            Candidature::with(['candidat', 'offre'])
                ->whereHas('offre.entreprise', function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->get()
        );
    }

    // Entreprise/Admin : modifier le statut
    public function updateStatut(Request $request, Candidature $candidature)
    {
        $request->validate([
            'statut' => 'required|in:acceptée,refusée'
        ]);

        if (
            auth()->user()->role !== 'admin' &&
            $candidature->offre->entreprise->user_id != auth()->id()
        ) {
            return response()->json([
                'message' => 'Accès refusé.'
            ], 403);
        }

        $candidature->update([
            'statut' => $request->statut
        ]);

        return response()->json([
            'message' => 'Statut mis à jour.',
            'candidature' => $candidature
        ]);
    }
}