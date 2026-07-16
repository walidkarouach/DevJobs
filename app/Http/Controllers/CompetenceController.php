<?php

namespace App\Http\Controllers;

use App\Models\Competence;
use App\Http\Requests\CompetenceRequest;

class CompetenceController extends Controller
{
    public function index()
    {
        return Competence::all();
    }

    public function store(CompetenceRequest $request)
    {
        $competence = Competence::create($request->validated());

        return response()->json([
            'message' => 'Compétence créée avec succès.',
            'competence' => $competence
        ],201);
    }

    public function show(Competence $competence)
    {
        return $competence;
    }

    public function update(CompetenceRequest $request, Competence $competence)
    {
        $competence->update($request->validated());

        return response()->json([
            'message'=>'Compétence modifiée.',
            'competence'=>$competence
        ]);
    }

    public function destroy(Competence $competence)
    {
        $competence->delete();

        return response()->json([
            'message'=>'Compétence supprimée.'
        ]);
    }
}