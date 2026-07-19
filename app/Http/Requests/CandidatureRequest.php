<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête utilisée pour postuler à une offre.
 * Aucune donnée n'est envoyée par le client : l'offre vient de la route
 * et le candidat vient de l'utilisateur authentifié.
 * L'accès est déjà filtré par le middleware role:candidate sur la route.
 */
class CandidatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
