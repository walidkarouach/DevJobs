<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Http\Requests\EntrepriseRequest;
use Illuminate\Http\Request;

class EntrepriseController extends Controller
{
    // Afficher toutes les entreprises
    public function index()
    {
        return response()->json(Entreprise::with('user')->get());
    }

    // Afficher une entreprise
    public function show($id)
    {
        return response()->json(
            Entreprise::with('user')->findOrFail($id)
        );
    }

    // Créer un profil entreprise
    public function store(EntrepriseRequest $request)
    {
        $entreprise = Entreprise::create([
            'user_id' => auth()->id(),
            'nom' => $request->nom,
            'secteur' => $request->secteur,
            'description' => $request->description,
            'logo' => $request->logo,
        ]);

        return response()->json([
            'message' => 'Entreprise créée avec succès.',
            'entreprise' => $entreprise
        ], 201);
    }

    // Modifier
    public function update(EntrepriseRequest $request, Entreprise $entreprise)
    {
        if (
            auth()->user()->role !== 'admin' &&
            auth()->id() !== $entreprise->user_id
        ) {
            return response()->json([
                'message' => 'Accès refusé.'
            ], 403);
        }

        $entreprise->update($request->validated());

        return response()->json([
            'message' => 'Entreprise modifiée.',
            'entreprise' => $entreprise
        ]);
    }

    // Supprimer
    public function destroy(Entreprise $entreprise)
    {
        if (
            auth()->user()->role !== 'admin' &&
            auth()->id() !== $entreprise->user_id
        ) {
            return response()->json([
                'message' => 'Accès refusé.'
            ], 403);
        }

        $entreprise->delete();

        return response()->json([
            'message' => 'Entreprise supprimée.'
        ]);
    }
}