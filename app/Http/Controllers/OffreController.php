<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\Entreprise;
use App\Http\Requests\OffreRequest;

class OffreController extends Controller
{
    // Liste des offres
    public function index()
    {
        return response()->json(
            Offre::with('entreprise')->get()
        );
    }

    // Une offre
    public function show(Offre $offre)
    {
        return response()->json(
            $offre->load('entreprise')
        );
    }

    // Ajouter
    public function store(OffreRequest $request)
    {
        $entreprise = Entreprise::where('user_id', auth()->id())->first();

        if (!$entreprise) {
            return response()->json([
                'message' => 'Vous devez créer votre profil entreprise.'
            ], 404);
        }

        $offre = Offre::create([
            'entreprise_id' => $entreprise->id,
            'titre' => $request->titre,
            'description' => $request->description,
            'type_contrat' => $request->type_contrat,
        ]);

        return response()->json([
            'message' => 'Offre créée avec succès.',
            'offre' => $offre
        ], 201);
    }

    // Modifier
    public function update(OffreRequest $request, Offre $offre)
    {
        if (
            auth()->user()->role !== 'admin' &&
            $offre->entreprise->user_id != auth()->id()
        ) {
            return response()->json([
                'message' => 'Accès refusé.'
            ], 403);
        }

        $offre->update($request->validated());

        return response()->json([
            'message' => 'Offre modifiée.',
            'offre' => $offre
        ]);
    }

    // Supprimer
    public function destroy(Offre $offre)
    {
        if (
            auth()->user()->role !== 'admin' &&
            $offre->entreprise->user_id != auth()->id()
        ) {
            return response()->json([
                'message' => 'Accès refusé.'
            ], 403);
        }

        $offre->delete();

        return response()->json([
            'message' => 'Offre supprimée.'
        ]);
    }
}