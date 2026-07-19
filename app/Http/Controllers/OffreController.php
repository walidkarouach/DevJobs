<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\Entreprise;
use App\Http\Requests\OffreRequest;
use Illuminate\Http\Request;

class OffreController extends Controller
{
    // Liste des offres
    public function index()
    {
        return response()->json(
            Offre::with('entreprise', 'competences')->get()
        );
    }

    // Une offre
    public function show(Offre $offre)
    {
        return response()->json(
            $offre->load('entreprise', 'competences')
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

        $offre->competences()->attach($request->input('competences', []));

        return response()->json([
            'message' => 'Offre créée avec succès.',
            'offre' => $offre->load('competences')
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

        $offre->update($request->safe()->except('competences'));

        if ($request->has('competences')) {
            $offre->competences()->sync($request->input('competences', []));
        }

        return response()->json([
            'message' => 'Offre modifiée.',
            'offre' => $offre->load('competences')
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

    // Recherche d'offres avec filtres combinables : titre, entreprise, compétence
    public function search(Request $request)
    {
        $query = Offre::with('entreprise', 'competences');

        if ($request->filled('titre')) {
            $query->where('titre', 'like', '%' . $request->input('titre') . '%');
        }

        if ($request->filled('entreprise')) {
            $query->whereHas('entreprise', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->input('entreprise') . '%');
            });
        }

        if ($request->filled('competence')) {
            $query->whereHas('competences', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->input('competence') . '%');
            });
        }

        return response()->json($query->get());
    }
}